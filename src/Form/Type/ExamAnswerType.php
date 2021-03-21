<?php

namespace App\Form\Type;

use App\Entity\Exam;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExamAnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $exam = $options['data'];
        if (!$exam instanceof Exam) {
            return;
        }
        $builder->add('answers', ChoiceType::class, [
            'mapped' => false,
            'expanded' => true,
            'multiple' => true,
            'choices' => [
                'One' => 1,
                'Two' => 2,
            ],
            'placeholder' => 'Select',
            'label' => 'Question',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        //$resolver->setDefaults(['data_class' => Answer::class]);
    }
}