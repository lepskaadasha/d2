<?php

namespace App\Form;

use App\Entity\Exam;
use App\Entity\Question;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ExamForm
 *
 * @package App\Form
 */
class ExamForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $exam = $options['data'];
        if (!$exam instanceof Exam) {
            return;
        }
        /** @var Question $question */
        $question = $exam->getQuestions()->get($options['flow_step']);
        $answers = $question->getAnswers()->toArray();
        shuffle($answers);
        $choices = [];
        foreach ($answers as $answer) {
            $choices[$answer->getName()] = $answer->getId();
        }
        $builder->add('answers', ChoiceType::class, [
            'mapped' => false,
            'expanded' => true,
            'multiple' => true,
            'choices' => $choices,
            'placeholder' => 'Select',
        ]);

    }

    public function getBlockPrefix() {
        return 'createExam';
    }
}

