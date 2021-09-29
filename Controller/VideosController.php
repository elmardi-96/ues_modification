<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Videos;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Videos controller.
 *
 * @Route("videos_crud")
 */
class VideosController extends Controller
{
    /**
     * Lists all cour entities.
     *
     * @Security("has_role('ROLE_MANAGER')")
     * @Route("/", name="videos_crud_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $videos = $em->getRepository('AppBundle:videos')->findAll();

        return $this->render('videos/index.html.twig', array(
            'videos' => $videos,
        ));
    }

    /**
     * Creates a new cour entity.
     *@Security("has_role('ROLE_MANAGER')")
     * @Route("/new", name="videos_crud_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $cour = new Cour();
        $form = $this->createForm('AppBundle\Form\videosType', $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cour);
            $em->flush();

            return $this->redirectToRoute('videos_crud_show', array('id' => $cour->getId()));
        }

        return $this->render('videos/new.html.twig', array(
            'cour' => $cour,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a cour entity.
     *@Security("has_role('ROLE_MANAGER')")
     * @Route("/{id}", name="videos_crud_show")
     * @Method("GET")
     */
    public function showAction(videos $cour)
    {
        $deleteForm = $this->createDeleteForm($cour);

        return $this->render('videos/show.html.twig', array(
            'cour' => $cour,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing cour entity.
     *@Security("has_role('ROLE_MANAGER')")
     * @Route("/{id}/edit", name="videos_crud_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, videos $cour)
    {
        $deleteForm = $this->createDeleteForm($cour);
        $editForm = $this->createForm('AppBundle\Form\videosType', $cour);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('videos_crud_edit', array('id' => $cour->getId()));
        }

        return $this->render('videos/edit.html.twig', array(
            'cour' => $cour,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a cour entity.
     *@Security("has_role('ROLE_MANAGER')")
     * @Route("/{id}", name="videos_crud_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, videos $cour)
    {
        $form = $this->createDeleteForm($cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cour);
            $em->flush();
        }

        return $this->redirectToRoute('videos_crud_index');
    }

    /**
     * Creates a form to delete a cour entity.
     *@Security("has_role('ROLE_MANAGER')")
     * @param videos $cour The cour entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(videos $cour)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('videos_crud_delete', array('id' => $cour->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
