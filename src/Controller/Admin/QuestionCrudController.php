<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Entity\Question;
use App\Form\Type\AnswerType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class QuestionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Question::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
          ->setEntityLabelInSingular('Question')
          ->setEntityLabelInPlural('Questions')
          // the visible title at the top of the page and the content of the <title> element
          // it can include these placeholders: %entity_id%, %entity_label_singular%, %entity_label_plural%
          ->setPageTitle('index', '%entity_label_plural% listing')

          // you can pass a PHP closure as the value of the title
          ->setPageTitle('new', 'New Question')
          ->setPaginatorPageSize(50)
          // in DETAIL and EDIT pages, the closure receives the current entity
          // as the first argument
          ->setPageTitle('detail', fn (Question $question) => (string) $question->getTitle())
          ->setPageTitle('edit', fn (Question $question) => sprintf('Editing <b>%s</b>', $question->getTitle()))

          // the help message displayed to end users (it can contain HTML tags)
          ->setHelp('edit', '...')
          ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title', 'Question'),
            AssociationField::new('post', 'Post')
              ->autocomplete(),
            CollectionField::new('answers')
              ->setEntryType(AnswerType::class)
              ->setSortable(false)
              ->onlyOnForms(),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
          ->add('title')
          ->add('id')
          ;
    }
}
