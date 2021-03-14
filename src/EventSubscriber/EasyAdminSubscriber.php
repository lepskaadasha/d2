<?php

namespace App\EventSubscriber;

use App\Entity\Post;
use App\Entity\Section;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => [['setPostSlug', 0], ['setSectionSlug', 0]],
            BeforeEntityUpdatedEvent::class => [['updatePostSlug', 0], ['updateSectionSlug', 0]],
        ];
    }

    public function setPostSlug(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Post)) {
            return;
        }

        $slug = $this->slugger->slug($entity->getTitle())->lower();
        $entity->setSlug($slug);
    }

    public function setSectionSlug(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Section)) {
            return;
        }

        $slug = $this->slugger->slug($entity->getTitle())->lower();
        $entity->setSlug($slug);
    }

    public function updatePostSlug(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Post)) {
            return;
        }

        $slug = $this->slugger->slug($entity->getTitle())->lower();
        $entity->setSlug($slug);
    }

    public function updateSectionSlug(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Section)) {
            return;
        }

        $slug = $this->slugger->slug($entity->getTitle())->lower();
        $entity->setSlug($slug);
    }
}
