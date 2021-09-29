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


class QuestionReponseController extends Controller {

    /**
     * @Route("/question", name="question")
     */
    public function indexAction(Request $request) {
            if ($this->get('security.authorization_checker')->isGranted('ROLE_ETUDIANT')) {

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("FAQ", "question");
        $breadcrumbs->prependRouteItem("Home", "question");

        $user = $this->getUser();
//        if (is_null($user->getFirstLogin())) {
//            return $this->redirectToRoute('fos_user_change_password');
//        }

        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $userx = $repository->findOneById($user->getId());
        
        $sql="select ac_module.id,ac_module.code,ac_module.designation from t_admission 
              JOIN t_inscription on t_admission.code = t_inscription.code_admission 
              JOIN ac_annee on t_inscription.code_annee = ac_annee.code 
              JOIN ac_formation on ac_annee.code_formation = ac_formation.code 
              JOIN ac_promotion on ac_formation.code = ac_promotion.code_formation and t_inscription.code_promotion = ac_promotion.code
              JOIN ac_semestre on ac_promotion.code = ac_semestre.code_promotion 
              JOIN ac_module on ac_semestre.code = ac_module.code_semestre 
              where ac_annee.cloture_academique='non' and ac_module.type ='N' and t_admission.code='".$userx."' and ac_module.active=1 and ac_semestre.designation in ('Semestre 2','Semestre 4','Semestre 6', 'Semestre 8', 'Semestre 10', 'Semestre 12', 'Semestre 14')";

         $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
         $stmt->execute();
         $modules = $stmt->fetchAll();
         
         $c = count($modules) ;
         $mods='';
         $i=0;
         foreach($modules as $m){
             if($i == $c-1){
             $mods.="'".$m['id']."'";
             }else{
             $mods.="'".$m['id']."',";
             }
            $i++;
         }
         
         $sql2=" SELECT id_question,Q.message as msg_Q,Q.created_at as created_Q,Q.updated_at as updated_Q,
                 t_etudiant.nom as nom_Etud,t_etudiant.prenom as prenom_Etud,R.id_reponse,R.message as msg_R,
                 R.created_at as created_R,R.updated_at as updated_R, 
                 p_enseignant.nom as nom_Ens,p_enseignant.prenom as prenom_Ens

                FROM `question` Q 
                left join reponse R on Q.id_question = R.fk_question
                left join fos_user as etud on Q.fk_etudiant = etud.id
                left join fos_user as ens on R.fk_enseignant = ens.id    
                left join p_enseignant on ens.username = p_enseignant.code
                left join t_admission on t_admission.code = etud.username
                left join t_preinscription on t_admission.code_preinscription = t_preinscription.code
                left join t_etudiant on t_preinscription.code_etudiant = t_etudiant.code
                where Q.fk_module in (".$mods.")
                order by id_question desc";

                 $stmt2 = $this->getDoctrine()->getConnection()->prepare($sql2);
                 $stmt2->execute();
                 $questions = $stmt2->fetchAll();
                 
        return $this->render('question/question.html.twig', [
                    'var' => $user->getId(),
                    'var2' => $userx,
                    'modules' => $modules,
                    'questions'=>$questions
                    ]);
            }else{
                     return $this->redirectToRoute('homepage');
                    }
    }
    
    /**
     * @Route("/mod", name="mod")
     */
    public function mod(Request $request) {

            if($request->isXmlHttpRequest()) {

                 $idmod = $request->request->get('idmod');    
                 $type  = $request->request->get('type');
                         $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $userx = $repository->findOneById($user->getId());
        
        if($type==0){
        
                 $sql="select ac_module.id,ac_module.code,ac_module.designation from t_admission 
              JOIN t_inscription on t_admission.code = t_inscription.code_admission 
              JOIN ac_annee on t_inscription.code_annee = ac_annee.code 
              JOIN ac_formation on ac_annee.code_formation = ac_formation.code 
              JOIN ac_promotion on ac_formation.code = ac_promotion.code_formation and t_inscription.code_promotion = ac_promotion.code
              JOIN ac_semestre on ac_promotion.code = ac_semestre.code_promotion 
              JOIN ac_module on ac_semestre.code = ac_module.code_semestre 
              where ac_annee.cloture_academique='non' and ac_module.type ='N' and t_admission.code='".$userx."' and ac_module.active=1 and ac_semestre.designation in ('Semestre 2','Semestre 4','Semestre 6', 'Semestre 8', 'Semestre 10', 'Semestre 12', 'Semestre 14')";
}else if($type==1){
    $sql="SELECT mdl.id,mdl.designation FROM ac_module mdl inner join ac_semestre sem on sem.code = mdl.code_semestre inner join pr_programmation pr on sem.code = pr.id_semestre and pr.id_module = mdl.code inner join ac_annee ann on pr.id_annee = ann.code inner join pr_progens pre on pre.code_prog = pr.code where ann.cloture_academique='non' and mdl.type ='N' and mdl.active=1 and pre.code_enseignant='".$userx."' and sem.designation in ('Semestre 2','Semestre 4','Semestre 6', 'Semestre 8', 'Semestre 10', 'Semestre 12', 'Semestre 14')";
}
         $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
         $stmt->execute();
         $modules = $stmt->fetchAll();
         
         $c = count($modules) ;
         $mods='';
         $i=0;
         foreach($modules as $m){
             if($i == $c-1){
             $mods.="'".$m['id']."'";
             }else{
             $mods.="'".$m['id']."',";
             }
            $i++;
         }
         
            if($idmod==0){
                 $sql2=" SELECT id_question,Q.message as msg_Q,Q.created_at as created_Q,Q.updated_at as updated_Q,
                 t_etudiant.nom as nom_Etud,t_etudiant.prenom as prenom_Etud,R.id_reponse,R.message as msg_R,
                 R.created_at as created_R,R.updated_at as updated_R,p_enseignant.nom as nom_Ens,p_enseignant.prenom as prenom_Ens

                FROM `question` Q 
                left join reponse R on Q.id_question = R.fk_question
                left join p_enseignant on R.fk_enseignant = p_enseignant.id
                left join fos_user on Q.fk_etudiant = fos_user.id
                left join t_admission on t_admission.code = fos_user.username
                left join t_preinscription on t_admission.code_preinscription = t_preinscription.code
                left join t_etudiant on t_preinscription.code_etudiant = t_etudiant.code
                where Q.fk_module in (".$mods.")
                order by id_question desc";
                
}else{
                //   $sql2=" SELECT id_question,Q.message as msg_Q,Q.created_at as created_Q,Q.updated_at as updated_Q,
                //  t_etudiant.nom as nom_Etud,t_etudiant.prenom as prenom_Etud,R.id_reponse,R.message as msg_R,
                //  R.created_at as created_R,R.updated_at as updated_R,p_enseignant.nom as nom_Ens,p_enseignant.prenom as prenom_Ens

                // FROM `question` Q 
                // left join reponse R on Q.id_question = R.fk_question
                // left join p_enseignant on R.fk_enseignant = p_enseignant.id
                // left join fos_user on Q.fk_etudiant = fos_user.id
                // left join t_admission on t_admission.code = fos_user.username
                // left join t_preinscription on t_admission.code_preinscription = t_preinscription.code
                // left join t_etudiant on t_preinscription.code_etudiant = t_etudiant.code
                // where Q.fk_module=".$idmod."
                // order by id_question desc";  
                
                $sql2="SELECT id_question,Q.message as msg_Q,Q.created_at as created_Q,Q.updated_at as updated_Q,
                 t_etudiant.nom as nom_Etud,t_etudiant.prenom as prenom_Etud,R.id_reponse,R.message as msg_R,
                 R.created_at as created_R,R.updated_at as updated_R,p_enseignant.nom as nom_Ens,p_enseignant.prenom as prenom_Ens,
                 p_enseignant.id as id_enseignant , ac_promotion.designation as promo , ac_formation.abreviation as forma
                 

                FROM `question` Q 
                left join reponse R on Q.id_question = R.fk_question
                left join p_enseignant on R.fk_enseignant = p_enseignant.id
                left join fos_user on Q.fk_etudiant = fos_user.id
                left join t_admission on t_admission.code = fos_user.username
                left join t_inscription on t_inscription.code_admission = t_admission.code
                left join t_preinscription on t_admission.code_preinscription = t_preinscription.code
                left join t_etudiant on t_preinscription.code_etudiant = t_etudiant.code
                left join ac_promotion on t_inscription.code_promotion = ac_promotion.code
                left join ac_formation on ac_promotion.code_formation = ac_formation.code
                where Q.fk_module=".$idmod."
                order by id_question desc";
}
                 $stmt2 = $this->getDoctrine()->getConnection()->prepare($sql2);
                 $stmt2->execute();
                 $questions = $stmt2->fetchAll();
                 
                 return $this->render('question/QR.html.twig', [
                       'questions'=>$questions,
                       'type'=>$type
                ]);

            }

    }
    
    /**
     * @Route("/insert", name="insert")
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
     * @Route("/enseignant", name="enseignant")
     */
    public function enseignant(Request $request) {
            if ($this->get('security.authorization_checker')->isGranted('ROLE_ENSEIGNANT')) {

                 $user = $this->getUser();
//        if (is_null($user->getFirstLogin())) {
//            return $this->redirectToRoute('fos_user_change_password');
//        }

        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $userx = $repository->findOneById($user->getId());
        
        $sql="SELECT mdl.id,mdl.designation FROM ac_module mdl inner join ac_semestre sem on sem.code = mdl.code_semestre inner join pr_programmation pr on sem.code = pr.id_semestre and pr.id_module = mdl.code inner join ac_annee ann on pr.id_annee = ann.code inner join pr_progens pre on pre.code_prog = pr.code where ann.cloture_academique='non' and mdl.type ='N' and mdl.active=1 and pre.code_enseignant='".$userx."' and sem.designation in ('Semestre 2','Semestre 4','Semestre 6', 'Semestre 8', 'Semestre 10', 'Semestre 12', 'Semestre 14')";

         $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
         $stmt->execute();
         $modules = $stmt->fetchAll();
                 
         $c = count($modules) ;
         $mods='';
         $i=0;
         foreach($modules as $m){
             if($i == $c-1){
             $mods.="'".$m['id']."'";
             }else{
             $mods.="'".$m['id']."',";
             }
            $i++;
         }
         
        //  $sql2=" SELECT id_question,Q.message as msg_Q,Q.created_at as created_Q,Q.updated_at as updated_Q,
        //          t_etudiant.nom as nom_Etud,t_etudiant.prenom as prenom_Etud,R.id_reponse,R.message as msg_R,
        //          R.created_at as created_R,R.updated_at as updated_R,p_enseignant.nom as nom_Ens,p_enseignant.prenom as prenom_Ens,
        //          p_enseignant.id as id_enseignant
                 

        //         FROM `question` Q 
        //         left join reponse R on Q.id_question = R.fk_question
        //         left join p_enseignant on R.fk_enseignant = p_enseignant.id
        //         left join fos_user on Q.fk_etudiant = fos_user.id
        //         left join t_admission on t_admission.code = fos_user.username
        //         left join t_preinscription on t_admission.code_preinscription = t_preinscription.code
        //         left join t_etudiant on t_preinscription.code_etudiant = t_etudiant.code
        //         where Q.fk_module in (".$mods.")
        //         order by id_question desc";
        
        $sql2="SELECT id_question,Q.message as msg_Q,Q.created_at as created_Q,Q.updated_at as updated_Q,
                 t_etudiant.nom as nom_Etud,t_etudiant.prenom as prenom_Etud,R.id_reponse,R.message as msg_R,
                 R.created_at as created_R,R.updated_at as updated_R,p_enseignant.nom as nom_Ens,p_enseignant.prenom as prenom_Ens,
                 p_enseignant.id as id_enseignant , ac_promotion.designation as promo , ac_formation.abreviation as forma
                 

                FROM `question` Q 
                left join reponse R on Q.id_question = R.fk_question
                left join p_enseignant on R.fk_enseignant = p_enseignant.id
                left join fos_user on Q.fk_etudiant = fos_user.id
                left join t_admission on t_admission.code = fos_user.username
                left join t_inscription on t_inscription.code_admission = t_admission.code
                left join t_preinscription on t_admission.code_preinscription = t_preinscription.code
                left join t_etudiant on t_preinscription.code_etudiant = t_etudiant.code
                left join ac_promotion on t_inscription.code_promotion = ac_promotion.code
                left join ac_formation on ac_promotion.code_formation = ac_formation.code
                where Q.fk_module in (".$mods.")
                order by id_question desc";

                 $stmt2 = $this->getDoctrine()->getConnection()->prepare($sql2);
                 $stmt2->execute();
                 $questions = $stmt2->fetchAll();
                 
        return $this->render('question/reponse.html.twig', [
                    'var' => $user->getId(),
                    'var2' => $userx,
                    'modules' => $modules,
                    'questions'=>$questions
                    ]);
                    }else{
                     return $this->redirectToRoute('homepage');
                    }

    }
    
    /**
     * @Route("/e_insert", name="enseignant_insert")
     */
    public function enseignantInserttAction(Request $request) {
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Enseignant", "enseignant");
            $breadcrumbs->prependRouteItem("Home", "enseignant");

             $submittedToken = $request->request->get('token');

             if ($this->isCsrfTokenValid('myform', $submittedToken)) {
                
                    $stmt = $this->getDoctrine()->getConnection()->prepare("
                    INSERT INTO reponse(message,fk_enseignant,fk_question,created_at)
                    VALUES (:message,:fk_enseignant,:fk_question,:created_at)");
                    
                    $question = intval($request->request->get('id_question'));
                   
                    $stmt->bindParam(':message'               , $request->request->get('message') );
                    $stmt->bindParam(':fk_enseignant'         , $this->getUser()->getId());
                    $stmt->bindParam(':fk_question'           , $question);
                    $stmt->bindParam(':created_at'            , date("Y/m/d h:i"));
     
                    $stmt->execute();
                
            }
             return $this->redirectToRoute('enseignant');


    }
    
    
    }
    
    
    
    
    