<?php

namespace App\EventSubscriber;

use App\Repository\ConferenceRepository;
use Twig\Environment;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $conferenceRepository;

    public function __construct(Environment $twig, ConferenceRepository $conferenceRepository)
    {
        $this->twig = $twig;
        $this->conferenceRepository = $conferenceRepository;
    }

    public function onControllerEvent(ControllerEvent $event)
    {
        $this->twig->addGlobal('conferences', $this->conferenceRepository->findAll());
    }

    public static function getSubscribedEvents()
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}
