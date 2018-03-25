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
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Core\Type\FormType;

/**
 * Entities
 */
use GB\TricksBundle\Entity\Trick;
use GB\TricksBundle\Form\TrickType;



class TrickController extends Controller
{
    public function indexAction(Request $request, $page)
    {
        $em = $this->getDoctrine()->getManager();
        $trickRepository = $em->getRepository('GBTricksBundle:Trick');

        $tricks = $trickRepository->findBy(array(), null, 8*$page);
        
        return $this->render('GBTricksBundle:Trick:index.html.twig',
                array('tricks' => $tricks));
    }
    
    /**
     * @ParamConverter("trick", options={"mapping": {"trick_id": "id"}})
     */    
    public function viewAction(Trick $trick)
    {
        return $this->render('GBTricksBundle:Trick:view.html.twig',
                array('trick' => $trick));
    }
    
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
                return $this->redirectToRoute('gb_tricks_homepage');
            }
        }
        
        return $this->render('GBTricksBundle:Trick:save.html.twig',
                array('form' => $form->createView()));
    }
    
    /**
     * @ParamConverter("trick", option={"mapping": {"trick_id": "id"}})
     */  
    public function modifyAction(Trick $trick)
    {
        $form = $this->get('form.factory')->create(TrickType::class, $trick);
        
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                return $this->redirectToRoute('gb_tricks_trickpage');
            }
        }
        return $this->render('GBTricksBundle:Trick:save.html.twig',
            array('form' => $form));
    }
    
    /**
     * @ParamConverter("trick", option={"mapping": {"trick_id": "id"}})
     */  
    public function deleteAction(Trick $trick)
    {
        $this->getDoctrine()->getManager()->remove($trick);
    }
    
    /**
     * @ParamConverter("trick", option={"mapping": {"trick_id": "id"}})
     */  
    public function addMessageAction(Trick $trick)
    {
        
    }
    
    
}
