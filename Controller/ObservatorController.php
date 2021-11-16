<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * Etudiant controller.
 *
 * @Route("observation")
 * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_OBSERVATOR')")
 */

class ObservatorController extends Controller {
    /**
     * 
     * @Route("/", name="observation_users")
     * @Method("GET")
     */
    public function indexAction()
    {
        // $em = $this->getDoctrine()->getManager();

        // $videos = $em->getRepository('AppBundle:videos')->findAll();
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $query = $repository->createQueryBuilder('u')
                ->where('u.etudiant IS NOT NULL ')
                ->getQuery();
        $users = $query->getResult();


        // dump($users); die();

        return $this->render('observation/index.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * 
     *
     * @Route("/listobservation",options = { "expose" = true } , name="list_observation")
     * @Method({"GET", "POST"})
     */
    public function action() {

        $em   = $this->getDoctrine()->getManager();
        $etudiant  = $em->getRepository('AppBundle:TEtudiantInfo')->findBy(['inscriptionValide' => 0]);
        

        $data = array();

        foreach ($etudiant as $key => $row) {

            $nestedData = array();
            $nestedData[] = ++$key;



            
            $cd = $row->getId();

            $nestedData[] = $row->getObservation();
            $nestedData[] = $row->getUtilisateur();
            $nestedData[] = $row->getNom();
            $nestedData[] = $row->getPrenom();
            // $nestedData[] = "<a class='openModel' data-id='".$cd."'><i  class='btn btn-xs btn-primary ace-icon fa fa-plus bigger-120'></i></a>";

            $nestedData["DT_RowId"] = $cd;

            $data[] = $nestedData;
        }

        $json_data = array(
            "data" => $data   // total data array
        );
        // dump($json_data['data'][0]);
        // die;

        return new Response(json_encode($json_data));
    }
    

}
