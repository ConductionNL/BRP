<?php

namespace App\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Ingeschrevenpersoon;
use App\Service\StUFService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

class StufSubscriber implements EventSubscriberInterface
{
    private $params;
    private $em;
    private $serializer;
    private StUFService $stUFService;

    public function __construct(ParameterBagInterface $params, EntityManagerInterface $em, SerializerInterface $serializer, StUFService $stUFService)
    {
        $this->params = $params;
        $this->em = $em;
        $this->serializer = $serializer;
        $this->stUFService = $stUFService;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['Ingeschrevenpersoon', EventPriorities::PRE_VALIDATE],
        ];
    }

    public function Ingeschrevenpersoon(ViewEvent $event)
    {
        $method = $event->getRequest()->getMethod();

        // Lats make sure that some one posts correctly
        if (Request::METHOD_GET !== $method || $this->params->get('mode') != 'StUF') {
            return;
        }
        $event->setResponse($this->stUFService->getResults($event->getRequest(), $this->serializer));
    }
}
