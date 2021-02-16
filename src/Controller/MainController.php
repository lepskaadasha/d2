<?php

namespace App\Controller;

use App\Entity\Section;
use App\Repository\SectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(SectionRepository $sectionRepository): Response
    {
        $sections = $sectionRepository->findAll();
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'sections' => $sections,
        ]);
    }


    /**
     * @Route("/doc/{slug}", methods="GET", name="section.canonical")
     *
     * @param Section $section
     */
    public function sectionShow(Section $section)
    {
        return $this->render('doc/section_index.html.twig', ['section' => $section]);
    }

}
