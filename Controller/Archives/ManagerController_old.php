<?php

namespace AppBundle\Controller\Archives;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;
use AppBundle\Entity\Major;
use AppBundle\Service\FileUploader3;
use Symfony\Component\HttpFoundation\File\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Acannee controller.
 *
 * @Route("management")
 */
class ManagerController_old extends Controller {

    /**
     * 
     *
     * @Route("/users/", name="management_users")
     * 
     */
    public function indexAction() {
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $query = $repository->createQueryBuilder('u')
                ->where('u.etudiant IS NOT NULL ')
                ->getQuery();
        $users = $query->getResult();


        // dump($users); die();

        return $this->render('management/users.html.twig', array(
                    'users' => $users,
        ));
    }
    
    /**
     * 
     * @Route("/users/validation/", name="validation_users")
     * @Method("GET")
     */
    public function ValidationIndexAction()
    {
        // $em = $this->getDoctrine()->getManager();

        // $videos = $em->getRepository('AppBundle:videos')->findAll();
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $query = $repository->createQueryBuilder('u')
                ->where('u.etudiant IS NOT NULL ')
                ->getQuery();
        $users = $query->getResult();


        // dump($users); die();

        return $this->render('validation/index.html.twig', array(
            'users' => $users,
        ));
    }
    /**
     * 
     *
     * @Route("/users/validation/list",options = { "expose" = true } , name="list_users")
     * @Method({"GET", "POST"})
     */
    public function action() {

        $sql = "SELECT id, utilisateur,  info_valide, nom, prenom FROM t_etudiant1 where info_valide = 1";

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

            $nestedData[] = $row['id'];
            $nestedData[] = $row['utilisateur'];
            $nestedData[] = $row['nom'];
            $nestedData[] = $row['prenom'];            
            $nestedData[] = "<a class='openModel' data-id='".$cd."'><i  class='btn btn-xs btn-primary ace-icon fa fa-plus bigger-120'></i></a>";

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

    /**
     * @Route("/users/validation/getetudiant/{id}" , name="get_etudiant")
     */
    public function updateStatut(Request $request, $id) {
        $sql = "SELECT * FROM t_etudiant1 where id = " .$id. "";

        $stmt = $this->getDoctrine()->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();

        return new Response(json_encode($result));
    }
    

    /**
     * @Route("/users/validation/{id}" , name="validate_etudiant")
     */
    public function validation(Request $request, $id) {
        $sql = "UPDATE t_etudiant1  SET info_valide = 2 where id = " .$id. "";
        $stmt = $this->getDoctrine()->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        if($stmt->execute()) {

            return new JsonResponse(["message"=>"Etudiant Bien Validé"],200);
        }
        else {
            return new JsonResponse(["message"=>"Veuillez contacté l'administrateur"],400);
        }
    }
    /**
     * @Route("/users/validation/deblock" , name="deblock_etudiant")
     */
    public function deblock(Request $request) {

        $id = json_decode($request->getContent(), true)['id'];
        $observation = json_decode($request->getContent(), true)['observation'];

        $em   = $this->getDoctrine()->getManager();
        
        $etudiant  = $em->getRepository('AppBundle:TEtudiantInfo')->find($id);
        $etudiant->setInscriptionValide(0);
        $etudiant->setObservation($observation);
        
        $em->persist($etudiant);
        try {
            $em->flush();
            return new JsonResponse(["message"=>"Etudiant Bien Deblock"],200);
        } catch (\Throwable $th) {
            return new JsonResponse(["message"=>"Veuillez contacté l'administrateur"],400);
        }  
        
        
        // if($stmt->execute()) {

        //     return new JsonResponse(["message"=>"Etudiant Bien Deblock"],200);
        // }
        // else {
        //     return new JsonResponse(["message"=>"Veuillez contacté l'administrateur"],400);
        // }
    }


    /**
     * 
     *
     * @Route("/users/list",options = { "expose" = true } , name="management_users_list")
     * 
     */
    public function UsersListAction() {
        $data = array();
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $query = $repository->createQueryBuilder('u')
                ->where('u.etudiant IS NOT NULL ')
                ->getQuery();
        $users = $query->getResult();


        //var_dump($users);

        foreach ($users as $key => $value) {
            $nestedData = array();
            $nestedData[] = ++$key;
            $nestedData[] = "<input type='checkbox' name = 'form-field-checkbox[]' value='" . $value->getId() . "' class='list_users cat'/>";
            $nestedData[] = $value->getEtudiant()->getNom();
            $nestedData[] = $value->getEtudiant()->getPrenom();
            $nestedData[] = $value->getUserName();
            $nestedData[] = $value->getEmail();
            if ($value->isEnabled() == 1) {
                //  $username     = $value->getUsername();  
                // $nestedData[] = $value->getUser()->getUsername(); 

                $nestedData[] = "<a class = 'active_user' href='1' rel = '" . $value->getId() . "'>  <i class='btn btn-xs btn-success ace-icon fa fa-unlock bigger-120'></i></a>";
            } else {
                $nestedData[] = "<a class = 'active_user' href='0'  rel = '" . $value->getId() . "'><i class='btn btn-xs btn-danger ace-icon fa fa-lock bigger-120'></i></a>";
            }



//            $url = $this->container->get('router')->generate('users_send_mail', array('id' => $value->getId()));
//            $nestedData[] = "<a class='' href='" . $url . "'> <i class='btn btn-xs btn-info  ace-icon fa fa-send-o bigger-120'></i> </a>";

            $url = $this->container->get('router')->generate('management_message_new', array('id' => $value->getId()));
            $nestedData[] = "<a class='' href='" . $url . "'> <i class='btn btn-xs btn-warning ace-icon fa fa-envelope bigger-120'></i> </a>";
            

            $nestedData["DT_RowId"] = $value->getId();
            $data[] = $nestedData;
        }

        $json_data = array(
            "data" => $data
        );


        return new Response(json_encode($json_data));
    }
    
    
    
      /**
     * 
     *
     * @Route("/list",options = { "expose" = true } , name="users_list1")
     * @Method({"GET", "POST"})
     */
    public function listAction() {



        $sql = "SELECT fs.id, etu.nom, etu.prenom, fs.`username`,fs.`enabled`,fs.`email` FROM "
                . "`fos_user` fs INNER JOIN t_etudiant etu on fs.`t_etudiant_id`=etu.id  ";

        //$totalRows .= $sql;
        //  $sqlRequest .= $sql;


        $stmt = $this->getDoctrine()->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();


        $data = array();

        foreach ($result as $key => $row) {

            $nestedData = array();
            $nestedData[] = ++$key;



            $nestedData[] = "<input type='checkbox' name = 'form-field-checkbox[]' value='" . $row['id'] . "' class='list_users cat'/>";
            $cd = $row['id'];


            $nestedData[] = $row['nom'];
            $nestedData[] = $row['prenom'];
            $nestedData[] = $row['username'];

            $nestedData[] = $row['email'];
            if ($row['enabled'] == 1) {
                //  $username     = $value->getUsername();  
                // $nestedData[] = $value->getUser()->getUsername(); 

                $nestedData[] = "<a class = 'active_user' href='1' rel = '" . $row['id'] . "'>  <i class='btn btn-xs btn-success ace-icon fa fa-unlock bigger-120'></i></a>";
            } else {
                $nestedData[] = "<a class = 'active_user' href='0'  rel = '" . $row['id'] . "'><i class='btn btn-xs btn-danger ace-icon fa fa-lock bigger-120'></i></a>";
            }
            
            $url = $this->container->get('router')->generate('management_message_new', array('id' => $row['id']));
            $nestedData[] = "<a class='' href='" . $url . "'> <i class='btn btn-xs btn-warning ace-icon fa fa-envelope bigger-120'></i> </a>";
            
            
            $url = $this->container->get('router')->generate('manager_user_edit', array('id' => $row['id']));
             $nestedData[] = "<a class='' href='" . $url . "'> <i class='btn btn-xs btn-primary ace-icon fa fa-edit bigger-120'></i> </a>";
            
            
            
            $nestedData["DT_RowId"] = $cd;

            $data[] = $nestedData;
        }

        $json_data = array(
            "data" => $data   // total data array
        );


        return new Response(json_encode($json_data));
    }
    
    /**
     * 
     *
     * @Route("/etudiants/", name="management_etudiants")
     * 
     */
    public function EtudiantsAction() {
//        $em = $this->getDoctrine()->getManager();
//        $users = $em->getRepository('AppBundle:User')->findAll();


        $repository = $this->getDoctrine()->getRepository('AppBundle:TEtudiant');
        $query = $repository->createQueryBuilder('etu')
                ->leftJoin('etu.user', 'u')
                ->orderBy('u.etudiant', 'ASC')
                ->getQuery();
        $etudiants = $query->getResult();

        return $this->render('management/etudiants.html.twig', array(
                    'etudiants' => $etudiants,
        ));
    }

    /**
     * 
     *
     * @Route("/etudiants/list",options = { "expose" = true } , name="management_etudiants_list")
     * 
     */
    public function EtudiantsListAction() {
        $data = array();

        $repository = $this->getDoctrine()->getRepository('AppBundle:TEtudiant');

        $query = $repository->createQueryBuilder('etu')
                ->leftJoin('etu.user', 'u')
                ->orderBy('u.etudiant', 'ASC')
                ->getQuery();




        $etudiants = $query->getResult();





        foreach ($etudiants as $key => $value) {


            $nestedData = array();
            $nestedData[] = ++$key;
            $nestedData[] = $value->getNom();
            $nestedData[] = $value->getPrenom();
            $nestedData[] = $value->getMail1();
            $nestedData[] = $value->getSexe();
            if (!empty($value->getUser())) {
                //  $username     = $value->getUsername();  
                // $nestedData[] = $value->getUser()->getUsername(); 
                $nestedData[] = "<span class='badge badge-success'>oui</span>";
            } else {
                $nestedData[] = "<a  class = '" . $value->getId() . "'><span class='badge badge-danger'>non</span></a>";
            }







            $nestedData[] = "";
            $nestedData["DT_RowId"] = $value->getId();
            $data[] = $nestedData;
        }

        $json_data = array(
            "data" => $data
        );


        return new Response(json_encode($json_data));
    }

    /**
     * 
     *
     * @Route("/etudiants/user/add", name="management_etudiants_add_user")
     * 
     */
    public function EtudiantsAddUserAction() {
        $em = $this->getDoctrine()->getManager();
        // $users = $em->getRepository('AppBundle:User');

        $user = new user();
        $user->setUsername('sdk');
        $user->setEmail('sdk');
        $user->setUsernameCanonical('sdk');
        $user->setPassword('passe');
        $em->persist($user);
        $em->flush();

        return null;
    }

    /**
     * 
     *
     * @Route("/users/disable/{id}/{etat}" ,options = { "expose" = true } , name="user_disable_account")
     * 
     */
    public function DisableAccountUserAction($id, $etat) {
        $data = array();
        $text = "";
        if ($etat == 1) {
            $etat = 0;
            $text = "désactivé";
        } else {
            $etat = 1;
            $text = "activé";
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($id);

        $user->setEnabled($etat);
        $em->flush();



        $json_data = array(
            'data' => 'Le Compte ' . $user->getUsername() . ' a été ' . $text . ' avec succes',
        );


        return new Response(json_encode($json_data));
    }

    /**
     * 
     *
     * @Route("/users/default/{id}" ,options = { "expose" = true } , name="user_default")
     * 
     */
    public function DefaultInfosUserAction($id) {

        $year = "";
        $nom = "";


//
//        if (!empty($etudiant->getDateNaissance())) {
//            $serializer = new Serializer(array(new DateTimeNormalizer('Y')));
//            $year = $serializer->normalize($etudiant->getDateNaissance());
//        }
//
//
//        if (!empty($etudiant->getNom())) {
//            $stripped = str_replace(' ', '', $etudiant->getNom());
//            $nom = substr($stripped, 0, 4);
//        }
//
//        $username = $nom . "" . $year . "" . rand(10, 99);


        $em = $this->getDoctrine()->getManager();
        $infos = $em->getRepository('AppBundle:TEtudiant')->find($id);
        //  dump($infos->getPrienscriptions()[0]->getadmissions()[0]->getCode());die();

        $code_admission = "";
        if (!empty($infos)) {
            $code_admission = $infos->getPrienscriptions()[0]->getadmissions()[0]->getCode();
        }


        //  echo $code_admission ; 

        $password = random_int(10000000, 99999999);

        //var_dump((string)$password); 
        $json_data = array(
            'username' => $code_admission,
            'email' => $infos->getMail1(),
            'password' => $password,
        );


        return new Response(json_encode($json_data));
    }

    /**
     * 
     *
     * @Route("/users/sendmail/{id}", name="users_send_mail")
     * 
     */
    public function SendMAilAction($id, \Swift_Mailer $mailer) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneById($id);
        // var_dump($user); die(); 




        $message = (new \Swift_Message('UNIVERSITE INTERNATIONAL ABULCASIS DES SCIENCES DE LA SANTE'))
                ->setFrom('tst@u3s.ma')
                ->setTo($user->getemail())
                ->setBody(
                $this->renderView(
                        // app/Resources/views/Emails/registration.html.twig
                        'Emails/registration.html.twig', array('user' => $user)
                ), 'text/html'
                )
        /*
         * If you also want to include a plaintext version of the message
          ->addPart(
          $this->renderView(
          'Emails/registration.txt.twig',
          array('name' => $name)
          ),
          'text/plain'
          )
         */
        ;

        $mailer->send($message);

        // or, you can also fetch the mailer service this way
        // $this->get('mailer')->send($message);

        return $this->render('Emails/registration.html.twig', array('user' => $user));
    }

    /**
     * Displays a form to edit an existing major entity.
     *
     * @Route("/major/{id}/{bloc}/edit", name="management_major_edit")
     * @Method({"GET", "POST"})
     */
    public function MajorsAction(Request $request, FileUploader3 $fileUploader, Major $major, $id, $bloc) {





        $editForm = $this->createForm('AppBundle\Form\MajorType', $major);
        $editForm->handleRequest($request);



        if ($editForm->isSubmitted() && $editForm->isValid()) {

//        $fileName = $major->getImage();
//        $file = new File($this->getParameter('majors_directory') . '/' . $major->getImage());
//        $major->setImage($file);
//            $file = $major->getImage();
//            $fileName = $fileUploader->upload($file);
            //           $major->setImage($fileName);
            //   $major->setImage($url);

            $this->getDoctrine()->getManager()->flush();


            $this->addFlash(
                    'notice', "L'enregistrement a été effectué"
            );

            return $this->redirectToRoute('management_major');
        }

        return $this->render('management/major_edit.html.twig', array(
                    'major' => $major,
                    'bloc' => $bloc,
                    'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Creates a new major entity.
     *
     * @Route("/major" , name="management_major")
     * @Method({"GET", "POST"})
     */
    public function HomeAction(Request $request) {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Major');
        $majors_level_1 = $repository->findBy(array("level" => 1), array('ordre' => 'ASC'));
        $majors_level_2 = $repository->findBy(array("level" => 2), array('ordre' => 'ASC'));
        $majors_level_3 = $repository->findBy(array("level" => 3), array('ordre' => 'ASC'));
        $majors_level_4 = $repository->findBy(array("level" => 4), array('ordre' => 'ASC'));
        $majors_level_5 = $repository->findBy(array("level" => 5), array('ordre' => 'ASC'));
        $majors_level_6 = $repository->findBy(array("level" => 6), array('ordre' => 'ASC'));
        $majors_level_7 = $repository->findBy(array("level" => 7), array('ordre' => 'ASC'));

        return $this->render('management/major.html.twig', array(
                    'majors_level_1' => $majors_level_1,
                    'majors_level_2' => $majors_level_2,
                    'majors_level_3' => $majors_level_3,
                    'majors_level_4' => $majors_level_4,
                    'majors_level_5' => $majors_level_5,
                    'majors_level_6' => $majors_level_6,
                    'majors_level_7' => $majors_level_7,
        ));
    }

    /**
     * Creates a new major entity.
     *
     * @Route("/major/ordre/{id}/{ordre}" ,options = { "expose" = true } , name="management_major_change_order")
     * @Method({"GET", "POST"})
     */
    public function MajorChangeOrdreAction($id, $ordre) {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Major');
        $major = $repository->find($id);
        $major->setOrdre($ordre);
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        $json_data = array(
            'data' => $id . ' - ' . $ordre,
        );
        return new Response(json_encode($json_data));
    }

    /**
     * 
     *
     * @Route("/users/reset" ,options = { "expose" = true } , name="user_reset_password")
     * 
     */
    public function resetAccountUserAction(Request $request) {

        foreach ($request->request->get('form-field-checkbox') as $key => $value) {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AppBundle:User')->find($value);
            $user->setPassword('$2y$13$XiSyLqZT6h6XROHEBlfTR.ttPE5U3bs4QuFDDoBIwCuwQBMECa/ti');
            $em->persist($user);
        }
        $em->flush();


        $json_data = array(
            'data' => "L'enregistrement a été bien  effectué",
        );


        return new Response(json_encode($json_data));
    }

    /**
     * Displays a form to edit an existing major entity.
     *
     * @Route("/cours", name="management_cours_index")
     * @Method({"GET", "POST"})
     */
    public function CoursFormAction(Request $request) {


        if ($request->isMethod('post') && !is_numeric($request->request->get('cours_id'))) {
            $this->addFlash(
                    'notice', "Veuillez Rensigner un élément pour effectuer cett opération"
            );
            $em = $this->getDoctrine()->getManager();
            $etablissement = $em->getRepository('AppBundle:AcEtablissement')->GetEtablissement(null);
            return $this->render('management/cours_index.html.twig', array('etablissement' => $etablissement));
        } else if ($request->isMethod('post') && is_numeric($request->request->get('cours_id'))) {
            //dump($request->request->get('cours_id')); die();
            return $this->redirectToRoute('management_cours_add', array('id' => $request->request->get('cours_id')));
        }



        $em = $this->getDoctrine()->getManager();
        $etablissement = $em->getRepository('AppBundle:AcEtablissement')->GetEtablissement(null);
        return $this->render('management/cours_index.html.twig', array('etablissement' => $etablissement));
    }

    /**
     * Displays a form to edit an existing major entity.
     *
     * @Route("/cours/add/{id}", name="management_cours_add")
     * @Method({"POST","GET"})
     */
    public function CoursNewAction(Request $request, $id) {


        $cour = new \AppBundle\Entity\Cours();
        $form = $this->createForm('AppBundle\Form\CoursType', $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            
            $em = $this->getDoctrine()->getManager();
            $AcElement = $em->getRepository('AppBundle:AcElement')->find($id);
            $cour->setElement($AcElement);
            $cour->setCodeCours($AcElement->getCode());
            $em->persist($cour);
            $em->flush();




            $this->addFlash(
                    'notice', "L'enregistrement a été effectué"
            );

            
           // dump($cour); die();

           
            return $this->redirectToRoute('management_cours_list');
        }

        return $this->render('management/cours_new.html.twig', array(
                    'cour' => $cour,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing major entity.
     *
     * @Route("/cours/list", name="management_cours_list")
     * 
     */
    public function CoursListAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $cours = $em->getRepository('AppBundle:Cours')->findAll();
        return $this->render('management/cours_list.html.twig', array(
                    'cours' => $cours,
        ));
    }

    /**
     * Deletes a cour entity.
     *
     * @Route("/cours/{id}/delete", name="management_cours_delete")
   
     */
    public function deleteCoursAction(Request $request, $id) {


        $em = $this->getDoctrine()->getManager();
        $cour = $em->getRepository('AppBundle:Cours')->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($cour);
        $em->flush();
        
        $this->addFlash(
                    'notice', "La suppression à été bien effectué"
            );


        return $this->redirectToRoute('management_cours_list');
    }
    
    /**
     * Displays a form to edit an existing major entity.
     *
     * @Route("/videos", name="management_videos_index")
     * @Method({"GET", "POST"})
     */
  
    public function VideosFormAction(Request $request) {


        if ($request->isMethod('post') && !is_numeric($request->request->get('videos_id'))) {
            $this->addFlash(
                    'notice', "Veuillez Rensigner un élément pour effectuer cett opération"
            );
            $em = $this->getDoctrine()->getManager();
            $etablissement = $em->getRepository('AppBundle:AcEtablissement')->GetEtablissement(null);
            return $this->render('management/videos_index.html.twig', array('etablissement' => $etablissement));
        } else if ($request->isMethod('post') && is_numeric($request->request->get('videos_id'))) {
            //dump($request->request->get('videos_id')); die();
            return $this->redirectToRoute('management_videos_add', array('id' => $request->request->get('videos_id')));
        }



        $em = $this->getDoctrine()->getManager();
        $etablissement = $em->getRepository('AppBundle:AcEtablissement')->GetEtablissement(null);
        return $this->render('management/videos_index.html.twig', array('etablissement' => $etablissement));
    }
    

    /**
     * Displays a form to edit an existing major entity.
     *
     * @Route("/videos/add/{id}", name="management_videos_add")
     * @Method({"POST","GET"})
     */
    public function VideosNewAction(Request $request, $id) {


        $cour = new \AppBundle\Entity\Videos();
        $form = $this->createForm('AppBundle\Form\VideosType', $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            
            $em = $this->getDoctrine()->getManager();
            $AcElement = $em->getRepository('AppBundle:AcElement')->find($id);
            $cour->setElement($AcElement);
            $cour->setCodeVideos($AcElement->getCode());
            $em->persist($cour);
            $em->flush();




            $this->addFlash(
                    'notice', "L'enregistrement a été effectué"
            );

            
           // dump($cour); die();

           
            return $this->redirectToRoute('management_videos_list');
        }

        return $this->render('management/videos_new.html.twig', array(
                    'cour' => $cour,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing major entity.
     *
     * @Route("/videos/list", name="management_videos_list")
     * 
     */
    public function VideosListAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $videos = $em->getRepository('AppBundle:Videos')->findAll();
        return $this->render('management/videos_list.html.twig', array(
                    'videos' => $videos,
        ));
    }

    /**
     * Deletes a cour entity.
     *
     * @Route("/videos/{id}/delete", name="management_videos_delete")
   
     */
    public function deleteVideosAction(Request $request, $id) {


        $em = $this->getDoctrine()->getManager();
        $cour = $em->getRepository('AppBundle:Videos')->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($cour);
        $em->flush();
        
        $this->addFlash(
                    'notice', "La suppression à été bien effectué"
            );


        return $this->redirectToRoute('management_videos_list');
    }
    
     /**
     * 
     *
     * @Route("/api", name="management_api")
     * 
     */
    public function apiAction() {
        
        return $this->render('management/api.html.twig', array());
    }
    
    
    
    
        /**
     * Displays a form to edit an existing user entity.
     *@Security("has_role('ROLE_MANAGER')")
     * @Route("/users/{id}/edit", name="manager_user_edit")
     * @Method({"GET", "POST"})
     */
    public function editUserAction(Request $request, User $user)
    {
     
        $editForm = $this->createForm('AppBundle\Form\UserType', $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            
            $this->getDoctrine()->getManager()->flush();

                        $this->addFlash(
                    'notice', "La modification a été effectué"
            );

            return $this->redirectToRoute('management_users');
            
            
          //  return $this->redirectToRoute('user_edit', array('id' => $user->getId()));
        }

        return $this->render('management/user_edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
           
        ));
    }

}
