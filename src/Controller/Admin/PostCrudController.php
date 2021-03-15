<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Form\Type\AttachmentType;
use App\Form\Type\PresentationType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the visible title at the top of the page and the content of the <title> element
            // it can include these placeholders: %entity_id%, %entity_label_singular%, %entity_label_plural%
            ->setPageTitle('index', '%entity_label_plural% listing')

            // you can pass a PHP closure as the value of the title
            ->setPageTitle('new', fn () => new \DateTime('now') > new \DateTime('today 13:00') ? 'New dinner' : 'New lunch')

            // in DETAIL and EDIT pages, the closure receives the current entity
            // as the first argument
            ->setPageTitle('detail', fn (Post $post) => (string) $post->getTitle())
            ->setPageTitle('edit', fn (Post $post) => sprintf('Editing <b>%s</b>', $post->getTitle()))

            // the help message displayed to end users (it can contain HTML tags)
            ->setHelp('edit', '...')

            ->setPaginatorPageSize(50)
          ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnIndex(),
            TextField::new('title'),
            AssociationField::new('section'),
            CollectionField::new('attachments')
                ->setEntryType(AttachmentType::class)
                ->onlyOnForms(),
            CollectionField::new('presentations')
                ->setEntryType(PresentationType::class)
                ->onlyOnForms(),
            TextEditorField::new('content')
                ->hideOnIndex(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(CRUD::PAGE_INDEX, 'detail');
    }


}
