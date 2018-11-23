<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Entity\Trick;
use AppBundle\Form\FrontPictureType;
use AppBundle\Form\MessageType;
use AppBundle\Form\TrickType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(EntityManagerInterface $em)
    {
        $tricks = $em->getRepository('AppBundle:Trick')->findAll();
        return $this->render('tricks/index.html.twig', array('tricks' => $tricks));
    }

    /**
     * @Route("/trick/{trick_id}", name="trick_show")
     * @ParamConverter("trick", options={"mapping": {"trick_id": "id"}})
     */
    public function viewAction(Request $request, Trick $trick, EntityManagerInterface $em)
    {
        $form = $this->messageAction($request, $trick, $em);
        $frontPictureForm = $this->frontPictureAction($request, $trick, $em);
        $messages = $em->getRepository('AppBundle:Message')->findBy(
            array('trick' => $trick), array('date' => 'desc')
        );
        return $this->render('tricks/view.html.twig', array(
            'trick' => $trick, 'form' => $form->createView(), 'messages' => $messages,
            'frontPictureForm' => $frontPictureForm->createView()
        ));
    }

    /**
     * @Route("/add", name="trick_add")
     * @Security("has_role('ROLE_USER')")
     */
    public function addAction(Request $request, EntityManagerInterface $em)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($trick);
            $em->flush();
            return $this->redirectToRoute('trick_show', array('trick_id' => $trick->getId()));
        }
        return $this->render('tricks/save.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/modify/{trick_id}", name="trick_modify")
     * @ParamConverter("trick", options={"mapping": {"trick_id": "id"}})
     * @Security("has_role('ROLE_USER')")
     */
    public function modifyAction(Request $request, Trick $trick, EntityManagerInterface $em)
    {
        $form = $this->createForm(TrickType::class, $trick)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('trick_show', array('trick_id' => $trick->getId()));
        }
        return $this->render('tricks/save.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/delete/{trick_id}", name="trick_delete")
     * @ParamConverter("trick", options={"mapping": {"trick_id": "id"}})
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteAction(Request $request, Trick $trick, EntityManagerInterface $em)
    {
        $submittedToken = $request->query->get('token');
        if ($this->isCsrfTokenValid('delete-trick', $submittedToken)) {
            $em->remove($trick);
            $em->flush();
        }
        return $this->redirectToRoute('homepage');
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function messageAction(Request $request, Trick $trick, EntityManagerInterface $em)
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message->setTrick($trick);
            $message->setUser($this->getUser());
            $em->persist($message);
            $em->flush();
            $form = $this->createForm(MessageType::class, new Message());
        }
        return $form;
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function frontPictureAction(Request $request, Trick $trick, EntityManagerInterface $em)
    {
        $form = $this->createForm(FrontPictureType::class, $trick)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!is_null($request->request->get('Reset'))) {
                $trick->setFrontImage(null);
            }
            $em->flush();
            $form = $this->createForm(FrontPictureType::class, $trick);
        }
        return $form;
    }

}
