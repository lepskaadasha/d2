<?php

namespace App\Form;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;

/**
 * Class ExamFlow
 * @package App\Form
 */
class ExamFlow extends FormFlow
{
    /**
     * {@inheritdoc}
     */
    protected $revalidatePreviousSteps = false;

    /**
     * {@inheritdoc}
     */
    protected function loadStepsConfig()
    {
        // $this->getFormData();
        return [
            [
                'label' => 'wheels',
                'form_type' => ExamForm::class,
            ],
            [
                'label' => 'engine',
                'form_type' => ExamForm::class,
            ],
            [
                'label' => 'confirmation',
            ],
        ];
    }
}

