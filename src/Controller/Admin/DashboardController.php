<?php

namespace App\Controller\Admin;

use App\Entity\Attachment;
use App\Entity\Post;
use App\Entity\Question;
use App\Entity\Section;
use App\Entity\Tag;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        // redirect to some CRUD controller
        $routeBuilder = $this->get(AdminUrlGenerator::class);

        return $this->redirect($routeBuilder->setController(PostCrudController::class)->generateUrl());

        // you can also render some template to display a proper Dashboard
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
          // the name visible to end users
          ->setTitle('ТКиОК')

          // the path defined in this method is passed to the Twig asset() function
          ->setFaviconPath('favicon.svg')

          // the domain used by default is 'messages'
          ->setTranslationDomain('my-custom-domain')

          // there's no need to define the "text direction" explicitly because
          // its default value is inferred dynamically from the user locale
          ->setTextDirection('ltr')

          // set this option if you prefer the page content to span the entire
          // browser width, instead of the default design which sets a max width
          ->renderContentMaximized()

          // set this option if you prefer the sidebar (which contains the main menu)
          // to be displayed as a narrow column instead of the default expanded design
          ->renderSidebarMinimized()

          // by default, all backend URLs include a signature hash. If a user changes any
          // query parameter (to "hack" the backend) the signature won't match and EasyAdmin
          // triggers an error. If this causes any issue in your backend, call this method
          // to disable this feature and remove all URL signature checks
          ->disableUrlSignatures()
          ;
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),

            MenuItem::section('Blog'),
            //MenuItem::linkToCrud('Categories', 'fa fa-tags', Tag::class),
            MenuItem::linkToCrud('Articles', 'fa fa-file-text', Post::class),
            MenuItem::linkToCrud('Sections', 'fa fa-puzzle-piece', Section::class),

            MenuItem::section('Question'),
            MenuItem::linkToCrud('Question', 'fa fa-question', Question::class),

            MenuItem::section('Users'),
            MenuItem::linkToCrud('Users', 'fa fa-user', User::class),
            MenuItem::linkToUrl('Visit public website', 'fa fa-star', '/'),
        ];
    }

}
