<?php

namespace App\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Service\GbavService;
use App\Service\StUFService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

class WoonplaatsHistorieSubscriber implements EventSubscriberInterface
{
    private ParameterBagInterface $parameterBag;
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private GbavService $gbavService;

    public function __construct(ParameterBagInterface $parameterBag, EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->parameterBag = $parameterBag;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->gbavService = new GbavService($this->parameterBag);
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
        $route = $event->getRequest()->attributes->get('_route');

        // Lats make sure that some one posts correctly
        if (Request::METHOD_GET !== $method || $route != 'api_ingeschrevenpersoons_get_woongeschiedenis_collection') {
            return;
        } elseif ($this->parameterBag->get('mode') != 'StUF'){
            return;
            //@TODO: We could support this in fixture mode also, by the means of time travel
        }
        $event->setResponse($this->gbavService->getWoongeschiedenis($event->getRequest(), $this->serializer));
    }
}
