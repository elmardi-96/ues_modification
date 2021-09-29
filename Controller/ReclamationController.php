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


class ReclamationController extends Controller {

    /**
     * @Route("/reclamation", name="reclamation")
     */
    public function indexReclamationAction(Request $request) {
    if ($this->get('security.authorization_checker')->isGranted('ROLE_ETUDIANT')) {

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("RECLAMATIONS", "reclamation");
        $breadcrumbs->prependRouteItem("Home", "etudiant_index");

        $user = $this->getUser();
        
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $userx = $repository->findOneById($user->getId());
        
         $sql=" select RP.valider as valider, R.id_reclamation as id_reclamation, R.message as message , R.object as objet ,R.created_at as created ,
                       user_rec.username ,RP.id_reclamation_reponse ,RP.message as message_reponse,
                       t_etudiant.nom as nom,t_etudiant.prenom as prenom,R.piece as piece
                from reclamation R 
                left join reclamation_reponse as RP on RP.fk_reclamation = R.id_reclamation  and RP.active = 1
                left join fos_user as etud on R.fk_user = etud.id 
                left join fos_user as user_rec on RP.fk_user = user_rec.id 
                inner join t_etudiant on t_etudiant.id = etud.t_etudiant_id
                where  R.fk_user =".$user->getId()." order by R.created_at desc";

                 $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                 $stmt->execute();
                 $reclamations = $stmt->fetchAll();
                 
        return $this->render('reclamation/reclamation_etudiant.html.twig', [
                    'reclamations'=>$reclamations
                    ]);}else{
                     return $this->redirectToRoute('homepage');
                    }
    }
    
    
    /**
     * @Route("/admin_reclamation", name="admin_reclamation")
     */
    public function indexAdminReclamationAction(Request $request) {
        
     if ($this->get('security.authorization_checker')->isGranted('ROLE_RECLAMATION')) {

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("RECLAMATIONS", "reclamation");
        $breadcrumbs->prependRouteItem("Home", "etudiant_index");

        $user = $this->getUser();
        
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $userx = $repository->findOneById($user->getId());

        $username = $user->getUsername();
        $service_sql = "" ;
        if($username !=  "reclamation"){
            $service_sql = " and  R.service =".$user->getId() ;
        }


        
        // $sql=" select R.id_reclamation as id_reclamation, R.message as message , R.object as objet ,R.created_at as created ,
        //               user_rec.username ,RP.id_reclamation_reponse ,RP.message as message_reponse,
        //               t_etudiant.nom as nom,t_etudiant.prenom as prenom
        //         from reclamation R 
        //         left join reclamation_reponse as RP on RP.fk_reclamation = R.id_reclamation 
        //         left join fos_user as etud on R.fk_user = etud.id 
        //         left join fos_user as user_rec on RP.fk_user = user_rec.id 
        //         inner join t_etudiant on t_etudiant.id = etud.t_etudiant_id
        //         order by R.created_at desc
        //     ";

        // ----- sql salah ---------

        
        // $sql="select R.id_reclamation as id_reclamation, R.message as message , R.object as objet ,DATE_FORMAT(R.created_at,'%d-%m-%Y %H:%i:%s') as created ,
        //                user_rec.username ,RP.id_reclamation_reponse ,RP.message as message_reponse,
        //                t_etudiant.nom as nom,t_etudiant.prenom as prenom, ac_promotion.ordre as promo , ac_formation.abreviation as forma,R.piece as piece
        //                 from reclamation R 
        //                 inner join fos_user as etud on R.fk_user = etud.id  
        //                 inner join t_admission on etud.username = t_admission.code
        //                 inner join t_inscription on t_inscription.code_admission = t_admission.code
        //                 inner join ac_annee on ac_annee.id = t_inscription.ac_annee_id
        //                 inner join ac_promotion on t_inscription.code_promotion = ac_promotion.code
        //                 inner join ac_formation on ac_promotion.code_formation = ac_formation.code
        //                 inner join t_etudiant on t_etudiant.id = etud.t_etudiant_id
        //                 left join reclamation_reponse as RP on RP.fk_reclamation = R.id_reclamation and RP.active = 1
        //                 left join fos_user as user_rec on RP.fk_user = user_rec.id 
        //                 where ac_annee.validation_academique = 'non' 
        //                 order by R.id_reclamation desc limit 2";



         // ----- sql salah ---------
      
        $sql = "select R.service as service , RP.valider as valider , R.id_reclamation as id_reclamation, R.message as message , R.object as objet ,DATE_FORMAT(R.created_at,'%d-%m-%Y %H:%i:%s') as created ,
        user_rec.username ,RP.id_reclamation_reponse ,RP.message as message_reponse,
        t_etudiant.nom as nom,t_etudiant.prenom as prenom, ac_promotion.ordre as promo , ac_formation.abreviation as forma,R.piece as piece
        from reclamation R 
        inner join fos_user as etud on R.fk_user = etud.id  
        inner join t_admission on etud.username = t_admission.code
        inner join t_inscription on t_admission.id = t_inscription.t_admission_id
        inner join ac_annee on t_inscription.ac_annee_id = ac_annee.id
        inner join ac_promotion on t_inscription.ac_promotion_id = ac_promotion.id
        inner join ac_formation on ac_promotion.formation_id = ac_formation.id
        inner join t_etudiant on t_etudiant.id = etud.t_etudiant_id
        left join reclamation_reponse as RP on RP.fk_reclamation = R.id_reclamation and RP.active = 1
        left join fos_user as user_rec on RP.fk_user = user_rec.id 
        where ac_annee.validation_academique = 'non'".$service_sql."
        order by R.id_reclamation desc";

         $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
         $stmt->execute();
         $reclamations_lenght = $stmt->fetchAll();
         
         $sql1 = $sql . ' LIMIT 0,10;';

         $stmt = $this->getDoctrine()->getConnection()->prepare($sql1);
         $stmt->execute();
         $reclamations = $stmt->fetchAll();

         $sql_user = "SELECT * FROM `fos_user` where email='service' ";

         $stmt = $this->getDoctrine()->getConnection()->prepare($sql_user);
         $stmt->execute();
         $fos_user = $stmt->fetchAll();


         
         return $this->render('reclamation/reclamation.html.twig', [
                'reclamations'=>$reclamations,
                'reclamations_lenght'=>$reclamations_lenght,
                'fos_user'=>$fos_user,

         ]);

         
    }
    else{
         return $this->redirectToRoute('homepage');
        }
    }
    
      
    /**
     * @Route("/voir_plus", name="voir_plus")
     */
    public function voirPlusAction(Request $request) {
        
        if ($this->get('security.authorization_checker')->isGranted('ROLE_RECLAMATION')) {
   
           $breadcrumbs = $this->get("white_october_breadcrumbs");
           $breadcrumbs->addRouteItem("RECLAMATIONS", "reclamation");
           $breadcrumbs->prependRouteItem("Home", "etudiant_index");
   
           $user = $this->getUser();
           
           $repository = $this->getDoctrine()->getRepository('AppBundle:User');
           $userx = $repository->findOneById($user->getId());

           $username = $user->getUsername();
           $service_sql = "" ;
           if($username !=  "reclamation"){
               $service_sql = " and  R.service =".$user->getId() ;
           }
   
         
           $sql = "select R.service as service , RP.valider as valider , R.id_reclamation as id_reclamation, R.message as message , R.object as objet ,DATE_FORMAT(R.created_at,'%d-%m-%Y %H:%i:%s') as created ,
           user_rec.username ,RP.id_reclamation_reponse ,RP.message as message_reponse,
           t_etudiant.nom as nom,t_etudiant.prenom as prenom, ac_promotion.ordre as promo , ac_formation.abreviation as forma,R.piece as piece
           from reclamation R 
           inner join fos_user as etud on R.fk_user = etud.id  
           inner join t_admission on etud.username = t_admission.code
           inner join t_inscription on t_admission.id = t_inscription.t_admission_id
           inner join ac_annee on t_inscription.ac_annee_id = ac_annee.id
           inner join ac_promotion on t_inscription.ac_promotion_id = ac_promotion.id
           inner join ac_formation on ac_promotion.formation_id = ac_formation.id
           inner join t_etudiant on t_etudiant.id = etud.t_etudiant_id
           left join reclamation_reponse as RP on RP.fk_reclamation = R.id_reclamation and RP.active = 1
           left join fos_user as user_rec on RP.fk_user = user_rec.id 
           where ac_annee.validation_academique = 'non' ".$service_sql."
           order by R.id_reclamation desc";
   
            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
            $stmt->execute();
            $reclamations_lenght = $stmt->fetchAll();
            
            $page = $request->request->get('page'); 

            $sql1 = $sql . ' LIMIT '.$page.',100';

            $stmt = $this->getDoctrine()->getConnection()->prepare($sql1);
            $stmt->execute();
            $reclamations = $stmt->fetchAll();
            
            return $this->render('reclamation/voirplus.html.twig', [
                   'reclamations'=>$reclamations,
                   'reclamations_lenght'=>$reclamations_lenght,
                   'page' => $page
            ]);
       }
       else{
            return $this->redirectToRoute('homepage');
           }
       }
       
    
    /**
     * @Route("/insert_admin_reclamation", name="insert_admin_reclamation")
     */
     
    public function insertAdminReclamationAction(Request $request) {
        
            $submittedToken = $request->request->get('token');

            if ($this->isCsrfTokenValid('myform', $submittedToken)) {
                   
                $stmt = $this->getDoctrine()->getConnection()->prepare("
                INSERT INTO reclamation_reponse(message,fk_reclamation,fk_user,created_at,updated_at,deleted_at)
                VALUES (:message,:fk_reclamation,:fk_user,:created_at,:updated_at,:deleted_at)");
                
                $idreclamation = intval($request->request->get('id_reclamation'));
                $userid = intval($this->getUser()->getId());
                
                $stmt->bindParam(':message'             ,  $request->request->get('message') );
                $stmt->bindParam(':fk_reclamation'      ,  $idreclamation);
                $stmt->bindParam(':fk_user'             ,  $userid);
                $stmt->bindParam(':created_at'          ,  date("Y/m/d h:i"));
                $stmt->bindParam(':updated_at'          ,  date("Y/m/d h:i"));
                $stmt->bindParam(':deleted_at'          ,  date("Y/m/d h:i"));

                $stmt->execute();
                
            }
             return $this->redirectToRoute('admin_reclamation');
            
    }
    
    /**
     * @Route("/trier", name="trier")
     */
    public function trier(Request $request) {



    }
    
    /**
     * @Route("/annuler_reclamation", name="annuler_reclamation")
     */
     
    public function AnnulerReclamationAction(Request $request) {
        
 if($request->isXmlHttpRequest()) {

                 $id = $request->request->get('id');    
                   
                $stmt = $this->getDoctrine()->getConnection()->prepare("
                UPDATE `reclamation_reponse` SET `active`= 0 , deleted_at='".date("Y/m/d h:i")."' WHERE id_reclamation_reponse = ".$id);

                $stmt->execute();
                
            }
             return new Response('success');
            
    }
    
    /**
     * @Route("/insert", name="insert")user
     */
     
    public function insertAction(Request $request) {
        
            $submittedToken = $request->request->get('token');

            if ($this->isCsrfTokenValid('myform', $submittedToken)) {
                
                $stmt = $this->getDoctrine()->getConnection()->prepare("
                INSERT INTO question(message,fk_module,fk_etudiant,created_at)
                VALUES (:message,:fk_module,:fk_etudiant,:created_at)");
                $module = intval($request->request->get('module'));
                $stmt->bindParam(':message'           , $request->request->get('message') );
                $stmt->bindParam(':fk_module'         ,  $module);
                $stmt->bindParam(':fk_etudiant'       , $this->getUser()->getId());
                $stmt->bindParam(':created_at'       , date("Y/m/d h:i"));

                $stmt->execute();
                
            }
             return $this->redirectToRoute('question');
            
    }
    
    
    /**
     * @Route("/reclamation_insert", name="reclamation_insert")
     */
    public function enseignantInserttAction(Request $request) {

             $submittedToken = $request->request->get('token');

             if ($this->isCsrfTokenValid('myform', $submittedToken)) {
                 
                    $link = '';
                    $files = $request->files->get('files')['file'];
                    $i = 0;
                    foreach($files as $file){
                        /**
                         * @var UploadedFile $file
                         */
                        $fileName = md5(uniqid()).'.'.$file->guessExtension();
                        if($i == 0){
                            $link = $fileName ;
                        }else{
                            $link = $link .'|'. $fileName ;
                        }
                        
                        $save = $file->move($this->getParameter('brochures_directory'),$fileName);
                        $i=1;
                    }
                
                    $stmt = $this->getDoctrine()->getConnection()->prepare("
                    INSERT INTO reclamation(object,message,fk_user,created_at,updated_at,deleted_at,piece)
                    VALUES (:object,:message,:fk_user,:created_at,:updated_at,:deleted_at,:piece)");
                    
                    $question = intval($request->request->get('id_question'));
                   
                    $stmt->bindParam(':object'               , $request->request->get('objet') );
                    $stmt->bindParam(':message'              , $request->request->get('message'));
                    $stmt->bindParam(':fk_user'              , $this->getUser()->getId());
                    $stmt->bindParam(':created_at'           , date("Y/m/d h:i"));
                    $stmt->bindParam(':updated_at'           , date("Y/m/d h:i"));
                    $stmt->bindParam(':deleted_at'           , date("Y/m/d h:i"));
                    $stmt->bindParam(':piece'                ,  $link);
     
                    $stmt->execute();
                
            }
             return $this->redirectToRoute('reclamation');


    }
    
     /**
     * @Route("/edit_password", name="edit_password")
     */
    public function editPasswordReclamationAction(Request $request) {
             return $this->redirectToRoute('fos_user_change_password');
            //  return $this->render('reclamation/update_mdp.html.twig');
    }
    
    /**
    * @Route("/excel_reclamation", name="excel_reclamation")
    */
    public function excelReclamationAction(Request $request) {
           
            $sql="  select   R.id_reclamation as id_reclamation, R.message as message , R.object as objet ,DATE_FORMAT(R.created_at,'%d-%m-%Y %H:%i:%s') as created ,
                             user_rec.username ,RP.id_reclamation_reponse ,RP.message as message_reponse,t_etudiant.nom as nom, t_etudiant.prenom as prenom,
                             ac_promotion.ordre as promo ,ac_formation.abreviation as forma,ac_etablissement.abreviation as etab ,
                             t_inscription.code_admission as code_admission , t_inscription.id as id_inscription
                             
                     from reclamation R 
                     inner join fos_user as etud on R.fk_user = etud.id  
                     inner join t_admission on etud.username = t_admission.code
                     inner join t_inscription on t_inscription.code_admission = t_admission.code
                     inner join ac_annee on ac_annee.id = t_inscription.ac_annee_id
                     inner join ac_promotion on t_inscription.code_promotion = ac_promotion.code
                     inner join ac_formation on ac_promotion.code_formation = ac_formation.code
                     inner join ac_etablissement on ac_etablissement.id = ac_formation.etablissement_id
                     inner join t_etudiant on t_etudiant.id = etud.t_etudiant_id
                     left join reclamation_reponse as RP on RP.fk_reclamation = R.id_reclamation  and RP.active = 1
                     left join fos_user as user_rec on RP.fk_user = user_rec.id 
                     where ac_annee.validation_academique = 'non'
                     order by R.created_at desc";

            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
            $stmt->execute();
            $reclamations = $stmt->fetchAll();
        
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
                       $sheet->setCellValue('A1', 'id reclamation');
                       $sheet->setCellValue('b1', 'objet');
                       $sheet->setCellValue('c1', 'message');
                       $sheet->setCellValue('d1', 'created');
                       $sheet->setCellValue('e1', 'etat(traitée ou non');
                       $sheet->setCellValue('f1', 'reponse');
                       $sheet->setCellValue('g1', 'nom'); 
                       $sheet->setCellValue('h1', 'prenom'); 
                       $sheet->setCellValue('i1', 'etablissement'); 
                       $sheet->setCellValue('j1', 'formation');
                       $sheet->setCellValue('k1', 'promotion');
                       $sheet->setCellValue('l1', 'code admission');
                       $sheet->setCellValue('m1', 'id inscription');
            
            $i = 1;
            foreach ($reclamations as $reclam){
            
                    //   echo $reclam->('id_reclamation');
                       $i++;
                       $sheet->setCellValue('A'.$i, $reclam['id_reclamation']);
                       $sheet->setCellValue('b'.$i, $reclam['objet']);
                       $sheet->setCellValue('c'.$i, $reclam['message']);
                       $sheet->setCellValue('d'.$i, $reclam['created']);
                    if( !empty($reclam['id_reclamation_reponse'])){
                       $sheet->setCellValue('e'.$i, 'traitée');
                       $sheet->setCellValue('f'.$i, 'message_reponse');
                    }else{
                       $sheet->setCellValue('e'.$i, 'non');
                       $sheet->setCellValue('f'.$i, 'NULL');
                    }
                       $sheet->setCellValue('g'.$i, $reclam['nom']); 
                       $sheet->setCellValue('h'.$i, $reclam['prenom']); 
                       $sheet->setCellValue('i'.$i, $reclam['etab']); 
                       $sheet->setCellValue('j'.$i, $reclam['forma']);
                       $sheet->setCellValue('k'.$i, $reclam['promo']);
                       $sheet->setCellValue('l'.$i, $reclam['code_admission']);
                       $sheet->setCellValue('m'.$i, $reclam['id_inscription']);
                            
            }
            
    
            
            foreach(range('A','Z') as $columnID)
            {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }
    
            $filename = 'toutes_reclamations.xlsx';
            $writer = new Xlsx($spreadsheet);
            $writer->save($filename);
    
            $content = file_get_contents($filename);
            header("Content-Disposition: attachment; filename=".$filename);
            unlink($filename);
            exit($content);
    
            return $this->redirectToRoute('admin_reclamation');
    }

        /**
     * @Route("/filtre", name="filtre")
     */
    public function filtre(Request $request) {

            if($request->isXmlHttpRequest()) {

                 $id = $request->request->get('id');    

                         $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $userx = $repository->findOneById($user->getId());
        if($id=='Repondu'){
         $sql="select R.id_reclamation as id_reclamation, R.message as message , R.object as objet ,DATE_FORMAT(R.created_at,'%d-%m-%Y %H:%i:%s') as created ,
                       user_rec.username ,RP.id_reclamation_reponse ,RP.message as message_reponse,
                       t_etudiant.nom as nom,t_etudiant.prenom as prenom, ac_promotion.ordre as promo , ac_formation.abreviation as forma,R.piece as piece
                        from reclamation R 
                        inner join fos_user as etud on R.fk_user = etud.id  
                        inner join t_admission on etud.username = t_admission.code
                        inner join t_inscription on t_inscription.code_admission = t_admission.code
                        inner join ac_annee on ac_annee.id = t_inscription.ac_annee_id
                        inner join ac_promotion on t_inscription.code_promotion = ac_promotion.code
                        inner join ac_formation on ac_promotion.code_formation = ac_formation.code
                        inner join t_etudiant on t_etudiant.id = etud.t_etudiant_id
                        left join reclamation_reponse as RP on RP.fk_reclamation = R.id_reclamation  and RP.active = 1
                        left join fos_user as user_rec on RP.fk_user = user_rec.id 
                        where ac_annee.validation_academique = 'non' and RP.message IS NOT NULL
                        order by R.id_reclamation desc";
        }elseif($id=='NonRepondu'){
         $sql="select R.id_reclamation as id_reclamation, R.message as message , R.object as objet ,DATE_FORMAT(R.created_at,'%d-%m-%Y %H:%i:%s') as created ,
                       user_rec.username ,RP.id_reclamation_reponse ,RP.message as message_reponse,
                       t_etudiant.nom as nom,t_etudiant.prenom as prenom, ac_promotion.ordre as promo , ac_formation.abreviation as forma,R.piece as piece
                        from reclamation R 
                        inner join fos_user as etud on R.fk_user = etud.id  
                        inner join t_admission on etud.username = t_admission.code
                        inner join t_inscription on t_inscription.code_admission = t_admission.code
                        inner join ac_annee on ac_annee.id = t_inscription.ac_annee_id
                        inner join ac_promotion on t_inscription.code_promotion = ac_promotion.code
                        inner join ac_formation on ac_promotion.code_formation = ac_formation.code
                        inner join t_etudiant on t_etudiant.id = etud.t_etudiant_id
                        left join reclamation_reponse as RP on RP.fk_reclamation = R.id_reclamation  and RP.active = 1
                        left join fos_user as user_rec on RP.fk_user = user_rec.id 
                        where ac_annee.validation_academique = 'non' and RP.message IS NULL
                        order by R.id_reclamation desc";   
        }else{
          $sql="select R.id_reclamation as id_reclamation, R.message as message , R.object as objet ,DATE_FORMAT(R.created_at,'%d-%m-%Y %H:%i:%s') as created ,
                       user_rec.username ,RP.id_reclamation_reponse ,RP.message as message_reponse,
                       t_etudiant.nom as nom,t_etudiant.prenom as prenom, ac_promotion.ordre as promo , ac_formation.abreviation as forma,R.piece as piece
                        from reclamation R 
                        inner join fos_user as etud on R.fk_user = etud.id  
                        inner join t_admission on etud.username = t_admission.code
                        inner join t_inscription on t_inscription.code_admission = t_admission.code
                        inner join ac_annee on ac_annee.id = t_inscription.ac_annee_id
                        inner join ac_promotion on t_inscription.code_promotion = ac_promotion.code
                        inner join ac_formation on ac_promotion.code_formation = ac_formation.code
                        inner join t_etudiant on t_etudiant.id = etud.t_etudiant_id
                        left join reclamation_reponse as RP on RP.fk_reclamation = R.id_reclamation  and RP.active = 1
                        left join fos_user as user_rec on RP.fk_user = user_rec.id 
                        where ac_annee.validation_academique = 'non'
                        order by R.id_reclamation desc";  
        }
         $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
         $stmt->execute();
         $reclamations = $stmt->fetchAll();
         
         return $this->render('reclamation/RC.html.twig', [
                'reclamations'=>$reclamations
         ]);                

            }

    }
    
    
    /**
     * @Route("/IDReclamation", name="IDRec")
     */
    public function IDReclamation(Request $request) {

            if($request->isXmlHttpRequest()) {

                 $id = $request->request->get('id');    

                         $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $userx = $repository->findOneById($user->getId());
        
         $sql="select R.id_reclamation as id_reclamation, R.message as message , R.object as objet ,DATE_FORMAT(R.created_at,'%d-%m-%Y %H:%i:%s') as created ,
                       user_rec.username ,RP.id_reclamation_reponse ,RP.message as message_reponse,
                       t_etudiant.nom as nom,t_etudiant.prenom as prenom, ac_promotion.ordre as promo , ac_formation.abreviation as forma
                        from reclamation R 
                        inner join fos_user as etud on R.fk_user = etud.id  
                        inner join t_admission on etud.username = t_admission.code
                        inner join t_inscription on t_inscription.code_admission = t_admission.code
                        inner join ac_annee on ac_annee.id = t_inscription.ac_annee_id
                        inner join ac_promotion on t_inscription.code_promotion = ac_promotion.code
                        inner join ac_formation on ac_promotion.code_formation = ac_formation.code
                        inner join t_etudiant on t_etudiant.id = etud.t_etudiant_id
                        left join reclamation_reponse as RP on RP.fk_reclamation = R.id_reclamation  and RP.active = 1
                        left join fos_user as user_rec on RP.fk_user = user_rec.id 
                        where ac_annee.validation_academique = 'non' and R.id_reclamation = ".$id."
                        order by R.created_at desc";
        
         $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
         $stmt->execute();
         $reclamations = $stmt->fetchAll();
         
         return $this->render('reclamation/RC.html.twig', [
                'reclamations'=>$reclamations
         ]);                

            }

    }
    
     
    /**
     * @Route("/piece_joint", name="piece")
     */
    public function pieceJointAction(Request $request) {
        
        //   $files = $request->request->get('files');
        //   var_dump($files);
         
         return $this->render('reclamation/piece.html.twig');         
            
    }
    
     /**
     * @Route("/info_piece", name="info_piece")
     */
    public function infoPieceAction(Request $request) {
        
        $_POST['files'];
        $files = $request->files->get('files')['file'];
        // $uploadDir  = $this->getParameter('brochures_directory');
        foreach($files as $file){
            /**
             * @var UploadedFile $file
             */
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $save = $file->move($this->getParameter('brochures_directory'),$fileName);
            if($save){print('ziko');}
            else{print('non');}
        }
        
        return new Response('salut');       
                 
            
    }


     /**
     * @Route("/affecteToService", name="affecteToService")
     */
    public function affecteToService(Request $request) {
        
     
    //    var_dump($request->request->get('service'));

        $arr     = explode(",", $request->request->get('reclamations'));
        $service = $request->request->get('service');

        foreach($arr as $ar ){

            $update ="update `reclamation` set service=".$service." where id_reclamation =".$ar;
            $stmt = $this->getDoctrine()->getConnection()->prepare($update);
            $stmt->execute();
            
        }
        
        return new Response('ok');       
                 
            
    }

    /**
     * @Route("/validerReclamation", name="validerReclamation")
     */
    public function validerReclamation(Request $request) {
        
     
            // var_dump($request->request->get('Reponses'));
    
            $arr     = explode(",", $request->request->get('Reponses'));
    
            foreach($arr as $ar ){
    
                $update ="update `reclamation_reponse` set valider= 1 where id_reclamation_reponse =".$ar;
                $stmt = $this->getDoctrine()->getConnection()->prepare($update);
                $stmt->execute();
                
            }
            
            return new Response('ok');       
                     
                
        }
    
   
    
    }
    
    
    
    
    