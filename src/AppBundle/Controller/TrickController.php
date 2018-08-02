<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Entity\Trick;
use AppBundle\Form\FrontPictureType;
use AppBundle\Form\MessageType;
use AppBundle\Form\TrickType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends Controller
{

    /**
     * 
     * @Route("/", name="homepage"
     * @return type
     */
    public function indexAction()
    {
        $tricks = $em->getRepository('GBTricksBundle:Trick')->findAll();
        return $this->render('::index.html.twig', array('tricks' => $tricks));
    }

    /**
     * @Route("/trick/{trick_id}", name="trick_show")
     * @ParamConverter("trick", options={"mapping": {"trick_id": "id"}})
     */
    public function viewAction(Request $request, Trick $trick)
    {
        $form = $this->messageAction($request, $trick);

        $frontPictureForm = $this->get('form.factory')->create(FrontPictureType::class, $trick);
        $this->frontPictureAction($request, $trick);

        $messages = $em->getRepository('GBTricksBundle:Message')->findBy(array('trick' => $trick), array('date' => 'desc'));

        $frontPictureForm = $this->get('form.factory')->create(FrontPictureType::class, $trick);

        return $this->render('::Trick:view.html.twig', array(
            'trick' => $trick, 'form' => $form->createView(), 'frontPictureForm' => $frontPictureForm->createView()
        ));
    }

    /**
     * 
     * @param Request $request
     * @return type
     * @Security("has_role('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function addAction(Request $request)
    {
        $trick = new Trick();
        $form = $this->get('form.factory')->create(TrickType::class, $trick);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($trick);
                $em->flush();
                return $this->redirectToRoute('gb_tricks_trickpage', array('trick_id' => $trick->getId()));
            }
        }

        return $this->render('GBTricksBundle:Trick:save.html.twig', array('form' => $form->createView()));
    }

    /**
     * @ParamConverter("trick", options={"mapping": {"trick_id": "id"}})
     * @Security("has_role('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function modifyAction(Request $request, Trick $trick)
    {
        $form = $this->get('form.factory')->create(TrickType::class, $trick);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                return $this->redirectToRoute('gb_tricks_trickpage', array('trick_id' => $trick->getId()));
            }
        }
        return $this->render('GBTricksBundle:Trick:save.html.twig', array('form' => $form->createView()));
    }

    /**
     * @ParamConverter("trick", options={"mapping": {"trick_id": "id"}})
     * @Security("has_role('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function deleteAction(Request $request, Trick $trick)
    {
        $submittedToken = $request->query->get('token');
        if ($this->isCsrfTokenValid('delete-trick', $submittedToken)) {
            $this->getDoctrine()->getManager()->remove($trick);
            $this->getDoctrine()->getManager()->flush();
        }
        return $this->redirectToRoute('gb_tricks_homepage');
    }

    /**
     * @ParamConverter("trick", option={"mapping": {"trick_id": "id"}})
     * @Security("has_role('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function messageAction(Request $request, Trick $trick)
    {
        $message = new Message();
        $form = $this->get('form.factory')->create(MessageType::class, $message);
        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $message->setTrick($trick);
                $message->setUser($this->getUser());
                $em->persist($message);
                $em->flush();
                $form = $this->get('form.factory')->create(MessageType::class, new Message());
            }
        }
        return $form;
    }

    /**
     * @ParamConverter("trick", option={"mapping": {"trick_id": "id"}})
     * @Security("has_role('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function frontPictureAction(Request $request, Trick $trick)
    {
        $form = $this->get('form.factory')->create(FrontPictureType::class, $trick);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->flush();
            }
        }
        return $form;
    }

    /**
     * @ParamConverter("trick", options={"mapping": {"trick_id": "id"}})
     * @Security("has_role('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function resetFrontPictureAction(Request $request, Trick $trick)
    {
        $submittedToken = $request->query->get('token');
        if ($this->isCsrfTokenValid('delete-frontPicture', $submittedToken)) {
            $trick->setFrontImage(null);
            $this->getDoctrine()->getManager()->flush();
        }
        return $this->redirectToRoute('gb_tricks_trickpage', array('trick_id' => $trick->getId()));
    }

}
