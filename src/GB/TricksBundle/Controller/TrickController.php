<?php

namespace GB\TricksBundle\Controller;
/**
 * General components
 */
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Core\Type\FormType;

/**
 * Entities & Forms
 */
use GB\TricksBundle\Entity\Trick;
use GB\TricksBundle\Form\TrickType;
use GB\TricksBundle\Entity\Message;
use GB\TricksBundle\Form\MessageType;

/**
 * To limit some functionalities to the authentified users
 */
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class TrickController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $trickRepository = $em->getRepository('GBTricksBundle:Trick');
        
        if($request->query->get('page')){
            $page = $request->query->get('page');
        }
        else{
            $page = 1;
        }
        $tricks = $trickRepository->findBy(array(), null, 6*$page);
        
        $nextPage = $page + 1;
        
        return $this->render('GBTricksBundle:Trick:index.html.twig',
                array('tricks' => $tricks, 'nextPage' => $nextPage));
    }
    
    /**
     * @ParamConverter("trick", options={"mapping": {"trick_id": "id"}})
     */    
    public function viewAction(Request $request, Trick $trick)
    {       
        $em = $this->getDoctrine()->getManager();
        $messageRepository = $em->getRepository('GBTricksBundle:Message');
        
        $form = $this->MessageAction($request, $trick);
        
        if($request->query->get('page')){
            $page = $request->query->get('page');
        }
        else{
            $page = 1;
        }
        
        $messages = $messageRepository->findBy(array('trick' => $trick), array('date' => 'desc'), 5*$page);
        
        $nextPage = $page + 1;
        
        return $this->render('GBTricksBundle:Trick:view.html.twig',
                array('trick' => $trick, 'form' => $form->createView(), 'messages' => $messages, 'nextPage' => $nextPage));
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
        
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);

            if($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($trick);
                $em->flush();
                return $this->redirectToRoute('gb_tricks_trickpage', array('trick_id' => $trick->getId()));
            }
        }
        
        return $this->render('GBTricksBundle:Trick:save.html.twig',
                array('form' => $form->createView()));
    }
    
    /**
     * @ParamConverter("trick", options={"mapping": {"trick_id": "id"}})
     * @Security("has_role('IS_AUTHENTICATED_REMEMBERED')")
     */  
    public function modifyAction(Request $request, Trick $trick)
    {
        $form = $this->get('form.factory')->create(TrickType::class, $trick);

        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                return $this->redirectToRoute('gb_tricks_trickpage', array('trick_id' => $trick->getId()));
            }
        }
        return $this->render('GBTricksBundle:Trick:save.html.twig',
            array('form' => $form->createView()));
    }
    
    /**
     * @ParamConverter("trick", options={"mapping": {"trick_id": "id"}})
     * @Security("has_role('IS_AUTHENTICATED_REMEMBERED')")
     */  
    public function deleteAction(Trick $trick)
    {
        $this->getDoctrine()->getManager()->remove($trick);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('gb_tricks_homepage');
    }
    
    /**
     * @ParamConverter("trick", option={"mapping": {"trick_id": "id"}})
     * @Security("has_role('IS_AUTHENTICATED_REMEMBERED')")
     */  
    public function MessageAction(Request $request, Trick $trick)
    {
        $message = new Message();
        $form = $this->get('form.factory')->create(MessageType::class, $message);
        $em = $this->getDoctrine()->getManager();
        
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $message->setTrick($trick);
                $message->setUser($this->getUser());
                $em->persist($message);
                $em->flush();
                $form = $this->get('form.factory')->create(MessageType::class, new Message());
            }
        }
        return $form;
    }
        
}
