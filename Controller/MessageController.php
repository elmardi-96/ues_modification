<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

/**
 * Message controller.
 *
 * @Route("management/message")
 */
class MessageController extends Controller {

    /**
     * Lists all message entities.
     *
     * @Route("/", name="management_message_index")
     * @Method("GET")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $messages = $em->getRepository('AppBundle:Message')->findAll();

        return $this->render('message/index.html.twig', array(
                    'messages' => $messages,
        ));
    }

    /**
     * Creates a new message entity.
     *
     * @Route("/new/{id}", name="management_message_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $id) {
        $message = new Message();
        $form = $this->createForm('AppBundle\Form\MessageType', $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

            $user_created = new User();
            $user_created = $this->getUser();


            
            $message->setUser($user);
            $message->setUserCreated($user_created);
            $message->setNotification(1); 

            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            $this->addFlash(
                    'notice', 'Votre Message a été Bien Envoyé'
            );

            return $this->redirectToRoute('management_users');
        }

        return $this->render('message/new.html.twig', array(
                    'message' => $message,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a message entity.
     *
     * @Route("/{id}", name="management_message_show")
     * @Method("GET")
     */
    public function showAction(Message $message) {
        $deleteForm = $this->createDeleteForm($message);

        return $this->render('message/show.html.twig', array(
                    'message' => $message,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing message entity.
     *
     * @Route("/{id}/edit", name="management_message_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Message $message) {
        $deleteForm = $this->createDeleteForm($message);
        $editForm = $this->createForm('AppBundle\Form\MessageType', $message);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('management_message_edit', array('id' => $message->getId()));
        }

        return $this->render('message/edit.html.twig', array(
                    'message' => $message,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a message entity.
     *
     * @Route("/{id}", name="management_message_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Message $message) {
        $form = $this->createDeleteForm($message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($message);
            $em->flush();
        }

        return $this->redirectToRoute('management_message_index');
    }

    /**
     * Creates a form to delete a message entity.
     *
     * @param Message $message The message entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Message $message) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('management_message_delete', array('id' => $message->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

    
    
    
    
    
    
    
    
}
