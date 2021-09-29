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


class ReclamationQuestionController extends Controller {

     /**
     * @Route("/reclamation_question", name="reclamation_question")
     */
    public function reclamation_question(Request $request) {
            if ($this->get('security.authorization_checker')->isGranted('ROLE_RECLAMATION')) {

        $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $userx = $repository->findOneById($user->getId());
        
        $sql="SELECT mdl.id,mdl.designation FROM ac_module mdl inner join ac_semestre sem on sem.code = mdl.code_semestre inner join pr_programmation pr on sem.code = pr.id_semestre and pr.id_module = mdl.code inner join ac_annee ann on pr.id_annee = ann.code inner join pr_progens pre on pre.code_prog = pr.code where ann.cloture_academique='non' and mdl.type ='N' and mdl.active=1  and sem.designation in ('Semestre 2','Semestre 4','Semestre 6', 'Semestre 8', 'Semestre 10', 'Semestre 12', 'Semestre 14')";
/* and pre.code_enseignant='".$userx."' */
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

        $sql2="SELECT id_question,Q.message as msg_Q,Q.created_at as created_Q,Q.updated_at as updated_Q,
                 t_etudiant.nom as nom_Etud,t_etudiant.prenom as prenom_Etud,R.id_reponse,R.message as msg_R,
                 R.created_at as created_R,R.updated_at as updated_R,p_enseignant.nom as nom_Ens,p_enseignant.prenom as prenom_Ens,
                 p_enseignant.id as id_enseignant , ac_promotion.designation as promo , ac_formation.abreviation as forma
                 

                FROM `question` Q 
                inner join fos_user on Q.fk_etudiant = fos_user.id
                inner join t_admission on t_admission.code = fos_user.username
                inner join t_inscription on t_inscription.code_admission = t_admission.code
                inner join t_preinscription on t_admission.code_preinscription = t_preinscription.code
                inner join t_etudiant on t_preinscription.code_etudiant = t_etudiant.code
                inner join ac_promotion on t_inscription.code_promotion = ac_promotion.code
                inner join ac_formation on ac_promotion.code_formation = ac_formation.code
                left join reponse R on Q.id_question = R.fk_question
                left join p_enseignant on R.fk_enseignant = p_enseignant.id
                where Q.fk_module in (".$mods.")
                order by id_question desc";

                 $stmt2 = $this->getDoctrine()->getConnection()->prepare($sql2);
                 $stmt2->execute();
                 $questions = $stmt2->fetchAll();
                 
        return $this->render('reclamation/QReponse.html.twig', [
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
     * @Route("/IDReclamation_QR", name="IDRec_QR")
     */
    public function IDReclamation_QR(Request $request) {

           

                 $id = $request->request->get('id');    

                         $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $userx = $repository->findOneById($user->getId());
        
         $sql="SELECT id_question,Q.message as msg_Q,Q.created_at as created_Q,Q.updated_at as updated_Q,
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
                where Q.fk_module in (".$mods.") and id_question = ".$id."
                order by id_question desc";
        
         $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
         $stmt->execute();
         $reclamations = $stmt->fetchAll();
         
         return $this->render('question/QR.html.twig', [
                'reclamations'=>$reclamations
         ]);                
            

    }
    
    }
    
    
    
    
    