<?php

namespace App\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Ingeschrevenpersoon;
use App\Entity\Kind;
use App\Entity\Ouder;
use App\Entity\Partner;
use Conduction\CommonGroundBundle\Entity\AuditTrail;
use Conduction\CommonGroundBundle\Service\NLXLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

class FamilieSubscriber implements EventSubscriberInterface
{
    private $params;
    private $em;
    private $serializer;
    private $nlxLogService;

    public function __construct(ParameterBagInterface $params, EntityManagerInterface $em, SerializerInterface $serializer, NLXLogService $nlxLogService)
    {
        $this->params = $params;
        $this->em = $em;
        $this->serializer = $serializer;
        $this->nlxLogService = $nlxLogService;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['getFamilie', EventPriorities::PRE_VALIDATE],
        ];
    }

    public function getFamilie(ViewEvent $event)
    {
        $method = $event->getRequest()->getMethod();
        $route = $event->getRequest()->attributes->get('_route');

        if ($method != 'GET' || (
            !strpos($route, '_ouders') &&
            !strpos($route, '_kinderen') &&
            !strpos($route, '_partners')

            )) {
            return;

        }
        // Lets get the rest of the data
        $result = $event->getControllerResult();
        $contentType = $event->getRequest()->headers->get('accept');
        if (!$contentType) {
            $contentType = $event->getRequest()->headers->get('Accept');
        }
        switch ($contentType) {
            case 'application/json':
                $renderType = 'json';
                break;
            case 'application/ld+json':
                $renderType = 'jsonld';
                break;
            case 'application/hal+json':
                $renderType = 'jsonhal';
                break;
            default:
                $contentType = 'application/json';
                $renderType = 'json';
        }

        $burgerservicenummer = $event->getRequest()->attributes->get('burgerservicenummer');
        $result = $this->em->getRepository(Ingeschrevenpersoon::class)->findOneBy(['burgerservicenummer' => $burgerservicenummer]);
        $itemId = $result->getid();

        if(strpos($route, '_ouders')){
            $results = $this->em->getRepository(Ouder::class)->findBy(['ingeschrevenpersoon'=> $itemId]);
        }
        elseif(strpos($route, '_partners')){
            $results = $this->em->getRepository(Partner::class)->findBy(['ingeschrevenpersoon'=> $itemId]);
        }
        elseif(strpos($route, '_kinderen')){
            $results = $this->em->getRepository(Kind::class)->findBy(['ingeschrevenpersoon'=> $itemId]);
        }

        $response = $this->serializer->serialize(
            $results,
            $renderType,
            ['enable_max_depth'=> true]
        );

        // Creating a response
        $response = new Response(
            $response,
            Response::HTTP_OK,
            ['content-type' => $contentType]
        );

        $event->setResponse($response);
    }
}
