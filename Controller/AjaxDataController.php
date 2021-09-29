<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Acannee controller.
 *
 * @Route("ajax")
 */
class AjaxDataController extends Controller {

    /**
     * 
     *
     * @Route("/semestres/{code_annee}/{code_promotion}",options = { "expose" = true } , name="semestres_by_code")
     * 
     */
    public function indexAction($code_annee, $code_promotion) {
        $repository = $this->getDoctrine()
                ->getRepository('AppBundle:PrProgrammation');

        $query = $repository->createQueryBuilder('p')
                ->select('p.idSemestre')
                ->where('p.idAnnee = :idannee')
                ->andWhere('p.idPromotion = :idpromotion')
                ->setParameter('idannee', $code_annee)
                ->setParameter('idpromotion', $code_promotion)
                ->distinct()
                ->getQuery();
        $semestres = $query->getResult();






        $result = "";
        foreach ($semestres as $key => $value) {

            $repository = $this->getDoctrine()->getRepository('AppBundle:AcSemestre');
            $sem = $repository->findOneByCode($value['idSemestre']);
            $result .= "<li value='" . $sem->getcode() . "'>" . $sem->getDesignation() . "</li>";
        }


        $response = new JsonResponse();
        $rs = $response->setData($result);


        return $rs;
    }

    /**
     *
     *
     * @Route("/tree/",options = { "expose" = true }, name="etudiant_tree")
     * 
     */
    public function TreeAction() {
        $session = new Session();



        $repository = $this->getDoctrine()->getRepository('AppBundle:TInscription');
        $inscription = $repository->find($session->get('id_inscription'));


        $all = array();

        foreach ($inscription->getPromotion()->getSemestres() as $key => $value) {
            $datah = array();
            $datah['id'] = $value->getId();
            $datah['text'] = $value->getDesignation();

            $datah['children'] = array();
            $datah['data'] = new \stdClass();
            foreach ($inscription->getPromotion()->getSemestres()[$key]->getModules() as $key2 => $value2) {
                $datah2 = array();
                $datah2['id'] = $value2->getId();
                $datah2['parent_id'] = $value->getId();
                $datah2['text'] = $value2->getDesignation();
                $datah2['children'] = array();
                $datah2['data'] = new \stdClass();
                $datah['children'][] = $datah2;


                foreach ($inscription->getPromotion()->getSemestres()[$key]->getModules()[$key2]->getElements() as $key3 => $value3) {
                    $datah3 = array();
                    $datah3['id'] = $value3->getId();
                    $datah3['parent_id'] = $value2->getId();
                    $datah3['text'] = $value3->getDesignation();

                    $datah3['children'] = array();
                    $datah3['data'] = new \stdClass();

                    var_dump($datah3);
                    die();
                    $datah2[$value2->getId()['parent_id']]['children'][] = $datah3;
                }
            }




            $all[$key] = $datah;
            break;
        }


        //  echo json_encode($all);
        $response = new Response(json_encode($all));

        return $response;
//        die();
//return $this->render('etudiant/examen.html.twig', array('inscription' => $inscription));
    }

}
