<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\AcModule;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Snappy\Pdf;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use AppBundle\Entity\TEtudiant;
use AppBundle\Entity\TEtudiantInfo;
use AppBundle\Form\TEtudiantType;
use AppBundle\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\File;
use DateTime;
use DateInterval;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

 /**
    * @Route("/api", name="api")
 */

class TestController extends Controller {

    /**
     * @Route("/test", name="test")
     */
    public function testAction() {

      $response = new Response();

      $response->setContent(json_encode([
            ['id' => 1, 'username' => 'zikozzzz', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Octopus asked me a riddle, outsmarted me', 'date' => 'Dec. 10, 2015'],
            ['id' => 2, 'username' => 'ziko', 'avatarUri' => '/images/ryan.jpeg', 'note' => 'I counted 8 legs... as they wrapped around me', 'date' => 'Dec. 1, 2015'],
            ['id' => 3, 'username' => 'zak', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Inked!', 'date' => 'Aug. 20, 2015'],
            ['id' => 3, 'username' => 'AquaPelham', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Inked!', 'date' => 'Aug. 20, 2017'],
        ]));

      $response->headers->set('Content-Type', 'application/json');
      // Allow all websites
      $response->headers->set('Access-Control-Allow-Origin', '*');
      $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, PATCH, OPTIONS');
      // Or a predefined website
      //$response->headers->set('Access-Control-Allow-Origin', 'https://jsfiddle.net/');
      // You can set the allowed methods too, if you want    //$response->headers->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, PATCH, OPTIONS');    
      return $response;

        // $notes = [
        //     ['id' => 1, 'username' => 'ziko', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Octopus asked me a riddle, outsmarted me', 'date' => 'Dec. 10, 2015'],
        //     ['id' => 2, 'username' => 'ziko', 'avatarUri' => '/images/ryan.jpeg', 'note' => 'I counted 8 legs... as they wrapped around me', 'date' => 'Dec. 1, 2015'],
        //     ['id' => 3, 'username' => 'zak', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Inked!', 'date' => 'Aug. 20, 2015'],
        //     ['id' => 3, 'username' => 'AquaPelham', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Inked!', 'date' => 'Aug. 20, 2017'],
        // ];

        //  // $sql="SELECT * FROM fos_user";
        
        //  // $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
        //  // $stmt->execute();
        //  // $data = $stmt->fetchAll();
        //   return $this->json( $notes,200);
         // return new JsonResponse($data);
    }

    /**
     * @Route("/modules/{code}", name="modules")
     */
    public function modulesAction($code) {
        
       if($code == "all")
       $code_etudiant = " ";
       else
       $code_etudiant = "and t_admission.code='" . $code."'";
     

      // $sql="select ac_module.id,ac_module.code, ac_module.designation 
      //         from t_admission 
      //         JOIN t_inscription on t_admission.code = t_inscription.code_admission 
      //         JOIN ac_annee on t_inscription.code_annee = ac_annee.code 
      //         JOIN ac_formation on ac_annee.code_formation = ac_formation.code 
      //         JOIN ac_promotion on ac_formation.code = ac_promotion.code_formation and t_inscription.code_promotion = ac_promotion.code
      //         JOIN ac_semestre on ac_promotion.code = ac_semestre.code_promotion 
      //         JOIN ac_module on ac_semestre.code = ac_module.code_semestre 
      //         where ac_annee.cloture_academique='non' and ac_module.type ='N' and t_admission.code='ADM-FMA_MG00003920' and ac_module.active=1 and ac_semestre.designation in ('Semestre 2','Semestre 4','Semestre 
      //         6', 'Semestre 8', 'Semestre 10', 'Semestre 12', 'Semestre 14')";
      

      $sql = "select ac_module.id,ac_module.code, ac_module.designation 
              from t_admission 
              JOIN t_inscription on t_admission.id = t_inscription.t_admission_id
              JOIN ac_annee on t_inscription.ac_annee_id = ac_annee.id
              JOIN ac_formation on ac_annee.formation_id = ac_formation.id 
              JOIN ac_promotion on ac_formation.id = ac_promotion.formation_id and t_inscription.code_promotion = ac_promotion.code
              JOIN ac_semestre on ac_promotion.id = ac_semestre.promotion_id
              JOIN ac_module on ac_semestre.id = ac_module.semestre_id
              where ac_annee.cloture_academique='non' and ac_module.type ='N' ". $code_etudiant." and ac_module.active=1 and ac_semestre.designation in ('Semestre 1','Semestre 3','Semestre 
              5', 'Semestre 7', 'Semestre 9', 'Semestre 11', 'Semestre 13')";


              $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
              $stmt->execute();
              $data = $stmt->fetchAll();

              $response = new Response();

              $response->setContent(json_encode($data));

              $response->headers->set('Content-Type', 'application/json');
              // Allow all websites
              $response->headers->set('Access-Control-Allow-Origin', '*');
              $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, PATCH, OPTIONS');
              //$response->headers->set('Access-Control-Allow-Origin', 'https://jsfiddle.net/');//$response->headers->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, PATCH, OPTIONS');    
              return $response;

              
              // return $this->json( $data,200);

    }
    
    /**
     * @Route("/files/{code}", name="files")
     */
    public function filesAction($code) {

     $sql = "select   t_etudiant.code,t_etudiant.nom,t_etudiant.prenom,t_admission.code as 
                      code_admission,ac_promotion.code,ac_formation.abreviation,video.image,video.video,video.id
                      ,video.title
                      from t_admission 
                      JOIN t_preinscription on  t_preinscription.id  = t_admission.t_preinscription_id
                      JOIN t_etudiant on t_etudiant.id =  t_preinscription.t_etudiant_id
                      JOIN t_inscription on t_admission.code = t_inscription.code_admission 
                      JOIN ac_annee on t_inscription.code_annee = ac_annee.code 
                      JOIN ac_formation on ac_annee.code_formation = ac_formation.code 
                      JOIN ac_etablissement on ac_etablissement.id = ac_formation.etablissement_id
                      JOIN ac_promotion on ac_formation.code = ac_promotion.code_formation 
                      and t_inscription.code_promotion = ac_promotion.code
                      JOIN video_promotion on ac_promotion.id  =  video_promotion.id_promotion
                      join video  on video_promotion.id_video = video.id 
                      where ac_annee.cloture_academique='non' and t_admission.code='".$code."'";

                 $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                 $stmt->execute();
                 $data = $stmt->fetchAll();

                  return $this->json( $data,200);
    }

    /**
     * @Route("/profile/{code}", name="profile")
     */
    public function profileAction($code) {

    $sql ="select  ac_etablissement.designation as etb_designation , ac_formation.designation as 
                   frm_designation,ac_promotion.designation as prm_designation,   t_admission.code as 
                   code_admission 
                   ,ac_promotion.code, t_etudiant.nom, t_etudiant.prenom, t_etudiant.date_naissance, 
                   t_etudiant.lieu_naissance , t_etudiant.nationalite,t_etudiant.mail1
                  from t_admission 
                  JOIN t_preinscription on t_preinscription.id = t_admission.t_preinscription_id
                  JOIN t_etudiant on t_etudiant.id =    t_preinscription.t_etudiant_id
                  JOIN t_inscription on t_admission.code = t_inscription.code_admission 
                  JOIN ac_annee on t_inscription.code_annee = ac_annee.code 
                  JOIN ac_formation on ac_annee.code_formation = ac_formation.code 
                  JOIN ac_etablissement on ac_etablissement.id = ac_formation.etablissement_id
                  JOIN ac_promotion on ac_formation.code = ac_promotion.code_formation 
                  and t_inscription.code_promotion = ac_promotion.code
                  where ac_annee.cloture_academique='non' and t_admission.code='".$code."'";

                 $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                 $stmt->execute();
                 $data = $stmt->fetchAll();

                return $this->json( $data,200);
    }

    
    /**
     * @Route("/search/{code}/{search}", name="search")
     */
    public function searchAction($code,$search) {

           $sql = "select   t_admission.code as code_admission ,ac_promotion.code,video.image,video.video,video.id
                  ,video.title
                  from t_admission 
                  JOIN t_inscription on t_admission.code = t_inscription.code_admission 
                  JOIN ac_annee on t_inscription.code_annee = ac_annee.code 
                  JOIN ac_formation on ac_annee.code_formation = ac_formation.code 
                  JOIN ac_etablissement on ac_etablissement.id = ac_formation.etablissement_id
                  JOIN ac_promotion on ac_formation.code = ac_promotion.code_formation 
                  and t_inscription.code_promotion = ac_promotion.code
                  JOIN video_promotion on ac_promotion.id  =  video_promotion.id_promotion
                  join video  on video_promotion.id_video = video.id 
                  where ac_annee.cloture_academique='non'and t_admission.code='".$code."' 
                  and video.video like '%".$search."%'";
  
                 $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                 $stmt->execute();
                 $data = $stmt->fetchAll();

                 return $this->json($data,200);
    }


    /**
     * @Route("/users/{username}/{password}", name="users")
     */
    public function usersAction($username,$password) {
                 
              $array = [];
              $array['message'] = 'vide';
              
              $sql = "SELECT id,username,password FROM fos_user where username='".$username."' ";
              $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
              $stmt->execute();
              $data = $stmt->fetchAll();

              foreach ( $data as  $dat) {
                if($dat['username'] == $username && password_verify($password,$dat['password']) ){
                       $array['message'] = 'existe';
                }
              }

             return $this->json( $array,200);
    }

    /**
     * @Route("/reclamation/{username}", name="reclamation")
     */
    public function reclamationAction($username){
        
         $sql="  select R.id_reclamation as id_reclamation, R.message as message , R.object as objet ,R.created_at as created ,
                       user_rec.username ,RP.id_reclamation_reponse ,RP.message as message_reponse,
                       t_etudiant.nom as nom,t_etudiant.prenom as prenom,R.piece as piece
                from reclamation R 
                left join reclamation_reponse as RP on RP.fk_reclamation = R.id_reclamation  and RP.active = 1
                left join fos_user as etud on R.fk_user = etud.id 
                left join fos_user as user_rec on RP.fk_user = user_rec.id 
                inner join t_etudiant on t_etudiant.id = etud.t_etudiant_id
                where  etud.username ='".$username."' order by R.created_at desc";

                 $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                 $stmt->execute();
                 $data = $stmt->fetchAll();


                 return $this->json($data,200);
    }

    /**
     * @Route("/insert_reclamation", name="insert_reclamation")
     */
    public function insertReclamationAction(Request $request){

                  $stmt = $this->getDoctrine()->getConnection()->prepare("
                    INSERT INTO reclamation(object,message,fk_user,created_at,updated_at,deleted_at)
                    VALUES (:object,:message,:fk_user,:created_at,:updated_at,:deleted_at)");
                   
                    $objet       = $request->request->get('objet');
                    $reclamation = $request->request->get('reclamation');
                    $date  = date("Y/m/d h:i");
                    $var  =   4349;

                    $stmt->bindParam(':object'               , $objet);
                    $stmt->bindParam(':message'              , $reclamation);
                    $stmt->bindParam(':fk_user'              , $var);
                    $stmt->bindParam(':created_at'           , $date);
                    $stmt->bindParam(':updated_at'           , $date);
                    $stmt->bindParam(':deleted_at'           , $date);
     
                    $stmt->execute();
                

                    return $this->json($objet,200);
    }
        
     
}
    
    
    
    
    