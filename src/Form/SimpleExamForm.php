<?php

namespace App\Form;

use App\Entity\Answer;
use App\Entity\Exam;
use App\Entity\Question;
use App\Form\Type\AnswerType;
use App\Form\Type\ExamAnswerType;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class SimpleExamForm
 *
 * @package App\Form
 */
class SimpleExamForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $exam = $options['data'];
        if (!$exam instanceof Exam) {
            return;
        }
        /** @var Collection $questions */
        $questions = $exam->getQuestions();

        foreach ($questions as $key => $question) {
            /** @var Question $question */
            $answers = $question->getAnswers()->toArray();
            shuffle($answers);

            $builder->add('answers_for_q' . $question->getId(), ChoiceType::class, [
                'mapped' => false,
                'expanded' => true,
                'multiple' => true,
                'required' => true,
                'choices' => $answers,
                'choice_value' => 'id',
                'choice_label' => function(?Answer $answer) {
                    return $answer ? $answer->getName() : '';
                },
                'constraints' => [
                    new NotBlank(),
                ],
                'placeholder' => 'Select',
                'label' => $question->getTitle(),
            ]);

        }
        $builder->addEventListener(
            FormEvents::SUBMIT,
            array($this, 'onSubmit')
        );

    }

    public function onSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        /** @var Exam $exam */
        $exam = $event->getData();
        /** @var Question[] $questions */
        $questions = $exam->getQuestions();
        foreach ($questions as $question) {
            $key = 'answers_for_q' . $question->getId();
            try {
                $answersForQuestion = $form->get($key)->getData();
                if (!empty($answersForQuestion)) {
                    foreach ($answersForQuestion as $answer) {
                        $exam->addAnswer($answer);
                    }
                }
            }
            catch (\Exception $e) {}
        }
        $exam->setCompleted(new \DateTime());
    }

    public function getBlockPrefix() {
        return 'simpleExamForm';
    }
}
