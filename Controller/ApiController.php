<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\ResultSetMapping;
use Unirest\Request as Req;
use AppBundle\Entity\AcEtablissement;

/**
 * Etudiant controller.
 *
 * @Route("management/api/")
 */
class ApiController extends Controller {

    /**
     * Displays api index
     *
     * @Route("index" ,name="api_index")
     * 
     */
    public function ApiIndexAction(Request $request) {
        return $this->render('management/api.html.twig');
    }

    /**
     * @Route("syn/etablissement" , options = { "expose" = true } , name="syn_ac_etablissement")
     */
    public function SynchnisationEtablissementAction(Request $request) {


        return $this->InsertOrUpdateMydatabase('ac_etablissement', 'etablissement');
    }

    /**
     * @Route("syn/formation" , options = { "expose" = true } , name="syn_ac_formation")
     */
    public function SynchnisationFormationAction(Request $request) {


        return $this->InsertOrUpdateMydatabase('ac_formation', 'formation');
    }

    /**
     * @Route("syn/annee" , options = { "expose" = true } , name="syn_ac_annee")
     */
    public function SynchnisationAnneeAction(Request $request) {

        return $this->InsertOrUpdateAcAnneeMydatabase('ac_annee', 'annee');
    }

    /**
     * @Route("syn/promotion" , options = { "expose" = true } , name="syn_ac_promotion")
     */
    public function SynchnisationPromotionAction(Request $request) {
        return $this->InsertOrUpdateMydatabase('ac_promotion', 'promotion');
    }

    /**
     * @Route("syn/semestre" , options = { "expose" = true } , name="syn_ac_semestre")
     */
    public function SynchnisationSemestreAction(Request $request) {
        return $this->InsertOrUpdateMydatabase('ac_semestre', 'semestre');
    }

    /**
     * @Route("syn/module" , options = { "expose" = true } , name="syn_ac_module")
     */
    public function SynchnisationModuleAction(Request $request) {
        return $this->InsertOrUpdateMydatabase('ac_module', 'module');
    }

    /**
     * @Route("syn/element" , options = { "expose" = true } , name="syn_ac_element")
     */
    public function SynchnisationElementAction(Request $request) {
        return $this->InsertOrUpdateMydatabase('ac_element', 'element');
    }

    /**
     * @Route("syn/enseignant" , options = { "expose" = true } , name="syn_p_enseignant")
     */
    public function SynchnisationEnseignantAction(Request $request) {


        return $this->InsertOrUpdateMydatabase('p_enseignant', 'enseignant');
    }

    /**
     * @Route("syn/natureepreuve" , options = { "expose" = true } , name="syn_pr_nature_epreuve")
     */
    public function SynchnisationNatureEpreuveAction(Request $request) {


        return $this->InsertOrUpdateMydatabase('pr_nature_epreuve', 'natureepreuve');
    }

    /**
     * @Route("syn/epreuve" , options = { "expose" = true } , name="syn_ac_epreuve")
     */
    public function SynchnisationEpreuveAction(Request $request) {


        return $this->InsertOrUpdateMydatabase('ac_epreuve', 'epreuve');
    }

    /**
     * @Route("syn/typebac" , options = { "expose" = true } , name="syn_x_type_bac")
     */
    public function SynchnisationTypeBacAction(Request $request) {
        //selectionner le max id de notre base de données
        // $sql = "select max(id)  id from x_type_bac ";
        return $this->InsertOrUpdateMydatabase('x_type_bac', 'typebac');
    }

    /**
     * @Route("syn/modalite" , options = { "expose" = true } , name="syn_x_modalites")
     */
    public function SynchnisationModaliteAction(Request $request) {
        //selectionner le max id de notre base de données
        //$sql = "select max(id)  id from x_modalites ";
        return $this->InsertOrUpdateMydatabase('x_modalites', 'modalite');
    }

    /**
     * @Route("syn/langue" , options = { "expose" = true } , name="syn_x_langues")
     */
    public function SynchnisationLangueAction(Request $request) {
        //selectionner le max id de notre base de données
        //$sql = "select max(id)  id from x_langues ";
        return $this->InsertOrUpdateMydatabase('x_langues', 'langue');
    }

    /**
     * @Route("syn/filiere" , options = { "expose" = true } , name="syn_x_filiere")
     */
    public function SynchnisationFiliereAction(Request $request) {
        //selectionner le max id de notre base de données
        // $sql = "select max(id)  id from x_filiere ";
        return $this->InsertOrUpdateMydatabase('x_filiere', 'filiere');
    }

    /**
     * @Route("syn/banque" , options = { "expose" = true } , name="syn_x_banque")
     */
    public function SynchnisationBanqueAction(Request $request) {
        //selectionner le max id de notre base de données
        //$sql = "select max(id)  id from x_banque ";
        return $this->InsertOrUpdateMydatabase('x_banque', 'banque');
    }

    /**
     * @Route("syn/academie" , options = { "expose" = true } , name="syn_x_academie")
     */
    public function SynchnisationAcademieAction(Request $request) {
        //selectionner le max id de notre base de données
        //$sql = "select max(id)  id from x_academie ";
        return $this->InsertOrUpdateMydatabase('x_academie', 'academie');
    }

    /**
     * @Route("syn/naturedemande" , options = { "expose" = true } , name="syn_nature_demande")
     */
    public function SynchnisationNatureDemandeAction(Request $request) {
        //selectionner le max id de notre base de données
        //$sql = "select max(id)  id from nature_demande ";
        return $this->InsertOrUpdateMydatabase('nature_demande', 'naturedemande');
    }

    /**
     * @Route("syn/statut" , options = { "expose" = true } , name="syn_p_statut")
     */
    public function SynchnisationStatutAction(Request $request) {
        //selectionner le max id de notre base de données
        // $sql = "select max(id)  id from p_statut ";
        return $this->InsertOrUpdateMydatabase('p_statut', 'statut');
    }

    /**
     * @Route("syn/salle" , options = { "expose" = true } , name="syn_p_salles")
     */
    public function SynchnisationSallesAction(Request $request) {
        //selectionner le max id de notre base de données
        //$sql = "select max(id)  id from p_salles ";
        return $this->InsertOrUpdateMydatabase('p_salles', 'salle');
    }

    /**
     * @Route("syn/organisme" , options = { "expose" = true } , name="syn_p_organisme")
     */
    public function SynchnisationOrganismeAction(Request $request) {
        //selectionner le max id de notre base de données
        //$sql = "select max(id)  id from p_organisme ";
        return $this->InsertOrUpdateMydatabase('p_organisme', 'organisme');
    }

    /**
     * @Route("syn/frais" , options = { "expose" = true } , name="syn_p_frais")
     */
    public function SynchnisationFraisAction(Request $request) {
        //selectionner le max id de notre base de données
        //$sql = "select max(id)  id from p_frais ";
        return $this->InsertOrUpdateMydatabase('p_frais', 'frais');
    }

    /**
     * @Route("syn/estatut" , options = { "expose" = true } , name="syn_p_estatut")
     */
    public function SynchnisationEstatutAction(Request $request) {
        //selectionner le max id de notre base de données
        //$sql = "select max(id)  id from p_estatut ";
        return $this->InsertOrUpdateMydatabase('p_estatut', 'estatut');
    }

    /**
     * @Route("syn/etudiant" , options = { "expose" = true } , name="syn_t_etudiant")
     */
    public function SynchnisationTEtudiantAction(Request $request) {
        //selectionner le max id de notre base de données
        //$sql = "select max(id)  id from t_etudiant ";
        return $this->InsertOrUpdateMydatabase('t_etudiant', 'etudiant');
    }

    /**
     * @Route("syn/preinscription" , options = { "expose" = true } , name="syn_t_preinscription")
     */
    public function SynchnisationTPreinscriptionAction(Request $request) {
        //selectionner le max id de notre base de données
        $sql = "select max(id)  id from t_preinscription ";
        return $this->InsertOrUpdateMydatabase('t_preinscription', 'preinscription');
    }

    /**
     * @Route("syn/admission" , options = { "expose" = true } , name="syn_t_admission")
     */
    public function SynchnisationTAdmissionAction(Request $request) {
        //selectionner le max id de notre base de données
        $sql = "select max(id)  id from t_admission ";
        return $this->InsertOrUpdateMydatabase('t_admission', 'admission');
    }

    /**
     * @Route("syn/inscription" , options = { "expose" = true } , name="syn_t_inscription")
     */
    public function SynchnisationTInscriptionAction(Request $request) {
        //selectionner le max id de notre base de données
        $sql = "select max(id)  id from t_inscription ";
        return $this->InsertOrUpdateMydatabase('t_inscription', 'inscription');
    }

    /**
     * @Route("syn/operationcab" , options = { "expose" = true } , name="syn_t_operationcab")
     */
    public function SynchnisationTOperationcabAction(Request $request) {
        //selectionner le max id de notre base de données
        // $sql = "select max(id)  id from t_operationcab ";

        return $this->InsertOrUpdateMydatabase('t_operationcab', 'operationcab');
    }

    /**
     * @Route("syn/operationdet" , options = { "expose" = true } , name="syn_t_operationdet")
     */
    public function SynchnisationTOperationDetAction(Request $request) {
        //selectionner le max id de notre base de données
        // $sql = "select max(id_operationdet)  id from t_operationdet ";
        return $this->InsertOrUpdateMydatabase('t_operationdet', 'operationdet');
    }

    /**
     * @Route("syn/regelement" , options = { "expose" = true } , name="syn_t_regelement")
     */
    public function SynchnisationRegelementAction(Request $request) {
        //selectionner le max id de notre base de données
        // $sql = "select max(id)  id from t_regelement ";
        return $this->InsertOrUpdateMydatabase('t_regelement', 'regelement');
    }

    /**
     * @Route("syn/gnote" , options = { "expose" = true } , name="syn_ex_gnotes")
     */
    public function SynchnisationExGnotesAction(Request $request) {
        //selectionner le max id de notre base de données
        // $sql = "select max(id)  id from ex_gnotes ";
        return $this->InsertOrUpdateMydatabase('ex_gnotes', 'gnote');
    }

    /**
     * @Route("syn/enote" , options = { "expose" = true } , name="syn_ex_enotes")
     */
    public function SynchnisationExEnotesAction(Request $request) {
        return $this->InsertOrUpdateMydatabase('ex_enotes', 'enote');
    }

    /**
     * @Route("syn/mnote" , options = { "expose" = true } , name="syn_ex_mnotes")
     */
    public function SynchnisationExMnotesAction(Request $request) {

        return $this->InsertOrUpdateMydatabase('ex_mnotes', 'mnote');
    }

    /**
     * @Route("syn/snote" , options = { "expose" = true } , name="syn_ex_snotes")
     */
    public function SynchnisationSxMnotesAction(Request $request) {
        return $this->InsertOrUpdateMydatabase('ex_snotes', 'snote');
    }

    /**
     * @Route("syn/anote" , options = { "expose" = true } , name="syn_ex_anotes")
     */
    public function SynchnisationAxMnotesAction(Request $request) {
        return $this->InsertOrUpdateMydatabase('ex_anotes', 'anote');
    }

    /**
     * @Route("syn/programme" , options = { "expose" = true } , name="syn_pr_programmation")
     */
    public function SynchnisationPrProgrammationAction(Request $request) {
        //selectionner le max id de notre base de données
        // $sql = "select max(id)  id from pr_programmation "; 
        return $this->InsertOrUpdateMydatabase('pr_programmation', 'programme');
    }

    /**
     * @Route("syn/emptime" , options = { "expose" = true } , name="syn_pl_emptime")
     */
    public function SynchnisationPlEmptimeAction(Request $request) {
        //selectionner le max id de notre base de données
        //  $sql = "select max(id)  id from pl_emptime ";
        return $this->InsertOrUpdateMydatabase('pl_emptime', 'emptime');
    }

    /**
     * @Route("syn/emptimens" , options = { "expose" = true } , name="syn_pl_emptimens")
     */
    public function SynchnisationPlEmptimensAction(Request $request) {
        //selectionner le max id de notre base de données
        //$sql = "select max(id)  id from pl_emptimens ";
        return $this->InsertOrUpdateMydatabase('pl_emptimens', 'emptimens');
    }

    /**
     * @Route("syn/absence" , options = { "expose" = true } , name="syn_xseance_absences")
     */
    public function SynchnisationXSeanceAbsences(Request $request) {
        $sql = "select max(id)  id from xseance_absences ";
        $conn = $this->getDoctrine()->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        $max_id = 0;
        if ($result['id']) {
            $max_id = $result['id'];
        }


        return $this->InsertOrUpdateMydatabaseByMaxid('xseance_absences', 'absence', $max_id);
    }
    
    
    
    
     /**
     * @Route("syn/puser" , options = { "expose" = true } , name="syn_p_user")
     */
    public function SynchnisationPUserAction(Request $request) {
        $detail = array('code' => 0, 'row' => 0, 'updated' => 0);
        $detail['code'] = 200;
        $detail['updated'] = 0;
        $password = '$2y$13$XiSyLqZT6h6XROHEBlfTR.ttPE5U3bs4QuFDDoBIwCuwQBMECa/ti';
        $roles = 'a:1:{i:0;s:13:"ROLE_ETUDIANT";}';


        $sql = "select etu.id as t_etudiant_id,
   adm.code as username ,
   adm.code as username_canonical ,
   etu.`mail1` as email,
   etu.`mail1` as email_canonical,
   '1' as 'enabled',
   '$password' as 'password',
   '$roles'  as 'roles',
   '0123456789' as 'default_p' 

   from t_inscription ins 
inner join t_admission adm on adm.code = ins.code_admission 
inner join t_preinscription pre on pre.code = adm.code_preinscription
inner join t_etudiant etu on etu.code = pre.code_etudiant 
where adm.code not in  (select fos.username from fos_user fos ) 
group by etu.id , adm.code
ORDER BY `username`  ASC";
        $conn = $this->getDoctrine()->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();
        foreach ($data as $key => $row) {
            if (empty($current_row)) {
                $detail['row'] ++;
                $conn->insert('fos_user', (array) $row);
            }
        }

        return new Response(json_encode($detail));
    }
    
    
    

    /**
     * @Route("syn/sanction" , options = { "expose" = true } , name="syn_xseance_sanction")
     */
    public function SynchnisationXSeanceSanction(Request $request) {
        //selectionner le max id de notre base de données
        //$sql = "select max(id)  id from xseance_sanction ";
        return $this->InsertOrUpdateMydatabase('xseance_sanction', 'sanction');
    }

    private function InsertOrUpdateMydatabase($ma_table, $link) {

        /* parametrs req */
        if ($ma_table == 't_operationdet') {
            $column_name = 'id_operationdet';
            $sqlreq = "SELECT * FROM $ma_table WHERE id_operationdet = ";
        } else {
            $column_name = 'id';
            $sqlreq = "SELECT * FROM $ma_table WHERE id = ";
        }



        $detail = array('code' => 0, 'row' => 0, 'updated' => 0);
        $headers = array('Accept' => 'application/json');
        $response = Req::get($this->getParameter('api_link') . '/api/' . $link . '/0', $headers);
        $conn = $this->getDoctrine()->getEntityManager()->getConnection();

        if ($response->code == 200) {
            $detail['code'] = 200;
            if ($response->body) {
                
            //    dump($response->body); die(); 
                foreach ($response->body as $key => $univ_row) {
                    $id = $univ_row->$column_name;

                    $stmt = $conn->prepare("$sqlreq $id");
                    $stmt->execute();
                    $current_row = $stmt->fetch();
                 //   dump($current_row); die(); 
                    
                    if (empty($current_row)) {
                        $detail['row'] ++;
                        $conn->insert($ma_table, (array) $univ_row);
                    } else {

                        $diff = array_diff((array) $univ_row, $current_row);
                        if (!empty($diff)) {


                            $detail['updated'] ++;
                            $conn->update($ma_table, (array) $univ_row, array($column_name => $id));
                        }
                    }
                }
            }
        }
        return new Response(json_encode($detail));
    }

    private function InsertOrUpdateMydatabaseWidoutchecekforeignkey($ma_table, $link) {

        /* parametrs req */
        $sqlreq = "SELECT * FROM $ma_table WHERE id = ";


        $detail = array('code' => 0, 'row' => 0, 'updated' => 0);
        $headers = array('Accept' => 'application/json');
        $response = Req::get($this->getParameter('api_link') . '/api/' . $link . '/0', $headers);
        $conn = $this->getDoctrine()->getEntityManager()->getConnection();

        if ($response->code == 200) {
            $detail['code'] = 200;
            if ($response->body) {
                foreach ($response->body as $key => $univ_row) {
                    $id = $univ_row->id;
                    /*                     * *select from this table to chesck  if this row exist or not **** */
                    $stmt = $conn->prepare("$sqlreq $id");
                    $stmt->execute();
                    $current_row = $stmt->fetch();
                    if (empty($current_row)) {
                        $detail['row'] ++;
                        $conn->insert($ma_table, (array) $univ_row);
                    } else {

                        $diff = array_diff((array) $univ_row, $current_row);
                        if (!empty($diff)) {


                            $detail['updated'] ++;

                            $conn->exec('SET FOREIGN_KEY_CHECKS = 0;');
                            $conn->update($ma_table, (array) $univ_row, array('id' => $id));
                        }
                    }
                }
            }
        }
        return new Response(json_encode($detail));
    }

    private function InsertOrUpdateMydatabaseArrays($ma_table, $link) {

        /* parametrs req */
        $sqlreq = "SELECT * FROM $ma_table";


        $detail = array('code' => 0, 'row' => 0, 'updated' => 0);
        $headers = array('Accept' => 'application/json');
        $response = Req::get($this->getParameter('api_link') . '/api/' . $link . '/0', $headers);
        $conn = $this->getDoctrine()->getEntityManager()->getConnection();

        if ($response->code == 200) {
            $detail['code'] = 200;
            if ($response->body) {
                foreach ($response->body as $key => $univ_row) {
                    $id = $univ_row->id;
                  //  echo $id . "<br/>";
                    $stmt = $conn->prepare("$sqlreq");
                    $stmt->execute();
                    $current_row = $stmt->fetchAll();
                    //dump($current_row);  

                    $found_key = array_search($id, array_column($current_row, 'id'));



                    //   dump($found_key); 

                    if (!is_numeric($found_key)) {

                        $detail['row'] ++;
                        //   $conn->insert($ma_table, (array) $univ_row);
                    } else {

                        $diff = array_diff((array) $univ_row, $current_row[$found_key]);
                        if (!empty($diff)) {


                            $detail['updated'] ++;


                            //  $conn->update($ma_table, (array) $univ_row, array('id' => $id));
                        }
                    }

                    if ($key == 1000) {
                        die();
                    }
                }
                // die();
            }
        }
        return new Response(json_encode($detail));
    }

    private function InsertOrUpdateMydatabaseByMaxid($ma_table, $link, $max_id) {

        /* parametrs req */

        $sqlreq = "SELECT * FROM $ma_table WHERE id = ";




        $detail = array('code' => 0, 'row' => 0, 'updated' => 0);
        $headers = array('Accept' => 'application/json');
        $response = Req::get($this->getParameter('api_link') . '/api/' . $link . '/' . $max_id, $headers);
        $conn = $this->getDoctrine()->getEntityManager()->getConnection();
        
      //  dump($response->body); die();

        if ($response->code == 200) {
            $detail['code'] = 200;
            if ($response->body) {
                foreach ($response->body as $key => $univ_row) {
                    $id = $univ_row->id;
                    /*                     * select from this table to chesck  if this row exist or not **** */
                    $stmt = $conn->prepare("$sqlreq $id");
                    $stmt->execute();
                    $current_row = $stmt->fetch();
                    if (empty($current_row)) {
                        $detail['row'] ++;
                        $conn->insert($ma_table, (array) $univ_row);
                    } else {

                        $diff = array_diff((array) $univ_row, $current_row);
                        if (!empty($diff)) {


                            $detail['updated'] ++;
                            $conn->update($ma_table, (array) $univ_row, array('id' => $id));
                        }
                    }
                }
            }
        }
        return new Response(json_encode($detail));
    }

    private function InsertOrUpdateAcAnneeMydatabase($ma_table, $link) {

        /* parametrs req */
        $sqlreq = "SELECT `id`, `etablissement_id`, `formation_id`, `code`, `code_etablissement`, `code_formation`, `code_promotion`, `designation` FROM  $ma_table WHERE id = ";
        $detail = array('code' => 0, 'row' => 0, 'updated' => 0);
        $headers = array('Accept' => 'application/json');
        $response = Req::get($this->getParameter('api_link') . '/api/' . $link . '/0', $headers);
        $conn = $this->getDoctrine()->getEntityManager()->getConnection();

        if ($response->code == 200) {
            $detail['code'] = 200;
            if ($response->body) {
                foreach ($response->body as $key => $univ_row) {
                    $id = $univ_row->id;
                    $stmt = $conn->prepare("$sqlreq $id");
                    $stmt->execute();
                    $current_row = $stmt->fetch();
                    if (empty($current_row)) {
                        $detail['row'] ++;
                        $conn->insert($ma_table, (array) $univ_row);
                    } else {

                        $diff = array_diff((array) $univ_row, $current_row);
                        if (!empty($diff)) {


                            $detail['updated'] ++;
                            $conn->update($ma_table, (array) $univ_row, array('id' => $id));
                        }
                    }
                }
            }
        }
        return new Response(json_encode($detail));
    }

}
