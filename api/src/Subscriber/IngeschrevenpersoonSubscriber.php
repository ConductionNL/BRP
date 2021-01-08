<?php

namespace App\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Ingeschrevenpersoon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

class IngeschrevenpersoonSubscriber implements EventSubscriberInterface
{
    private $params;
    private $em;
    private $serializer;

    public function __construct(ParameterBagInterface $params, EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->params = $params;
        $this->em = $em;
        $this->serializer = $serializer;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['IngeschrevenpersoonOnBsn', EventPriorities::PRE_VALIDATE],
        ];
    }

    public function IngeschrevenpersoonOnBsn(ViewEvent $event)
    {
        $result = $event->getControllerResult();
        $burgerservicenummer = $event->getRequest()->attributes->get('burgerservicenummer');
        $contentType = $event->getRequest()->headers->get('accept');
        if (!$contentType) {
            $contentType = $event->getRequest()->headers->get('Accept');
        }
        $method = $event->getRequest()->getMethod();

        // Lats make sure that some one posts correctly
        if (Request::METHOD_GET !== $method || $event->getRequest()->get('_route') != 'api_ingeschrevenpersoons_get_on_bsn_collection') {
            return;
        }

        // Lets set a return content type
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

        $result = $this->em->getRepository(Ingeschrevenpersoon::class)->findOneBy(['burgerservicenummer' => $burgerservicenummer]);

        // now we need to overide the normal subscriber
        if(
            $event->getRequest()->query->has('geefFamilie') &&
            $event->getRequest()->query->get('geefFamilie') == 'true'
        ) {
            $json = $this->serializer->serialize(
                $result,
                $renderType,
                ['enable_max_depth' => true, 'groups' => ['show_family']]
            );
        } else {
            $json = $this->serializer->serialize(
                $result,
                $renderType,
                ['enable_max_depth' => true]
            );
        }

        $response = new Response(
            $json,
            Response::HTTP_OK,
            ['content-type' => $contentType]
        );

        $event->setResponse($response);
    }
}
