<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


use Symfony\Component\HttpFoundation\Response;




class NewapiController extends Controller {

    /**
     * @Route("/api/etablissement", name="etablissement_api")
     */
    public function EtablissementAction() {
           
        $sql = "select * from ac_etablissement";


        $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();

        return self::ResponseApi($data);
  
      
    }

    /**
     * @Route("/api/formation/{id}", name="formation_api")
     */
    public function FormationAction($id) {

           
        $sql = "select * from ac_formation where etablissement_id=".intval($id);


        $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();

        return self::ResponseApi($data);
  
      
    }

    /**
     * @Route("/api/promotion/{id}", name="promotion_api")
     */
    public function PrommotionAction($id) {

           
        $sql = "select * from ac_promotion where formation_id=".intval($id);


        $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();

        $response = new Response();

        return self::ResponseApi($data);
  
      
    }

     /**
     * @Route("/api/semestre/{id}", name="semestre_api")
     */
    public function SemestreAction($id) {

           
        $sql = "select * from ac_semestre where active = 1 and promotion_id=".intval($id);


        $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();

        return self::ResponseApi($data);
  
      
    }

    /**
     * @Route("/api/module/{id}", name="module_api")
     */
    public function ModuleAction($id) {

           
        $sql = "select * from ac_module where active = 1 and semestre_id=".intval($id);


        $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();

        return self::ResponseApi($data);
  
      
    }

    /**
     * @Route("/api/element/{id}", name="element_api")
     */
    public function ElementAction($id) {

           
        $sql = "select * from ac_element where active = 1 and module_id=".intval($id);

        $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();

        return self::ResponseApi($data);
  
      
    }



    

    public function ResponseApi($data) {

        $response = new Response();

        $response->setContent(json_encode($data));

        $response->headers->set('Content-Type', 'application/json');
        // Allow all websites
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, PATCH, OPTIONS');
        //$response->headers->set('Access-Control-Allow-Origin', 'https://jsfiddle.net/');//$response->headers->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, PATCH, OPTIONS');    
        return $response;
  
      
    }

    

   
     
}
    
    
    
    
    