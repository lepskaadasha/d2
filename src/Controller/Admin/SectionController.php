<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Entity\Section;
use App\Form\PostType;
use App\Form\SectionType;
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
    public function index(): Response
    {
        return $this->render('section/index.html.twig', [
            'controller_name' => 'SectionController',
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

}
