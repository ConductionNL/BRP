<?php

namespace App\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Ingeschrevenpersoon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

// We intercept the call here so the we can build our own query for the database, that might be a bit over the top now but we need to make a stuf question out of this at a latter moment.

class IngeschrevenpersonenSubscriber implements EventSubscriberInterface
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
            KernelEvents::REQUEST => ['IngeschrevenpersonenQuery', EventPriorities::PRE_READ],
        ];
    }

    public function IngeschrevenpersonenQuery(RequestEvent $event)
    {
        $query = $event->getRequest()->server->get('QUERY_STRING');
        $query = str_replace('__', '.', $query);
        $query = explode('&', $query);
        foreach ($query as $key=> $parameter) {
            $parameter = explode('=', $parameter);
            if ($parameter[0] == 'geboorte.datum') {
                $parameter[1] = str_replace('-', '', $parameter[1]);
            }
            if ($parameter[0] == 'geboorte.plaats') {
                $parameter[0] = 'geboorte.plaats.omschrijving';
            }
            $query[$key] = implode('=', $parameter);
        }
        $query = implode('&', $query);
        $event->getRequest()->server->set('QUERY_STRING', $query);

//        $result = $event->getControllerResult();
//        $method = $event->getRequest()->getMethod();
//
//        $contentType = $event->getRequest()->headers->get('accept');
//
//        if (!$contentType) {
//            $contentType = $event->getRequest()->headers->get('Accept');
//        }
//
//        //var_dump( $event->getRequest()->get('_route')); dsadds adsaadsaadsa
//
//        // Lats make sure that some one posts correctly
//        if (Request::METHOD_GET !== $method || $event->getRequest()->get('_route') != 'api_ingeschrevenpersoons_get_collection') {
//            return;
//        }
//
//        $expand = $event->getRequest()->query->get('expand');
//        $fields = $event->getRequest()->query->get('fields');
//        $burgerservicenummer = strval($event->getRequest()->query->get('burgerservicenummer'));
//        $familieEerstegraad = strval($event->getRequest()->query->get('familie_eerstegraad'));
//        //$familieTweedegraad = strval ($event->getRequest()->query->get('familie_tweedegraad'));
//        //$familieDerdegraad = strval ($event->getRequest()->query->get('familie_derdegraad'));
//        //$familieVierdegraad = strval ($event->getRequest()->query->get('familie_vierdegraad'));
//        $geboorteDatum = $event->getRequest()->query->get('geboorte__datum');
//        $geslachtsaanduiding = $event->getRequest()->query->get('geslachtsaanduiding');
//        $inclusiefoverledenpersonen = $event->getRequest()->query->get('inclusiefoverledenpersonen');
//        $naamGeslachtsnaam = $event->getRequest()->query->get('naam__geslachtsnaam');
//        $naamVoornamen = $event->getRequest()->query->get('naam__voornamen');
//        $naamVoorvoegsel = $event->getRequest()->query->get('naam__voorvoegsel');
//        $verblijfplaatsGemeentevaninschrijving = $event->getRequest()->query->get('verblijfplaats__gemeentevaninschrijving');
//        $verblijfplaatsHuisletter = $event->getRequest()->query->get('verblijfplaats__huisletter');
//        $verblijfplaatsHuisnummer = $event->getRequest()->query->get('verblijfplaats__huisnummer');
//        $verblijfplaatsHuisnummertoevoeging = $event->getRequest()->query->get('verblijfplaats__huisnummertoevoeging');
//        $verblijfplaatsIdentificatiecodenummeraanduiding = $event->getRequest()->query->get('verblijfplaats__identificatiecodenummeraanduiding');
//        $verblijfplaatsNaamopenbareruimte = $event->getRequest()->query->get('verblijfplaats__naamopenbareruimte');
//        $verblijfplaatsPostcode = $event->getRequest()->query->get('verblijfplaats__postcode');
//
//        $qb = $this->em->getRepository(Ingeschrevenpersoon::class)->createQueryBuilder('i')
//        ->leftJoin('i.naam', 'n')
//        ->leftJoin('i.verblijfplaats', 'v');
//
//        if ($burgerservicenummer) {
//            $qb->andWhere('i.burgerservicenummer = :burgerservicenummer')
//            ->setParameter('burgerservicenummer', $burgerservicenummer);
//        }
//
//        if ($verblijfplaatsIdentificatiecodenummeraanduiding) {
//            $qb
//            ->andWhere('v.bagId = :verblijfplaatsIdentificatiecodenummeraanduiding')
//            ->setParameter('verblijfplaatsIdentificatiecodenummeraanduiding', $verblijfplaatsIdentificatiecodenummeraanduiding);
//        }
//
//        if ($familieEerstegraad) {
//            $qb->leftJoin('i.kinderen', 'k')
//            ->leftJoin('i.partners', 'p')
//            ->leftJoin('i.ouders', 'o')
//            ->andWhere($qb->expr()->orX(
//                $qb->expr()->eq('k.burgerservicenummer', ':familieEerstegraad'),
//                $qb->expr()->eq('p.burgerservicenummer', ':familieEerstegraad'),
//                $qb->expr()->eq('o.burgerservicenummer', ':familieEerstegraad')
//            ))
//                ->setParameter('familieEerstegraad', $familieEerstegraad);
//        }
//
//        // Lets set a return content type
//        switch ($contentType) {
//            case 'application/json':
//                $renderType = 'json';
//                break;
//            case 'application/ld+json':
//                $renderType = 'jsonld';
//                break;
//            case 'application/hal+json':
//                $renderType = 'jsonhal';
//                break;
//            default:
//                $contentType = 'application/json';
//                $renderType = 'json';
//        }
//
//        //
//        $results = $qb->getQuery()->getResult();
//
//        // now we need to overide the normal subscriber
//        $json = $this->serializer->serialize(
//            $results,
//            $renderType,
//            ['enable_max_depth' => true]
//        );
//
//        $response = new Response(
//            $json,
//            Response::HTTP_OK,
//            ['content-type' => $contentType]
//        );
//
//        $event->setResponse($response);
    }
}
