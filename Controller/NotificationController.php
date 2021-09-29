<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Notification;
use AppBundle\Entity\NotificationFile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Notificationfile controller.
 *
 * @Route("management/notification")
 */
class NotificationController extends Controller {

    /**
     * Lists all notificationFile entities.
     *
     * @Route("/", name="management_notification_index")
     * @Method("GET")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $notifications = $em->getRepository('AppBundle:Notification')->findAll();

        return $this->render('notification/index.html.twig', array(
                    'notifications' => $notifications,
        ));
    }

    /**
     * Creates a new notificationFile entity.
     *
     * @Route("/new", name="management_notification_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
        $notification = new Notification();
        $form = $this->createForm('AppBundle\Form\NotificationType', $notification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();


            $uploads_directory = $this->getParameter('notifications_directory');
            $files = $request->files->get('appbundle_notification')['my_files'];
            $err = 0;

            foreach ($files as $file) {
                $MimeType = array(
                    'application/octet-stream',
                    'application/pdf',
                );


                if ($file->getError() == 1) {
                    $this->addFlash('error', 'le(s) fichier(s) utilisé non supporté(s) par le système ');
                } elseif (!in_array($file->getMimeType(), $MimeType)) {
                    $err++;
                    $this->addFlash('error', 'le(s) fichier(s) utilisé non autorisé');
                } elseif ($file->getSize() > 5000000) {
                    $err++;
                    $this->addFlash('error', 'La taille du fichier ne devrait pas dépasser 5 Mb');
                }

                if ($err > 0) {
                    return $this->render('notification/new.html.twig', array(
                                'notification' => $notification,
                                'form' => $form->createView(),
                    ));
                }


                $filename = md5(uniqid()) . '.' . $file->guessExtension();
                try {
                    $file->move($this->getParameter('notifications_directory'), $filename);
                    $notificationFile = new NotificationFile();
                    $notificationFile->setOriginalName($file->getClientOriginalName());
                    $notificationFile->setImageName($filename);

                    $notificationFile->setNotification($notification);
                    $em->persist($notificationFile);
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
            }



            $notification->setUserCreated($this->container->get('security.token_storage')->getToken()->getUser());

            $em->persist($notification);
            $em->flush();

            // return $this->redirectToRoute('management_notification_show', array('id' => $notification->getId()));

            $em = $this->getDoctrine()->getManager();
            $notifications = $em->getRepository('AppBundle:Notification')->findAll();

            return $this->redirectToRoute('management_notification_index', array('notifications' => $notifications,));
        }

        return $this->render('notification/new.html.twig', array(
                    'notification' => $notification,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a notificationFile entity.
     *
     * @Route("/{id}", name="management_notification_show")
     * @Method("GET")
     */
    public function showAction(Notification $notification) {


        return $this->render('notification/show.html.twig', array(
                    'notification' => $notification,
        ));
    }

    /**
     * Displays a form to edit an existing notificationFile entity.
     *
     * @Route("/{id}/edit", name="management_notification_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Notification $notification) {

        $editForm = $this->createForm('AppBundle\Form\NotificationType', $notification);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {




            $uploads_directory = $this->getParameter('notifications_directory');
            $files = $request->files->get('appbundle_notification')['my_files'];
            $err = 0;

            foreach ($files as $file) {
                $MimeType = array(
                    'application/octet-stream',
                    'application/pdf',
                );


                if ($file->getError() == 1) {
                    $this->addFlash('error', 'le(s) fichier(s) utilisé non supporté(s) par le système ');
                } elseif (!in_array($file->getMimeType(), $MimeType)) {
                    $err++;
                    $this->addFlash('error', 'le(s) fichier(s) utilisé non autorisé');
                } elseif ($file->getSize() > 5000000) {
                    $err++;
                    $this->addFlash('error', 'La taille du fichier ne devrait pas dépasser 5 Mb');
                }

                if ($err > 0) {
                    return $this->render('notification/new.html.twig', array(
                                'notification' => $notification,
                                'form' => $form->createView(),
                    ));
                }


                $filename = md5(uniqid()) . '.' . $file->guessExtension();
                try {
                    $file->move($this->getParameter('notifications_directory'), $filename);
                    $notificationFile = new NotificationFile();
                    $notificationFile->setOriginalName($file->getClientOriginalName());
                    $notificationFile->setImageName($filename);

                    $notificationFile->setNotification($notification);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($notificationFile);
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
            }



      $notification->setUserUpdated($this->container->get('security.token_storage')->getToken()->getUser());

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                    'notice', "La modification  a été bien effectué"
            );
            return $this->redirectToRoute('management_notification_index');

        }

        return $this->render('notification/edit.html.twig', array(
                    'notification' => $notification,
                    'edit_form' => $editForm->createView(),
        ));
    }
    
    
    
    
    
    

   
    
     /**
     * 
     *
     * @Route("/delete/{id}" ,options = { "expose" = true } , name="management_notification_delete")
     * 
     */
    public function deleteAction(Request $request , Notification $notification) {

         $em = $this->getDoctrine()->getManager();
         $em->remove($notification);
         $em->flush();


        $json_data = array(
            'data' => "L'enregistrement a été supprimé avec succées",
        );


        return new Response(json_encode($json_data));
    }

}
