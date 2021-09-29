<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Major;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Major controller.
 *
 * @Route("management/crud/major")
 */
class MajorController extends Controller
{
    /**
     * Lists all major entities.
     *@Security("has_role('ROLE_MANAGER')")
     * @Route("/", name="major_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $majors = $em->getRepository('AppBundle:Major')->findAll();

        return $this->render('major/index.html.twig', array(
            'majors' => $majors,
        ));
    }

    /**
     * Creates a new major entity.
     *@Security("has_role('ROLE_MANAGER')")
     * @Route("/new", name="major_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $major = new Major();
        $form = $this->createForm('AppBundle\Form\MajorType', $major);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($major);
            $em->flush();

            return $this->redirectToRoute('major_show', array('id' => $major->getId()));
        }

        return $this->render('major/new.html.twig', array(
            'major' => $major,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a major entity.
     *@Security("has_role('ROLE_MANAGER')")
     * @Route("/{id}", name="major_show")
     * @Method("GET")
     */
    public function showAction(Major $major)
    {
        $deleteForm = $this->createDeleteForm($major);

        return $this->render('major/show.html.twig', array(
            'major' => $major,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing major entity.
     *@Security("has_role('ROLE_MANAGER')")
     * @Route("/{id}/edit", name="major_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Major $major)
    {
        $deleteForm = $this->createDeleteForm($major);
        $editForm = $this->createForm('AppBundle\Form\MajorType', $major);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('major_edit', array('id' => $major->getId()));
        }

        return $this->render('major/edit.html.twig', array(
            'major' => $major,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a major entity.
     *@Security("has_role('ROLE_MANAGER')")
     * @Route("/{id}", name="major_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Major $major)
    {
        $form = $this->createDeleteForm($major);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($major);
            $em->flush();
        }

        return $this->redirectToRoute('major_index');
    }

    /**
     * Creates a form to delete a major entity.
     *@Security("has_role('ROLE_MANAGER')")
     * @param Major $major The major entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Major $major)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('major_delete', array('id' => $major->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
