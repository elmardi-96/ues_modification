<?php

namespace AppBundle\Controller;

/**
 * Etudiant controller.
 *
 * @Route("univ")
 */
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\AcEtablissement;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UniversiteController extends Controller {

    /**
     *
     * 
     * @Security("has_role('ROLE_MANAGER')  or has_role('ROLE_RESPONSABLE')")
     *
     * @Route("/getformation/{id}",options = { "expose" = true },  name="get_formation")
     * @Method({"GET", "POST"})
     */
    public function getFormationAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $AcEtablissement = $em->getRepository('AppBundle:AcEtablissement')->find($id);
        $formation = $em->getRepository('AppBundle:AcEtablissement')->GetFormation($AcEtablissement, null);
        $json_data = array(
            "data" => $formation
        );
        return new Response(json_encode($json_data));
    }

    /**

     * @Security("has_role('ROLE_MANAGER')  or has_role('ROLE_RESPONSABLE')")
     * @Route("/getpromotion/{id}",options = { "expose" = true },  name="get_promotion")
     * @Method({"GET", "POST"})
     */
    public function getPromotionAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $AcFormation = $em->getRepository('AppBundle:AcFormation')->find($id);
        $promotion = $em->getRepository('AppBundle:AcEtablissement')->GetPromotion($AcFormation, null);
        $json_data = array(
            "data" => $promotion
        );
        return new Response(json_encode($json_data));
    }

    /**

     * @Security("has_role('ROLE_MANAGER')  or has_role('ROLE_RESPONSABLE')")
     * @Route("/getsemestre/{id}",options = { "expose" = true },  name="get_semestre")
     * @Method({"GET", "POST"})
     */
    public function getSemestreAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $obj = $em->getRepository('AppBundle:AcPromotion')->find($id);
        $result = $em->getRepository('AppBundle:AcEtablissement')->GetSemestre($obj, null);
        $json_data = array(
            "data" => $result
        );
        return new Response(json_encode($json_data));
    }

    /**

     * @Security("has_role('ROLE_MANAGER')  or has_role('ROLE_RESPONSABLE')")
     * @Route("/getmodule/{id}",options = { "expose" = true },  name="get_module")
     * @Method({"GET", "POST"})
     */
    public function getModuleAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $obj = $em->getRepository('AppBundle:AcSemestre')->find($id);
        $result = $em->getRepository('AppBundle:AcEtablissement')->GetModule($obj, null);
        $json_data = array(
            "data" => $result
        );
        return new Response(json_encode($json_data));
    }

    /**

     * @Security("has_role('ROLE_MANAGER')  or has_role('ROLE_RESPONSABLE')")
     * @Route("/getelement/{id}",options = { "expose" = true },  name="get_element")
     * @Method({"GET", "POST"})
     */
    public function getelementAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $obj = $em->getRepository('AppBundle:AcModule')->find($id);
        $result = $em->getRepository('AppBundle:AcEtablissement')->GetElement($obj, null);
        $json_data = array(
            "data" => $result
        );
        return new Response(json_encode($json_data));
    }

}
