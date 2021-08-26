<?php

namespace App\Service;

use App\Entity\AangaanHuwelijkPartnerschap;
use App\Entity\Geboorte;
use App\Entity\Gezagsverhouding;
use App\Entity\Ingeschrevenpersoon;
use App\Entity\Kind;
use App\Entity\NaamPersoon;
use App\Entity\OpschortingBijhouding;
use App\Entity\Ouder;
use App\Entity\Overlijden;
use App\Entity\Partner;
use App\Entity\VerblijfBuitenland;
use App\Entity\Verblijfplaats;
use App\Entity\Verblijfstitel;
use Conduction\CommonGroundBundle\Service\SerializerService;
use Conduction\CommonGroundBundle\ValueObject\IncompleteDate;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class StUFService
{
    private ParameterBagInterface $parameterBag;
    private Client $client;
    private XmlEncoder $xmlEncoder;
    private EntityManagerInterface $entityManager;
    private LtcService $ltcService;
    private LayerService $layerService;

    public function __construct(LayerService $layerService)
    {
        $this->layerService = $layerService;
        $this->entityManager = $layerService->getEntityManager();
        $this->parameterBag = $layerService->getParameterBag();
        $this->ltcService = new LtcService($layerService->getCommonGroundService());

        //If the mode is not StUF, then we will not need to configure this service.
        if ($this->parameterBag->get('mode') != 'StUF') {
            return;
        }

        if ($this->parameterBag->has('stuf_uri')) {
            $baseUri = $this->parameterBag->get('stuf_uri');
        } else {
            throw new Exception('The base uri for the StUF requests has not been configured. This base uri is required for this mode');
        }

        if (
            !$this->parameterBag->has('app_certificate') ||
            !$this->parameterBag->has('app_ssl_key') ||
            !file_exists($this->parameterBag->get('app_certificate')) ||
            !file_exists($this->parameterBag->get('app_ssl_key'))
        ) {
            throw new Exception('The SSL certificate for two sided SSL have not been configured. The SSL certificate is required for this mode');
        }
        $this->headers = [
            'Content-Type' => 'text/xml',
            'SOAPAction'   => '"http://www.egem.nl/StUF/sector/bg/0310/npsLv01"',
        ];
        $this->guzzleConfig = [
            'http_errors' => false,
            'timeout'     => 4000.0,
            'headers'     => $this->headers,
            'verify'      => true,
            'cert'        => $this->parameterBag->get('app_certificate'),
            'ssl_key'     => $this->parameterBag->get('app_ssl_key'),
            'base_uri'    => $baseUri,
        ];

        $this->client = new Client($this->guzzleConfig);

        $this->xmlEncoder = new XmlEncoder(['xml_root_node_name' => 'soap:Envelope']);
    }

    public function createStufMessage(Request $request)
    {
        $time = new DateTime('now');
        $message = [
            '@xmlns:soap' => 'http://schemas.xmlsoap.org/soap/envelope/',
            '@xmlns:StUF' => 'http://www.egem.nl/StUF/StUF0301',
            '@xmlns:ns'   => 'http://www.egem.nl/StUF/sector/bg/0310',
            '@xmlns:xsi'  => 'http://www.w3.org/2001/XMLSchema-instance',
            'soap:Body'   => [

                'ns:npsLv01' => [
                    'ns:stuurgegevens' => [
                        'StUF:berichtcode'     => 'Lv01',
                        'StUF:zender'          => [
                            'StUF:organisatie'  => '0405',
                            'StUF:applicatie'   => 'WAAR',
                            'StUF:gebruiker'    => 'waardepapieren',
                        ],
                        'StUF:ontvanger' => [
                            'StUF:organisatie'  => '0405',
                            'StUF:applicatie'   => 'DDS',
                        ],
                        'StUF:tijdstipBericht'  => $time->format('YmdHisv'),
                        'StUF:entiteittype'     => 'NPS',

                    ],
                    'ns:parameters' => [
                        'StUF:sortering'                => 1,
                        'StUF:indicatorVervolgvraag'    => 'false',
                        'StUF:maximumAantal'            => 30,
                    ],
                    'ns:gelijk' => [
                        '@StUF:entiteittype'                            => 'NPS',
                        'ns:inp.bsn'                                    => $request->attributes->has('burgerservicenummer') ? $request->attributes->get('burgerservicenummer') : ($request->query->has('burgerservicenummer') ? $request->query->get('burgerservicenummer') : null),
                        'ns:geslachtsnaam'                              => $request->query->has('naam_geslachtsnaam') ? $request->query->get('naam_geslachtsnaam') : null,
                        'ns:voorvoegselGeslachtsnaam'                   => $request->query->has('naam_voorvoegsel') ? $request->query->get('naam_voorvoegsel') : null,
                        'ns:voornamen'                                  => $request->query->has('naam_voornamen') ? $request->query->get('naam_voornamen') : null,
                        'ns:geslachtsaanduiding'                        => $request->query->has('geslachtsaanduiding') ? $request->query->get('geslachtsaanduiding') : null,
                        'ns:geboortedatum'                              => $request->query->has('geboorte_datum') ? $request->query->get('geboorte_datum') : null,
                        'ns:inp.geboorteplaats'                         => $request->query->has('geboorte_plaats') ? $request->query->get('geboorte_plaats') : null,
                        'ns:verblijfsadres'                             => [
                            'ns:aoa.identificatie'               => $request->query->has('verblijfplaats_nummeraanduidingIdentificatie') ? $request->query->get('verblijfplaats_nummeraanduidingIdentificatie') : null,
                            'ns:wpl.identificatie'               => $request->query->has('verblijfplaats_gemeenteVanInschrijving') ? $request->query->get('verblijfplaats_gemeenteVanInschrijving') : null,
                            'ns:gor.straatnaam'                  => $request->query->has('verblijfplaats_straat') ? $request->query->get('verblijfplaats_straat') : null,
                            'ns:aoa.postcode'                    => $request->query->has('verblijfplaats_postcode') ? $request->query->get('verblijfplaats_postcode') : null,
                            'ns:aoa.huisnummer'                  => $request->query->has('verblijfplaats_huisnummer') ? $request->query->get('verblijfplaats_huisnummer') : null,
                            'ns:aoa.huisletter'                  => $request->query->has('verblijfplaats_huisletter') ? $request->query->get('verblijfplaats_huisletter') : null,
                            'ns:aoa.huisnummertoevoeging'        => $request->query->has('verblijfplaats_huisnummertoevoeging') ? $request->query->get('verblijfplaats_huisnummertoevoeging') : null,
                        ],
                    ],
                    'ns:scope' => [
                        'ns:object' => [
                            '@StUF:entiteittype'                    => 'NPS',
                            'ns:inp.bsn'                            => ['@xsi:nil' => 'true'],
                            'ns:geslachtsnaam'                      => ['@xsi:nil' => 'true'],
                            'ns:voorvoegselGeslachtsnaam'           => ['@xsi:nil' => 'true'],
                            'ns:voorletters'                        => ['@xsi:nil' => 'true'],
                            'ns:voornamen'                          => ['@xsi:nil' => 'true'],
                            'ns:aanhefAanschrijving'                => ['@xsi:nil' => 'true'],
                            'ns:voornamenAanschrijving'             => ['@xsi:nil' => 'true'],
                            'ns:geslachtsnaamAanschrijving'         => ['@xsi:nil' => 'true'],
                            'ns:adellijkeTitelPredikaat'            => ['@xsi:nil' => 'true'],
                            'ns:geslachtsaanduiding'                => ['@xsi:nil' => 'true'],
                            'ns:geboortedatum'                      => ['@xsi:nil' => 'true'],
                            'ns:inp.geboorteplaats'                 => ['@xsi:nil' => 'true'],
                            'ns:inp.geboorteLand'                   => ['@xsi:nil' => 'true'],
                            'ns:overlijdensdatum'                   => ['@xsi:nil' => 'true'],
                            'ns:inp.overlijdenplaats'               => ['@xsi:nil' => 'true'],
                            'ns:inp.overlijdenLand'                 => ['@xsi:nil' => 'true'],
                            'ns:verblijfsadres'                     => [
                                'ns:aoa.identificatie'              => ['@xsi:nil' => 'true'],
                                'ns:wpl.identificatie'              => ['@xsi:nil' => 'true'],
                                'ns:wpl.woonplaatsNaam'             => ['@xsi:nil' => 'true'],
                                'ns:gor.openbareRuimteNaam'         => ['@xsi:nil' => 'true'],
                                'ns:gor.straatnaam'                 => ['@xsi:nil' => 'true'],
                                'ns:aoa.postcode'                   => ['@xsi:nil' => 'true'],
                                'ns:aoa.huisnummer'                 => ['@xsi:nil' => 'true'],
                                'ns:aoa.huisletter'                 => ['@xsi:nil' => 'true'],
                                'ns:aoa.huisnummertoevoeging'       => ['@xsi:nil' => 'true'],
                                'ns:inp.locatiebeschrijving'        => ['@xsi:nil' => 'true'],
                                'ns:begindatumVerblijf'             => ['@xsi:nil' => 'true'],
                            ],
                            'ns:sub.verblijfBuitenland' => [
                                'ns:lnd.landcode'                   => ['@xsi:nil' => 'true'],
                                'ns:sub.adresBuitenland1'           => ['@xsi:nil' => 'true'],
                                'ns:sub.adresBuitenland2'           => ['@xsi:nil' => 'true'],
                                'ns:sub.adresBuitenland3'           => ['@xsi:nil' => 'true'],
                            ],
                            'ns:inp.gemeenteVanInschrijving'            => ['@xsi:nil' => 'true'],
                            'ns:inp.datumInschrijving'                  => ['@xsi:nil' => 'true'],
                            'ns:vbt.aanduidingVerblijfstitel'           => ['@xsi:nil' => 'true'],
                            'ns:ing.datumVerkrijgingVerblijfstitel'     => ['@xsi:nil' => 'true'],
                            'ns:ing.datumVerliesVerblijfstitel'         => ['@xsi:nil' => 'true'],
                            'ns:inp.datumVestigingInNederland'          => ['@xsi:nil' => 'true'],
                            'ns:inp.immigratieLand'                     => ['@xsi:nil' => 'true'],
                            'ns:ing.aanduidingUitgeslotenKiesrecht'     => ['@xsi:nil' => 'true'],
                            'ns:ing.indicatieGezagMinderjarige'         => ['@xsi:nil' => 'true'],
                            'ns:ing.indicatieCurateleRegister'          => ['@xsi:nil' => 'true'],
                            'ns:inp.datumOpschortingBijhouding'         => ['@xsi:nil' => 'true'],
                            'ns:inp.redenOpschortingBijhouding'         => ['@xsi:nil' => 'true'],
                            'ns:inp.indicatieGeheim'                    => ['@xsi:nil' => 'true'],
                            'ns:inOnderzoek'                            => ['@xsi:nil' => 'true'],
                            'StUF:tijdvakGeldigheid'                    => [
                                'StUF:beginGeldigheid'              => ['@xsi:nil' => 'true'],
                                'StUF:eindGeldigheid'               => ['@xsi:nil' => 'true'],
                            ],
                            'StUF:tijdstipRegistratie'              => ['@xsi:nil' => 'true'],
                            'ns:inp.heeftAlsEchtgenootPartner'      => [
                                '@StUF:entiteittype'                => 'NPSNPSHUW',
                                'ns:gerelateerde'                   => [
                                    '@StUF:entiteittype'            => 'NPS',
                                    'ns:inp.bsn'                    => ['@xsi:nil' => 'true'],
                                    'ns:geslachtsnaam'              => ['@xsi:nil' => 'true'],
                                    'ns:voorvoegselGeslachtsnaam'   => ['@xsi:nil' => 'true'],
                                    'ns:voorletters'                => ['@xsi:nil' => 'true'],
                                    'ns:voornamen'                  => ['@xsi:nil' => 'true'],
                                    'ns:aanhefAanschrijving'        => ['@xsi:nil' => 'true'],
                                    'ns:voornamenAanschrijving'     => ['@xsi:nil' => 'true'],
                                    'ns:geslachtsnaamAanschrijving' => ['@xsi:nil' => 'true'],
                                    'ns:adellijkeTitelPredikaat'    => ['@xsi:nil' => 'true'],
                                    'ns:geslachtsaanduiding'        => ['@xsi:nil' => 'true'],
                                    'ns:geboortedatum'              => ['@xsi:nil' => 'true'],
                                    'ns:inp.geboorteplaats'         => ['@xsi:nil' => 'true'],
                                    'ns:inp.geboorteLand'           => ['@xsi:nil' => 'true'],
                                ],
                                'ns:datumSluiting'                  => ['@xsi:nil' => 'true'],
                                'ns:plaatsSluiting'                 => ['@xsi:nil' => 'true'],
                                'ns:landSluiting'                   => ['@xsi:nil' => 'true'],
                                'ns:inOnderzoek'                    => ['@xsi:nil' => 'true'],
                            ],
                            'ns:inp.heeftAlsKinderen' => [
                                '@StUF:entiteittype'                => 'NPSNPSKND',
                                'ns:gerelateerde'                   => [
                                    '@StUF:entiteittype'            => 'NPS',
                                    'ns:inp.bsn'                    => ['@xsi:nil' => 'true'],
                                    'ns:geslachtsnaam'              => ['@xsi:nil' => 'true'],
                                    'ns:voorvoegselGeslachtsnaam'   => ['@xsi:nil' => 'true'],
                                    'ns:voorletters'                => ['@xsi:nil' => 'true'],
                                    'ns:voornamen'                  => ['@xsi:nil' => 'true'],
                                    'ns:aanhefAanschrijving'        => ['@xsi:nil' => 'true'],
                                    'ns:voornamenAanschrijving'     => ['@xsi:nil' => 'true'],
                                    'ns:geslachtsnaamAanschrijving' => ['@xsi:nil' => 'true'],
                                    'ns:adellijkeTitelPredikaat'    => ['@xsi:nil' => 'true'],
                                    'ns:geboortedatum'              => ['@xsi:nil' => 'true'],
                                    'ns:inp.geboorteplaats'         => ['@xsi:nil' => 'true'],
                                    'ns:inp.geboorteLand'           => ['@xsi:nil' => 'true'],
                                ],
                                'ns:inOnderzoek'                    => ['@xsi:nil' => 'true'],
                            ],
                            'ns:inp.heeftAlsOuders' => [
                                '@StUF:entiteittype'                => 'NPSNPSOUD',
                                'ns:gerelateerde'                   => [
                                    '@StUF:entiteittype'            => 'NPS',
                                    'ns:inp.bsn'                    => ['@xsi:nil' => 'true'],
                                    'ns:geslachtsnaam'              => ['@xsi:nil' => 'true'],
                                    'ns:voorvoegselGeslachtsnaam'   => ['@xsi:nil' => 'true'],
                                    'ns:voorletters'                => ['@xsi:nil' => 'true'],
                                    'ns:voornamen'                  => ['@xsi:nil' => 'true'],
                                    'ns:aanhefAanschrijving'        => ['@xsi:nil' => 'true'],
                                    'ns:voornamenAanschrijving'     => ['@xsi:nil' => 'true'],
                                    'ns:geslachtsnaamAanschrijving' => ['@xsi:nil' => 'true'],
                                    'ns:adellijkeTitelPredikaat'    => ['@xsi:nil' => 'true'],
                                    'ns:geslachtsaanduiding'        => ['@xsi:nil' => 'true'],
                                    'ns:geboortedatum'              => ['@xsi:nil' => 'true'],
                                    'ns:inp.geboorteplaats'         => ['@xsi:nil' => 'true'],
                                    'ns:inp.geboorteLand'           => ['@xsi:nil' => 'true'],
                                ],
                                'ns:ouderAanduiding'                            => ['@xsi:nil' => 'true'],
                                'ns:datumIngangFamilierechtelijkeBetrekking'    => ['@xsi:nil' => 'true'],
                                'ns:inOnderzoek'                                => ['@xsi:nil' => 'true'],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $this->xmlEncoder->encode($message, 'xml', ['remove_empty_tags' => true]);
    }

    private function createIncompleteDate(string $date): IncompleteDate
    {
        return new IncompleteDate((int) substr($date, 0, 4), (int) substr($date, 4, 2), (int) substr($date, 6, 2));
    }

    public function createNaamPersoon(array $answer): NaamPersoon
    {
        $result = new NaamPersoon();
        $result->setGeslachtsnaam(is_array($answer['geslachtsnaam']) ? null : $answer['geslachtsnaam']);
        !is_array($answer['voorletters']) ? $result->setVoorletters($answer['voorletters']) : null;
        !is_array($answer['voornamen']) ? $result->setVoornamen($answer['voornamen']) : null;
        $result->setVoorvoegsel(is_array($answer['voorvoegselGeslachtsnaam']) ? '' : $answer['voorvoegselGeslachtsnaam']);
        $result->setAanschrijfwijze((key_exists('aanhefAanschrijving', $answer) && !is_array($answer['aanhefAanschrijving']) ? $answer['aanhefAanschrijving'] : null).' '.
            (key_exists('voornamenAanschrijving', $answer) && !is_array($answer['voornamenAanschrijving']) ? $answer['voornamenAanschrijving'] : (is_array($answer['voornamen']) ? '' : $answer['voornamen'])).' '.
            (key_exists('geslachtsnaamAanschrijving', $answer) && !is_array($answer['geslachtsnaamAanschrijving']) ? $answer['geslachtsnaamAanschrijving'] : $answer['geslachtsnaam']).' '.
            (key_exists('adellijkeTitelPredikaat', $answer) && !is_array($answer['adellijkeTitelPredikaat']) ? $answer['adellijkeTitelPredikaat'] : null));
        $result->setGebuikInLopendeTekst(
            (key_exists('voornamenAanschrijving', $answer) && !is_array($answer['voornamenAanschrijving']) ? $answer['voornamenAanschrijving'] : (is_array($answer['voornamen']) ? '' : $answer['voornamen'])).' '.
            (key_exists('geslachtsnaamAanschrijving', $answer) && !is_array($answer['geslachtsnaamAanschrijving']) ? $answer['geslachtsnaamAanschrijving'] : $answer['geslachtsnaam'])
        );
        $this->entityManager->persist($result);

        return $result;
    }

    public function createLeeftijd(string $geboortedatum): int
    {
        try {
            $geboortedatum = new DateTime($geboortedatum);
            $leeftijd = $geboortedatum->diff(new DateTime('now'), true)->format('%Y');
        } catch (Exception $e) {
            $leeftijd = 0;
        }

        return $leeftijd;
    }

    public function createGeboorte(array $answer): Geboorte
    {
        $result = new Geboorte();
//        var_dump($answer['geboortedatum']);
        !is_array($answer['geboortedatum']) ? $result->setDatum($this->layerService->stringToIncompleteDate($answer['geboortedatum'])) : null;
        !is_array($answer['inp.geboorteLand']) ? $result->setLand($this->ltcService->getLand($answer['inp.geboorteLand'])) : null;
        !is_array($answer['inp.geboorteplaats']) ? $result->setPlaats($this->ltcService->getGemeente($answer['inp.geboorteplaats'])) : null;

        $this->entityManager->persist($result);

        return $result;
    }

    public function createGezagsverhouding(array $answer): Gezagsverhouding
    {
        $result = new Gezagsverhouding();
        $result->setIndicatieGezagMinderjarige(is_array($answer['ing.indicatieGezagMinderjarige']) ? null : $answer['ing.indicatieGezagMinderjarige']);
        $result->setIndicatieCurateleRegister(is_array($answer['ing.indicatieCurateleRegister']) ? false : $answer['ing.indicatieCurateleRegister']);

        return $result;
    }

    public function createOpschortingBijhouding(array $answer): ?OpschortingBijhouding
    {
        $result = new OpschortingBijhouding();
        !is_array($answer['inp.datumOpschortingBijhouding']) ? $result->setDatum($this->createIncompleteDate($answer['inp.datumOpschortingBijhouding'])) : null;
        !is_array($answer['inp.redenOpschortingBijhouding']) ? $result->setReden($answer['inp.redenOpschortingBijhouding']) : null;

        if (!is_array($answer['inp.datumOpschortingBijhouding'])) {
            return $result;
        }

        return null;
    }

    public function createOverlijden(array $answer): ?Overlijden
    {
        $result = new Overlijden();
        !is_array($answer['overlijdensdatum']) ? $result->setDatum($this->createIncompleteDate($answer['overlijdensdatum'])) : null;
        !is_array($answer['inp.overlijdenLand']) ? $result->setPlaats($this->ltcService->getLand($answer['inp.overlijdenLand'])) : null;
        !is_array($answer['inp.overlijdenplaats']) ? $result->setPlaats($this->ltcService->getGemeente($answer['inp.overlijdenplaats'])) : null;

        if (!is_array($answer['overlijdensdatum'])) {
            return $result;
        }

        return null;
    }

    public function createVerblijfBuitenland(array $answer, VerblijfPlaats $verblijfplaats): Verblijfplaats
    {
//        $result = new VerblijfBuitenland();

        $verblijfplaats->setLand($this->ltcService->getLand($answer['sub.verblijfBuitenland']['lnd.landcode']));
        $verblijfplaats->setAdresregel1($answer['sub.verblijfBuitenland']['sub.adresBuitenland1']);
        $verblijfplaats->setAdresregel2($answer['sub.verblijfBuitenland']['sub.adresBuitenland2']);
        $verblijfplaats->setAdresregel3($answer['sub.verblijfBuitenland']['sub.adresBuitenland3']);

        return $verblijfplaats;
    }

    public function createVerblijfplaats(array $answer): Verblijfplaats
    {
        $result = new Verblijfplaats();
        if (!key_exists('verblijfsadres', $answer)) {
            $result->setVertrokkenOnbekendWaarheen(true);

            return $result;
        }

        !is_array($answer['verblijfsadres']['aoa.identificatie']) ? $result->setNummeraanduidingIdentificatie($answer['verblijfsadres']['aoa.identificatie']) : null;
        !is_array($answer['verblijfsadres']['wpl.identificatie']) ? $result->setAdresseerbaarObjectIdentificatie($answer['verblijfsadres']['wpl.identificatie']) : null;
        !is_array($answer['verblijfsadres']['wpl.woonplaatsNaam']) ? $result->setWoonplaats($answer['verblijfsadres']['wpl.woonplaatsNaam']) : null;
        !is_array($answer['verblijfsadres']['wpl.identificatie']) ? $result->setNaamOpenbareRuimte($answer['verblijfsadres']['gor.openbareRuimteNaam']) : null;
        !is_array($answer['verblijfsadres']['inp.locatiebeschrijving']) ? $result->setLocatiebeschrijving($answer['verblijfsadres']['inp.locatiebeschrijving']) : null;
        !is_array($answer['verblijfsadres']['gor.straatnaam']) ? $result->setStraatnaam($answer['verblijfsadres']['gor.straatnaam']) : null;
        !is_array($answer['verblijfsadres']['aoa.postcode']) ? $result->setPostcode($answer['verblijfsadres']['aoa.postcode']) : null;
        !is_array($answer['verblijfsadres']['aoa.huisnummer']) ? $result->setHuisnummer($answer['verblijfsadres']['aoa.huisnummer']) : null;
        !is_array($answer['verblijfsadres']['aoa.huisletter']) ? $result->setHuisletter($answer['verblijfsadres']['aoa.huisletter']) : null;
        !is_array($answer['verblijfsadres']['aoa.huisnummertoevoeging']) ? $result->setHuisnummertoevoeging($answer['verblijfsadres']['aoa.huisnummertoevoeging']) : null;
        !is_array($answer['verblijfsadres']['begindatumVerblijf']) ? $result->setDatumAanvangAdreshouding($this->layerService->stringToIncompleteDate($answer['verblijfsadres']['begindatumVerblijf'])) : null;
        !key_exists('sub.verblijfBuitenland', $answer) ?? $result = $this->createVerblijfBuitenland($answer, $result);
        !is_array($answer['inp.gemeenteVanInschrijving']) ? $result->setGemeenteVanInschrijving($this->ltcService->getGemeente($answer['inp.gemeenteVanInschrijving'])) : null;
        !is_array($answer['inp.datumInschrijving']) ? $result->setDatumInschrijvingInGemeente($this->layerService->stringToIncompleteDate($answer['inp.datumInschrijving'])) : null;
        !is_array($answer['inp.datumVestigingInNederland']) ? $result->setDatumVestigingInNederland($this->layerService->stringToIncompleteDate($answer['inp.datumVestigingInNederland'])) : null;
//        $result->setDatumIngangGeldigheid($answer['StUF:tijdvakGeldigheid']['StUF:beginGeldigheid']);
        !is_array($answer['inp.immigratieLand']) ? $result->setLandVanwaarIngeschreven($this->ltcService->getLand($answer['inp.immigratieLand'])) : null;

        return $result;
    }

    public function createVerlijfstitel(array $answer): ?Verblijfstitel
    {
        $result = new Verblijfstitel();

        is_array($answer['vbt.aanduidingVerblijfstitel']) ?? $result->setAanduiding($answer['vbt.aanduidingVerblijfstitel']);
        is_array($answer['ing.datumVerkrijgingVerblijfstitel']) ?? $result->setDatumIngang($answer['ing.datumVerkrijgingVerlijfstitel']);
        is_array($answer['ing.datumVerliesVerblijfstitel']) ?? $result->setDatumEinde($answer['ing.datumVerliesVerblijfstitel']);

        if (!is_array($answer['vbt.aanduidingVerblijfstitel'])) {
            $this->entityManager->persist($result);

            return $result;
        }

        return null;
    }

    public function createAangaanHuwelijkPartnerschap(array $answer): AangaanHuwelijkPartnerschap
    {
        $result = new AangaanHuwelijkPartnerschap();

        is_array($answer['landSluiting']) ? null : $result->setLand($this->ltcService->getLand($answer['landSluiting']));
        is_array($answer['plaatsSluiting']) ? null : $result->setPlaats($this->ltcService->getGemeente($answer['plaatsSluiting']));
        is_array($answer['datumSluiting']) ? null : $result->setDatum($this->createIncompleteDate($answer['datumSluiting']));
        !key_exists('inOnderzoek', $answer) ?? $result->setInOnderzoek($answer['inOnderzoek']);

        return $result;
    }

    public function createPartner(array $answer): Partner
    {
        $partner = new Partner();
        $partner->setNaam($this->createNaamPersoon($answer['gerelateerde']));
        $partner->setGeboorte($this->createGeboorte($answer['gerelateerde']));
        is_array($answer['gerelateerde']['geslachtsaanduiding']) ? null : $partner->setGeslachtsaanduiding($answer['gerelateerde']['geslachtsaanduiding']);
        is_array($answer['gerelateerde']['inp.bsn']) ? null : $partner->setBurgerservicenummer($answer['gerelateerde']['inp.bsn']);
        $partner->setAangaanHuwelijkPartnerschap($this->createAangaanHuwelijkPartnerschap($answer));

        return $partner;
    }

    public function createKind(array $answer): Kind
    {
        $kind = new Kind();
        $kind->setNaam($this->createNaamPersoon($answer['gerelateerde']));
        $kind->setGeboorte($this->createGeboorte($answer['gerelateerde']));
        $kind->setLeeftijd($this->createLeeftijd($answer['gerelateerde']['geboortedatum']));
        is_array($answer['gerelateerde']['inp.bsn']) ?? $kind->setBurgerservicenummer($answer['gerelateerde']['inp.bsn']);
        !key_exists('inOnderzoek', $answer) ?? $kind->setInOnderzoek($answer['inOnderzoek']);

        return $kind;
    }

    public function createOuder(array $answer): Ouder
    {
        $ouder = new Ouder();
        is_array($answer['gerelateerde']['inp.bsn']) ?? $ouder->setBurgerservicenummer($answer['gerelateerde']['inp.bsn']);
        $ouder->setNaam($this->createNaamPersoon($answer['gerelateerde']));
        $ouder->setGeboorte($this->createGeboorte($answer['gerelateerde']));
        !is_array($answer['gerelateerde']['geslachtsaanduiding']) ? $ouder->setGeslachtsaanduiding($answer['gerelateerde']['geslachtsaanduiding']) : null;
        !key_exists('inOnderzoek', $answer) ?? $ouder->setInOnderzoek($answer['inOnderzoek']);
        !key_exists('datumIngangFamilierechtelijkeBetrekking', $answer) ?? $ouder->setDatumIngangFamilierechtelijkeBetreking($answer['datumIngangFamilierechtelijkeBetrekking']);
        !is_array($answer['ouderAanduiding']) ? $ouder->setOuderAanduiding($answer['ouderAanduiding']) : null;

        return $ouder;
    }

    public function createPartners(array $answer, Ingeschrevenpersoon $ingeschrevenpersoon): Ingeschrevenpersoon
    {
        if ((key_exists('@a:entiteittype', $answer) || key_exists('@StUF:entiteittype', $answer)) && !key_exists('#', $answer) && !key_exists('#', $answer['gerelateerde'])) {
            $ingeschrevenpersoon->addPartner($this->createPartner($answer));
        } else {
            foreach ($answer as $partner) {
                if (is_array($partner) && key_exists('gerelateerde', $partner) && !key_exists('#', $partner['gerelateerde'])) {
                    $ingeschrevenpersoon->addPartner($this->createPartner($partner));
                }
            }
        }

        return $ingeschrevenpersoon;
    }

    public function createKinderen(array $answer, Ingeschrevenpersoon $ingeschrevenpersoon): Ingeschrevenpersoon
    {
        if ((key_exists('@a:entiteittype', $answer) || key_exists('@StUF:entiteittype', $answer)) && !key_exists('#', $answer) && !key_exists('#', $answer['gerelateerde'])) {
            $ingeschrevenpersoon->addKind($this->createKind($answer));
        } else {
            foreach ($answer as $kind) {
                if (is_array($kind) && key_exists('gerelateerde', $kind) && !key_exists('#', $kind['gerelateerde'])) {
                    $ingeschrevenpersoon->addKind($this->createKind($kind));
                }
            }
        }

        return $ingeschrevenpersoon;
    }

    public function createOuders(array $answer, Ingeschrevenpersoon $ingeschrevenpersoon): Ingeschrevenpersoon
    {
        if ((key_exists('@a:entiteittype', $answer) || key_exists('@StUF:entiteittype', $answer)) && !key_exists('#', $answer) && !key_exists('#', $answer['gerelateerde'])) {
            $ingeschrevenpersoon->addOuder($this->createOuder($answer));
        } else {
            foreach ($answer as $ouder) {
                if (is_array($ouder) && key_exists('gerelateerde', $ouder) && !key_exists('#', $ouder['gerelateerde'])) {
                    $ingeschrevenpersoon->addOuder($this->createOuder($ouder));
                }
            }
        }

        return $ingeschrevenpersoon;
    }

    public function addRelatives(array $answer, Ingeschrevenpersoon $ingeschrevenpersoon): Ingeschrevenpersoon
    {
        isset($answer['inp.heeftAlsEchtgenootPartner']) ?
            $ingeschrevenpersoon = $this->createPartners($answer['inp.heeftAlsEchtgenootPartner'], $ingeschrevenpersoon) :
            null;
        isset($answer['inp.heeftAlsKinderen']) ?
            $ingeschrevenpersoon = $this->createKinderen($answer['inp.heeftAlsKinderen'], $ingeschrevenpersoon) :
            null;
        isset($answer['inp.heeftAlsOuders']) ?
            $ingeschrevenpersoon = $this->createOuders($answer['inp.heeftAlsOuders'], $ingeschrevenpersoon) :
            null;

        return $ingeschrevenpersoon;
    }

    public function createIngeschrevenPersoon(array $answer): Ingeschrevenpersoon
    {
        $result = new Ingeschrevenpersoon();
        $result->setBurgerservicenummer($answer['inp.bsn']);
        $result->setNaam($this->createNaamPersoon($answer));
        $result->setGeboorte($this->createGeboorte($answer));
        $result->setGeslachtsaanduiding($answer['geslachtsaanduiding']);
//        $result->setDatumEersteInschrijvingGBA($this->createIncompleteDate($answer['StUF:tijdstipRegistratie']));
        $result->setGeheimhoudingPersoonsgegevens(!is_array($answer['inp.indicatieGeheim']) ? $answer['inp.indicatieGeheim'] : false);
        $result->setInOnderzoek(key_exists('inOnderzoek', $answer) ? $answer['inOnderzoek'] : false);
        $result->setLeeftijd($this->createLeeftijd($answer['geboortedatum']));
        $result->setKiesrecht(!$answer['ing.aanduidingUitgeslotenKiesrecht']);
        $result->setGezagsverhouding($this->createGezagsverhouding($answer));
        $opschorting = $this->createOpschortingBijhouding($answer);
        if ($opschorting) {
            $result->setOpschortingBijhouding($opschorting);
        }
        if ($overlijden = $this->createOverlijden($answer)) {
            $result->setOverlijden($overlijden);
        }
        $result->setVerblijfplaats($this->createVerblijfplaats($answer));
        $verblijfsTitel = $this->createVerlijfstitel($answer);
        if ($verblijfsTitel) {
            $result->setVerblijfstitel($verblijfsTitel);
            $this->entityManager->persist($result->getVerblijfstitel());
        }
        $result = $this->addRelatives($answer, $result);
        $this->entityManager->persist($result);

        return $result;
    }

    public function performRequest(Request $request): array
    {
        $requestMessage = $this->createStufMessage($request);
        $response = $this->client->post('', ['body' => $requestMessage]);
        if ($response->getStatusCode() != 200 && $response->getStatusCode() != 201 && $response->getStatusCode() != 202) {
            echo 'Endpoint: ' . $this->parameterBag->get('stuf_uri') . '\n';
            echo 'Error Code: ' . $response->getStatusCode() . '\n';
            echo 'Response Body: ' . $response->getBody()->getContents() . '\n';
            echo 'Request Body: ' . $requestMessage . '\n';
            exit;
        }
        $result = $this->xmlEncoder->decode($response->getBody()->getContents(), 'xml');
        if (key_exists('antwoord', $result['s:Body']['npsLa01'])) {
            return $result['s:Body']['npsLa01']['antwoord']['object'];
        } else {
            throw new HttpException(404, 'Person not found');
        }
    }

    public function createIngeschrevenPersonen(array $results): array
    {
        $processedResults = [];
        foreach ($results as $result) {
            try {
                $processedResults[] = $this->createIngeschrevenPersoon($result);
            } catch (Exception $exception) {
                throw $exception;
            }
        }

        return $processedResults;
    }

    public function getIngeschrevenPersonen(array $result, Request $request, SerializerService $serializerService): ArrayCollection
    {
        if (key_exists('@a:entiteittype', $result)) {
            $results[] = $this->createIngeschrevenPersoon($result);
        } else {
            $results = $this->createIngeschrevenPersonen($result);
        }
        switch ($serializerService->getRenderType($request->headers->get('Accept'))) {
            case 'jsonhal':
                $response['adressen'] = $results;
                $response['totalItems'] = count($results);
                $response['itemsPerPage'] = count($results);
                $response['_links'] = $response['_links'] = ['self' => "/ingeschrevenpersonen?{$request->getQueryString()}"];
                break;
            default:
                $response['@context'] = '/contexts/IngeschrevenPersoon';
                $response['@id'] = '/IngeschrevenPersonen';
                $response['@type'] = 'hydra:Collection';
                $response['hydra:member'] = $results;
                $response['hydra:totalItems'] = count($results);
                break;
        }

        return new ArrayCollection($response);
    }

    public function getResults(RequestEvent $event, SerializerInterface $serializer): void
    {
        $serializerService = new SerializerService($serializer);
        $result = $this->performRequest($event->getRequest());
        if ($event->getRequest()->attributes->has('burgerservicenummer')) {
            $result = $this->createIngeschrevenPersoon($result);
        } else {
            $result = $this->getIngeschrevenPersonen($result, $event->getRequest(), $serializerService);
        }
        $serializerService->setResponse($result, $event);
    }
}
