<?php

namespace App\Form;

use App\Entity\Exam;
use App\Entity\Question;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CreateSimpleExamForm.
 *
 * @package App\Form
 */
class CreateSimpleExamForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $exam = $options['data'];
        if (!$exam instanceof Exam) {
            return;
        }

    }

    public function getBlockPrefix() {
        return 'createSimpleExamForm';
    }
}
