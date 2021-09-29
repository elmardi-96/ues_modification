<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;
use AppBundle\Entity\Major;
use AppBundle\Service\FileUploader3;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Acannee controller.
 *
 * @Route("responsable")
 */
class ResponsableCoursController extends Controller {

  

    /**
     * Displays a form to edit an existing major entity.
     *
     * @Route("/cours", name="_management_cours_index")
     * @Method({"GET", "POST"})
     */
    public function CourssFormAction(Request $request) {


        if ($request->isMethod('post') && !is_numeric($request->request->get('cours_id'))) {
            $this->addFlash(
                    'notice', "Veuillez Rensigner un élément pour effectuer cett opération"
            );
            $em = $this->getDoctrine()->getManager();
            $etablissement = $em->getRepository('AppBundle:AcEtablissement')->GetEtablissement(null);
            return $this->render('management/cours_index.html.twig', array('etablissement' => $etablissement));
        } else if ($request->isMethod('post') && is_numeric($request->request->get('cours_id'))) {
            //dump($request->request->get('cours_id')); die();
            return $this->redirectToRoute('management_cours_add', array('id' => $request->request->get('cours_id')));
        }



        $em = $this->getDoctrine()->getManager();
        $etablissement = $em->getRepository('AppBundle:AcEtablissement')->GetEtablissement(null);
        return $this->render('management/cours_index.html.twig', array('etablissement' => $etablissement));
    }

    /**
     * Displays a form to edit an existing major entity.
     *
     * @Route("/cours/add/{id}", name="_management_cours_add")
     * @Method({"POST","GET"})
     */
    public function CoursNewAction(Request $request, $id) {


        $cour = new \AppBundle\Entity\Cours();
        $form = $this->createForm('AppBundle\Form\CoursType', $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //dump($form); die();
            $em = $this->getDoctrine()->getManager();
            $AcElement = $em->getRepository('AppBundle:AcElement')->find($id);
            $cour->setElement($AcElement);
            $cour->setCodeCours($AcElement->getCode());
            $em->persist($cour);
            $em->flush();




            $this->addFlash(
                    'notice', "L'enregistrement a été effectué"
            );


           
            return $this->redirectToRoute('management_cours_list');
        }

        return $this->render('management/cours_new.html.twig', array(
                    'cour' => $cour,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing major entity.
     *
     * @Route("/cours/list", name="_management_cours_list")
     * 
     */
    public function CoursListAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $cours = $em->getRepository('AppBundle:Cours')->findAll();
        return $this->render('management/cours_list.html.twig', array(
                    'cours' => $cours,
        ));
    }

    /**
     * Deletes a cour entity.
     *
     * @Route("/cours/{id}/delete", name="_management_cours_delete")
   
     */
    public function deleteCoursAction(Request $request, $id) {


        $em = $this->getDoctrine()->getManager();
        $cour = $em->getRepository('AppBundle:Cours')->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($cour);
        $em->flush();
        
        $this->addFlash(
                    'notice', "La suppression à été bien effectué"
            );


        return $this->redirectToRoute('management_cours_list');
    }
    
    
     /**
     * Displays a form to edit an existing major entity.
     *
     * @Route("/videos", name="_management_videos_index")
     * @Method({"GET", "POST"})
     */
    public function VideosFormAction(Request $request) {


        if ($request->isMethod('post') && !is_numeric($request->request->get('videos_id'))) {
            $this->addFlash(
                    'notice', "Veuillez Renseigner un élément pour effectuer cette opération"
            );
            $em = $this->getDoctrine()->getManager();
            $etablissement = $em->getRepository('AppBundle:AcEtablissement')->GetEtablissement(null);
            return $this->render('management/videos_index.html.twig', array('etablissement' => $etablissement));
        } else if ($request->isMethod('post') && is_numeric($request->request->get('videos_id'))) {
            //dump($request->request->get('videos_id')); die();
            return $this->redirectToRoute('management_videos_add', array('id' => $request->request->get('videos_id')));
        }



        $em = $this->getDoctrine()->getManager();
        $etablissement = $em->getRepository('AppBundle:AcEtablissement')->GetEtablissement(null);
        return $this->render('management/videos_index.html.twig', array('etablissement' => $etablissement));
    }

    /**
     * Displays a form to edit an existing major entity.
     *
     * @Route("/videos/add/{id}", name="_management_videos_add")
     * @Method({"POST","GET"})
     */
    public function VideosNewAction(Request $request, $id) {


        $cour = new \AppBundle\Entity\Videos();
        $form = $this->createForm('AppBundle\Form\VideosType', $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //dump($form); die();
            $em = $this->getDoctrine()->getManager();
            $AcElement = $em->getRepository('AppBundle:AcElement')->find($id);
            $cour->setElement($AcElement);
            $cour->setCodeVideos($AcElement->getCode());
            $em->persist($cour);
            $em->flush();




            $this->addFlash(
                    'notice', "L'enregistrement a été effectué"
            );


           
            return $this->redirectToRoute('management_videos_list');
        }

        return $this->render('management/videos_new.html.twig', array(
                    'cour' => $cour,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing major entity.
     *
     * @Route("/videos/list", name="_management_videos_list")
     * 
     */
    public function VideosListAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $videos = $em->getRepository('AppBundle:Videos')->findAll();
        return $this->render('management/videos_list.html.twig', array(
                    'videos' => $videos,
        ));
    }

    /**
     * Deletes a cour entity.
     *
     * @Route("/videos/{id}/delete", name="_management_videos_delete")
   
     */
    public function deleteVideosAction(Request $request, $id) {


        $em = $this->getDoctrine()->getManager();
        $cour = $em->getRepository('AppBundle:Videos')->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($cour);
        $em->flush();
        
        $this->addFlash(
                    'notice', "La suppression à été bien effectué"
            );


        return $this->redirectToRoute('management_videos_list');
    }
    
  

}
