<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\AcEtablissement ; 

/**
 * Etudiant controller.
 *
 * @Route("api/")
 */


class UnivApiTestController extends Controller
{
    
     /**
     * @Route("acetablissements/{id}", name="ac_etablissement_show")
     */
    public function showAction(AcEtablissement $AcEtablissement)
    {
       
        $data = $this->get('jms_serializer')->serialize($AcEtablissement, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
    
}
