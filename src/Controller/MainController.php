<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Section;
use App\Repository\PostRepository;
use App\Repository\SectionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
    public function sectionShow(Section $section, PostRepository $postRepository)
    {
        $posts = $postRepository->findBySection($section);
        return $this->render('doc/section_index.html.twig', [
            'section' => $section,
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/doc/{section_slug}/{post_slug}", name="post.canonical")
     * @ParamConverter("section", options={"mapping": {"section_slug": "slug"}})
     * @ParamConverter("post", options={"mapping": {"post_slug": "slug"}})
     */
    public function postShow(Section $section, Post $post): Response
    {
        return $this->render('blog/post_show.html.twig', ['post' => $post]);
    }

}
