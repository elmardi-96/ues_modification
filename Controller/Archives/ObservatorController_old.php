<?php

namespace AppBundle\Controller\Archives;

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
 * @Security("has_role('ROLE_OBSERVATOR')")
 */

class ObservatorController_old extends Controller {
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

        $sql = "SELECT id, utilisateur, observation, info_valide, nom, prenom FROM t_etudiant1 where info_valide = 0 and observation is not null";
        
        //$totalRows .= $sql;
        //  $sqlRequest .= $sql;


        $stmt = $this->getDoctrine()->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $data = array();

        foreach ($result as $key => $row) {

            $nestedData = array();
            $nestedData[] = ++$key;



            
            $cd = $row['id'];

            $nestedData[] = $row['observation'];
            $nestedData[] = $row['utilisateur'];
            $nestedData[] = $row['nom'];
            $nestedData[] = $row['prenom'];            
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
