<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Section;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\SluggerInterface;

class SectionType extends AbstractType
{
    private $slugger;

    // Form types are services, so you can inject other services in them if needed
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var Section */
                $section = $event->getData();
                if (null !== $sectionTitle = $section->getTitle()) {
                    $section->setSlug($this->slugger->slug($sectionTitle)->lower());
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Section::class,
        ]);
    }


}
