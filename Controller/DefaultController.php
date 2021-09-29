<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller {

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request) {


        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_MANAGER')) {
           return $this->redirectToRoute('management_users');
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_RESPONSABLE')) {
           return $this->redirectToRoute('management_cours_list');
        } elseif ($this->get('security.authorization_checker')->isGranted('ROLE_ETUDIANT')) {
            return $this->redirectToRoute('etudiant_index');
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_ENSEIGNANT')) {
          // return $this->render('enseignant/index.html.twig');
             return $this->redirectToRoute('enseignant');
        }
        elseif ($this->get('security.authorization_checker')->isGranted('ROLE_RECLAMATION')) {
          // return $this->render('enseignant/index.html.twig');
             return $this->redirectToRoute('admin_reclamation');
        }
        
        elseif ($this->get('security.authorization_checker')->isGranted('ROLE_OBSERVATEUR')) {
            // ADD By Moumni Amine SI
               return $this->redirectToRoute('observation_users');
          }

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
                    'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ]);
    }

   
    /**
     * @Route("/enseignant/home", name="enseignant_index")
     */
    public function enseignantAction(Request $request) {

        return $this->render('enseignant/index.html.twig');
    }

}
