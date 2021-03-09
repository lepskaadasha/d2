<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Question;
use App\Entity\Section;
use App\Form\Type\AnswerType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('post', EntityType::class, [
              'class' => Post::class,
              'query_builder' => function (EntityRepository $er) {
                  return $er->createQueryBuilder('p')
                    ->orderBy('p.title', 'ASC');
              },
              'choice_label' => 'title',
            ])
            ->add('answers', CollectionType::class, [
              'entry_type' => AnswerType::class,
              'entry_options' => [
                'attr' => ['class' => 'answer-box'],
              ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
