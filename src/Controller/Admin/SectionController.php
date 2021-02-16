<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Entity\Section;
use App\Form\SectionType;
use App\Repository\SectionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/section")
 * @IsGranted("ROLE_ADMIN")
 */
class SectionController extends AbstractController
{
    /**
     * @Route("/", name="admin_section_index")
     */
    public function index(SectionRepository $sectionRepository): Response
    {
        $list = $sectionRepository->findAll();

        return $this->render('admin/section/index.html.twig', [
            'sections' => $list,
        ]);
    }

    /**
     * Creates a new Section entity.
     *
     * @Route("/new", methods="GET|POST", name="admin_section_new")
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     */
    public function new(Request $request): Response
    {
        $section = new Section();

        // See https://symfony.com/doc/current/form/multiple_buttons.html
        $form = $this->createForm(SectionType::class, $section)
            ->add('saveAndCreateNew', SubmitType::class);

        $form->handleRequest($request);

        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See https://symfony.com/doc/current/forms.html#processing-forms
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($section);
            $em->flush();

            // Flash messages are used to notify the user about the result of the
            // actions. They are deleted automatically from the session as soon
            // as they are accessed.
            // See https://symfony.com/doc/current/controller.html#flash-messages
            $this->addFlash('success', 'section.created_successfully');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('admin_section_new');
            }

            return $this->redirectToRoute('admin_section_index');
        }

        return $this->render('admin/section/new.html.twig', [
            'section' => $section,
            'form' => $form->createView(),
        ]);
    }
//  IsGranted("edit", subject="section", message="Sections can only be edited by their authors.")
    /**
     * Displays a form to edit an existing Section entity.
     *
     * @Route("/{id<\d+>}/edit", methods="GET|POST", name="admin_section_edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Section $section): Response
    {
        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'section.updated_successfully');

            //return $this->redirectToRoute('admin_section_edit', ['id' => $section->getId()]);
            return $this->redirectToRoute('admin_section_index');
        }

        return $this->render('admin/section/edit.html.twig', [
            'section' => $section,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a Section entity.
     *
     * @Route("/{id}/delete", methods="POST", name="admin_section_delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Section $section): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_section_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($section);
        $em->flush();

        $this->addFlash('success', 'section.deleted_successfully');

        return $this->redirectToRoute('admin_section_index');
    }

}
