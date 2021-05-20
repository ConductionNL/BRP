<?php


namespace App\Service;


use App\Entity\AangaanHuwelijkPartnerschap;
use App\Entity\Geboorte;
use App\Entity\Gezagsverhouding;
use App\Entity\Ingeschrevenpersoon;
use App\Entity\Kind;
use App\Entity\NaamPersoon;
use App\Entity\Nationaliteit;
use App\Entity\OpschortingBijhouding;
use App\Entity\Ouder;
use App\Entity\Overlijden;
use App\Entity\Partner;
use App\Entity\VerblijfBuitenland;
use App\Entity\Verblijfplaats;
use App\Entity\Verblijfstitel;
use Conduction\CommonGroundBundle\ValueObject\IncompleteDate;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use GuzzleHttp\Client;
use DateTime;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class StUFService
{
    private ParameterBagInterface $parameterBag;
    private Client $client;
    private XmlEncoder $xmlEncoder;
    private EntityManagerInterface $entityManager;
    private LtcService $ltcService;

    public function __construct(ParameterBagInterface $parameterBag, EntityManagerInterface $entityManager, LtcService $ltcService)
    {
        $this->entityManager = $entityManager;
        $this->parameterBag = $parameterBag;
        $this->ltcService = $ltcService;
        if($this->parameterBag->has('stuf_uri')){
            $baseUri = $this->parameterBag->get('stuf_uri');
        } else {
            throw new Exception('The base uri for the StUF requests has not been configured. This base uri is required for this mode');
        }

        if(
            !$this->parameterBag->has('app_certificate') ||
            !$this->parameterBag->has('app_ssl_key') ||
            !file_exists($this->parameterBag->get('app_certificate')) ||
            !file_exists($this->parameterBag->get('app_ssl_key'))
        ) {
            throw new Exception('The SSL certificate for two sided SSL have not been configured. The SSL certificate is required for this mode');
        }
        $this->headers = [
            'Content-Type' => 'text/xml',
            'SOAPAction' => "\"http://www.egem.nl/StUF/sector/bg/0310/npsLv01\""
        ];
        $this->guzzleConfig = [
            'http_errors' => false,
            'timeout' => 4000.0,
            'headers' => $this->headers,
            'verify' => true,
            'cert' => $this->parameterBag->get('app_certificate'),
            'ssl_key' => $this->parameterBag->get('app_ssl_key'),
            'base_uri' => $baseUri,
        ];

        $this->client = new Client($this->guzzleConfig);

        $this->xmlEncoder = new XmlEncoder(['xml_root_node_name' => 'soap:Envelope']);

    }

    public function createStufMessage (Request $request)
    {
        $time = new DateTime('now');
        $message = [
            '@xmlns:soap' => "http://schemas.xmlsoap.org/soap/envelope/",
            '@xmlns:StUF' => "http://www.egem.nl/StUF/StUF0301",
            '@xmlns:ns' => "http://www.egem.nl/StUF/sector/bg/0310",
            '@xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
            "soap:Body" => [

                "ns:npsLv01" => [
                    'ns:stuurgegevens' => [
                        'StUF:berichtcode'     =>  'Lv01',
                        'StUF:zender' => [
                            'StUF:organisatie'  => '0405',
                            'StUF:applicatie'   => 'WAAR',
                            'StUF:gebruiker'    => 'waardepapieren',
                        ],
                        'StUF:ontvanger' => [
                            'StUF:organisatie'  => '0405',
                            'StUF:applicatie'   => 'DDS',
                        ],
                        'StUF:tijdstipBericht'  =>  $time->format('YmdHisv'),
                        'StUF:entiteittype'    =>  'NPS',

                    ],
                    'ns:parameters' => [
                        'StUF:sortering'                => 1,
                        'StUF:indicatorVervolgvraag'    => 'false',
                        'StUF:maximumAantal'            => 30,
                    ],
                    'ns:gelijk' => [
                        '@StUF:entiteittype'                            => 'NPS',
                        'ns:inp.bsn'                                    => $request->attributes->has('burgerservicenummer') ? $request->attributes->get('burgerservicenummer') : ($request->query->has('burgerservicenummer') ? $request->attributes->get('burgerservicenummer') : null),
                        'ns:geboortedatum'                              => $request->query->has('geboorte.datum') ?  $request->query->get('geboorte.datum') : null,
                        'ns:geboorteplaats'                             => $request->query->has('geboorte.plaats') ?  $request->query->get('geboorte.plaats') : null,
                        'ns:geslachtsaanduiding'                        => $request->query->has('geslachtsaanduiding') ?  $request->query->get('geslachtsaanduiding') : null,
                        'ns:geslachtsnaam'                              => $request->query->has('naam.geslachtsnaam') ?  $request->query->get('naam.geslachtsnaam') : null,
                        'ns:voorvoegselGeslachtsnaam'                   => $request->query->has('naam.voorvoegsel') ? $request->query->get('naam.voorvoegsel') : null,
                        'ns:voornamen'                                  => $request->query->has('naam.voornamen') ? $request->query->get('naam.voornamen') : null,
                        'ns:verblijfsadres.wpl.identificatie'           => $request->query->has('verblijfplaats.gemeenteVanInschrijving') ? $request->query->get('verblijfplaats.gemeenteVanInschrijving') : null,
                        'ns:verblijfadres.aoa.identificatie'            => $request->query->has('verblijfplaats.nummeraanduidingIdentificatie') ? $request->query->get('verblijfplaats.nummeraanduidingIdentificatie') : null,
                        'ns:verblijfsadres.aoa.postcode'                => $request->query->has('verblijfplaats.postcode') ? $request->query->get('verblijfplaats.postcode') : null,
                        'ns:verblijfsadres.aoa.straat'                  => $request->query->has('verblijfplaats.straat') ? $request->query->get('verblijfplaats.straat') : null,
                        'ns:verblijfsadres.aoa.huisnummer'              => $request->query->has('verblijfplaats.huisnummer') ? $request->query->get('verblijfplaats.huisnummer') : null,
                        'ns:verblijfsadres.aoa.huisletter'              => $request->query->has('verblijfplaats.huisletter') ? $request->query->get('verblijfplaats.huisletter') : null,
                        'ns:verblijfsadres.aoa.huisnummertoevoeging'    => $request->query->has('verblijfplaats.huisnummertoevoeging') ? $request->query->get('verblijfplaats.huisnummertoevoeging') : null,
                    ],
                    'ns:scope' => [
                        'ns:object' => [
                            '@StUF:entiteittype'                    => "NPS",
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
                            'ns:inp.overlijdensdatum'               => ['@xsi:nil' => 'true'],
                            'ns:inp.overlijdenplaats'               => ['@xsi:nil' => 'true'],
                            'ns:inp.overlijdenLand'                 => ['@xsi:nil' => 'true'],
                            'ns:verblijfsadres' => [
                                'ns:aoa,identificatie'              => ['@xsi:nil' => 'true'],
                                'ns:wpl,identificatie'              => ['@xsi:nil' => 'true'],
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
                            'ns:gemeenteVanInschrijving'            => ['@xsi:nil' => 'true'],
                            'ns:inp.datumInschrijving'              => ['@xsi:nil' => 'true'],
                            'ns:vbt.aanduidingVerblijfstitel'       => ['@xsi:nil' => 'true'],
                            'ns:ing.datumVerkrijgingVerblijfstitel' => ['@xsi:nil' => 'true'],
                            'ns:ing.datumVerliesVerblijfstitel'     => ['@xsi:nil' => 'true'],
                            'ns:inp.datumVestigingInNederland'      => ['@xsi:nil' => 'true'],
                            'ns:inp.immigratieLand'                 => ['@xsi:nil' => 'true'],
                            'ns:ing.aanduidingUitgeslotenKiesrecht' => ['@xsi:nil' => 'true'],
                            'ns:ing.indicatieGezagMinderjarige'     => ['@xsi:nil' => 'true'],
                            'ns:ing.indicatieCurateleRegister'      => ['@xsi:nil' => 'true'],
                            'ns:inp.datumOpschortingBijhouding'     => ['@xsi:nil' => 'true'],
                            'ns:inp.redenOpschortingBijhouding'     => ['@xsi:nil' => 'true'],
                            'ns:inp.indicatieGeheim'                => ['@xsi:nil' => 'true'],
                            'ns:inp.inOnderzoek'                    => ['@xsi:nil' => 'true'],
                            'StUF:tijdvakGeldigheid' => [
                                'StUF:beginGeldigheid'              => ['@xsi:nil' => 'true'],
                            ],
                            'StUF:tijdstipRegistratie'              => ['@xsi:nil' => 'true'],
                            'ns:inp.heeftAlsEchtgenootPartner'  => [
                                '@StUF:entiteittype'                =>  'NPSNPSHUW',
                                'ns:gerelateerde' => [
                                    '@StUF:entiteittype'            =>  'NPS',
                                    'ns:inp.bsn'                    => ['@xsi:nil' => 'true'],
                                    'ns:geslachtsnaam'              => ['@xsi:nil' => 'true'],
                                    'ns:voorvoegselGeslachtNaam'    => ['@xsi:nil' => 'true'],
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
                                'ns:gerelateerde' => [
                                    '@StUF:entiteittype'            => 'NPS',
                                    'ns:inp.bsn'                    => ['@xsi:nil' => 'true'],
                                    'ns:geslachtsnaam'              => ['@xsi:nil' => 'true'],
                                    'ns:voorvoegselGeslachtNaam'    => ['@xsi:nil' => 'true'],
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
                                '@StUF:entiteittype'                => 'NPSNPSKND',
                                'ns:gerelateerde' => [
                                    '@StUF:entiteittype'            => 'NPS',
                                    'ns:inp.bsn'                    => ['@xsi:nil' => 'true'],
                                    'ns:geslachtsnaam'              => ['@xsi:nil' => 'true'],
                                    'ns:voorvoegselGeslachtNaam'    => ['@xsi:nil' => 'true'],
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
                        ]
                    ]
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
        $result->setGeslachtsnaam($answer['geslachtsnaam']);
        $result->setVoorletters($answer['voorletters']);
        $result->setVoornamen($answer['voornamen']);
        $result->setVoorvoegsel(is_array($answer['voorvoegselGeslachtsnaam']) ? '' : $answer['voorvoegselGeslachtsnaam']);
        $result->setAanschrijfwijze((key_exists('aanhefAanschrijving', $answer)? $answer['aanschrijfwijze'] : null) .
            (key_exists('voornamenAanschrijving', $answer) ? $answer['voornamenAanschrijving'] : $answer['voornamen']) .
            (key_exists('geslachtsnaamAanschrijving', $answer) ? $answer['geslachtsnaamAanschrijving'] : $answer['geslachtsnaam']) .
            (key_exists('adellijkeTitelPredikaat', $answer)? $answer['adelijkeTitelPredikaat'] : null));
        $result->setGebuikInLopendeTekst(
            (key_exists('voornamenAanschrijving', $answer) ? $answer['voornamenAanschrijving'] : $answer['voornamen']) .
            (key_exists('geslachtsnaamAanschrijving', $answer) ? $answer['geslachtsnaamAanschrijving'] : $answer['geslachtsnaam'])
        );
        $this->entityManager->persist($result);

        return $result;
    }

    public function createLeeftijd(string $geboortedatum): int
    {
        try{
            $geboortedatum = new DateTime($geboortedatum);
            $leeftijd = $geboortedatum->diff(new DateTime('now'), true)->format('%Y');
        } catch (Exception $e){
            $leeftijd = 0;
        }
        return $leeftijd;
    }

    public function createGeboorte(array $answer): Geboorte
    {
        $result = new Geboorte();
        $result->setDatum($this->createIncompleteDate($answer['geboortedatum']));
        $result->setLand($this->ltcService->getLand($answer['inp.geboorteland']));
        $result->setPlaats($this->ltcService->getGemeente($answer['inp.geboorteplaats']));

        $this->entityManager->persist($result);

        return $result;
    }

    public function createGezagsverhouding(array $answer): Gezagsverhouding
    {
        $result = new Gezagsverhouding();
        $result->setIndicatieGezagMinderjarige($answer['ing.indicatieGezagMinderjarige']);
        $result->setIndicatieCurateleRegister($answer['ing.indicatieCurateleRegister']);
        return $result;
    }

    public function createOpschortingBijhouding(array $answer, Ingeschrevenpersoon $person): OpschortingBijhouding
    {
        $result = new OpschortingBijhouding();
        $result->setDatum($this->createIncompleteDate($answer['inp.datumOpschortingBijhouding']));
        $result->setIngeschrevenpersoon($person);
        $result->setReden();
        return $result;
    }

    public function createOverlijden(array $answer, Ingeschrevenpersoon $person): Overlijden
    {
        $result = new Overlijden();
        $result->setIngeschrevenpersoon($person);
        $result->setDatum($this->createIncompleteDate($answer['overleidensdatum']));
        $result->setPlaats($this->ltcService->getLand($answer['inp.overlijdensLand']));
        $result->setPlaats($this->ltcService->getGemeente($answer['inp.overlijdensplaats']));
        return $result;
    }

    public function createVerblijfBuitenland(array $answer): VerblijfBuitenland
    {
        $result = new VerblijfBuitenland();

        $result->setLand($this->ltcService->getLand($answer['sub.verblijfBuitenland']['lnd.landcode']));
        $result->setAdresregel1($answer['sub.verblijfBuitenland']['sub.adresBuitenland1']);
        $result->setAdresregel2($answer['sub.verblijfBuitenland']['sub.adresBuitenland2']);
        $result->setAdresregel3($answer['sub.verblijfBuitenland']['sub.adresBuitenland3']);
        return $result;
    }

    public function createVerblijfplaats(array $answer): Verblijfplaats
    {
        $result = new Verblijfplaats();
        $result->setIdentificatiecodeNummeraanduiding($answer['verblijfsadres']['aoa.identificatie']);
        $result->setBagId($answer['verblijfsadres']['aoa.identificatie']);
        $result->setIdentificatiecodeVerblijfplaats($answer['verblijfsadres']['wpl.identificatie']);
        $result->setWoonplaatsnaam($answer['verblijfsadres']['wpl.woonplaatsNaam']);
        $result->setNaamOpenbareRuimte($answer['verblijfsadres']['gor.openbareRuimteNaam']);
        $result->setLocatiebeschrijving($answer['verblijfsadres']['inp.locatiebeschrijving']);
        $result->setStraatnaam($answer['verblijfsadres']['gor.straatnaam']);
        $result->setPostcode($answer['verblijfsadres']['aoa.postcode']);
        $result->setHuisnummer($answer['verblijfsadres']['aoa.huisnummer']);
        $result->setHuisletter($answer['verblijfsadres']['aoa.huisletter']);
        $result->setHuisnummertoevoeging($answer['verblijfsadres']['aoa.huisnummertoevoeging']);
        $result->setDatumAanvangAdreshouding($answer['verblijfsadres']['begindatumVerblijf']);
        $result->setVerblijfBuitenland($this->createVerblijfBuitenland($answer));
        $result->setGemeenteVanInschrijving($this->ltcService->getGemeente($answer['inp.gemeenteVanInschrijving']));
        $result->setDatumInschrijvingInGemeente($answer['datumInschrijving']);
        $result->setDatumVestigingInNederland($answer['inp.datumVestigingInNederland']);
        $result->setDatumIngangGeldigheid($answer['StUF:tijdvakGeldigheid']['StUF:beginGeldigheid']);
        $result->setLandVanwaarIngeschreven($this->ltcService->getLand($answer['inp.immigratieland']));
        return $result;
    }

    public function createVerlijfstitel(array $answer): Verblijfstitel
    {
        $result = new Verblijfstitel();

        $result->setAanduiding($answer['vbt.aanduidingVerblijfstitel']);
        $result->setDatumIngang($answer['ing.datumVerkrijgingVerlijfstitel']);
        $result->setDatumEinde($answer['ing.datumVerliesVerblijfstitel']);

        return $result;
    }

    public function createAangaanHuwelijkPartnerschap(array $answer): AangaanHuwelijkPartnerschap
    {
        $result = new AangaanHuwelijkPartnerschap();

        $result->setLand($this->ltcService->getLand($answer['landSluiting']));
        $result->setPlaats($this->ltcService->getGemeente($answer['plaatsSluiting']));
        $result->setDatum($this->createIncompleteDate($answer['datumSluiting']));
        $result->setInOnderzoek($answer['inOnderzoek']);


        return $result;
    }

    public function createPartner(array $answer): Partner
    {
        $partner = new Partner();
        $partner->setNaam($this->createNaamPersoon($answer['gerelateerde']));
        $partner->setGeboorte($this->createGeboorte($answer['gerelateerde']));
        $partner->setGeslachtsaanduiding($answer['gerelateerde']['geslachtsaanduiding']);
        $partner->setBurgerservicenummer($answer['gerelateerde']['inp.bsn']);
        $partner->setAangaanHuwelijkPartnerschap($this->createAangaanHuwelijkPartnerschap($answer));

        return $partner;
    }

    public function createKind(array $answer): Kind
    {
        $kind = new Kind();
        $kind->setNaam($answer['gerelateerde']);
        $kind->setGeboorte($answer['gerelateerde']);
        $kind->setLeeftijd($this->createLeeftijd($answer['gerelateerde']['geboortedatum']));
        $kind->setBurgerservicenummer($answer['gerelateerde']['inp.bsn']);
        $kind->setInOnderzoek($answer['inOnderzoek']);

        return $kind;
    }

    public function createOuder(array $answer): Ouder
    {
        $ouder = new Ouder();
        $ouder->setBurgerservicenummer($answer['gerelateerde']['inp.bsn']);
        $ouder->setNaam($this->createNaamPersoon($answer['gerelateerde']));
        $ouder->setGeboorte($this->createGeboorte($answer['gerelateerde']));
        $ouder->setGeslachtsaanduiding($answer['gerelateerde']['geslachtsAanduiding']);
        $ouder->setInOnderzoek($answer['inOnderzoek']);
        $ouder->setDatumIngangFamilierechtelijkeBetreking($answer['datumIngangFamilierechtelijkeBetrekking']);
        $ouder->setOuderAanduiding($answer['ouderAanduiding']);

        return $ouder;
    }

    public function createPartners(array $answer, Ingeschrevenpersoon $ingeschrevenpersoon): Ingeschrevenpersoon
    {

        return $ingeschrevenpersoon;
    }

    public function addRelatives(array $answer, Ingeschrevenpersoon $ingeschrevenpersoon): Ingeschrevenpersoon
    {
        $ingeschrevenpersoon = $this->createPartners($answer, $ingeschrevenpersoon);
        return $ingeschrevenpersoon;
    }

    public function createIngeschrevenPersoon(array $answer): Ingeschrevenpersoon
    {
        $result = new Ingeschrevenpersoon();
        $result->setBurgerservicenummer($answer['inp.bsn']);
        $result->setNaam($this->createNaamPersoon($answer));
        $result->setGeboorte($this->createGeboorte($answer));
        $result->setGeslachtsaanduiding($answer['geslachtsaanduiding']);
        $result->setDatumEersteInschrijvingGBA($this->createIncompleteDate($answer['StUF:tijdstipRegistratie']));
        $result->setGeheimhoudingPersoonsgegevens($answer['inp.indicatieGeheim']);
        $result->setInOnderzoek($answer['inOnderzoek']);
        $result->setLeeftijd($this->createLeeftijd($answer['geboortedatum'], $result));
        $result->setKiesrecht(!$answer['ing.aanduidingUitgeslotenKiesrecht']);
        $result->setGezagsverhouding($this->createGezagsverhouding($answer));
        $result->setOpschortingBijhouding($this->createOpschortingBijhouding($answer, $result));
        $result->setOverlijden($this->createOverlijden($answer, $result));
        $result->setVerblijfplaats($this->createVerblijfplaats($answer));
        $result->setVerblijfstitel($this->createVerlijfstitel($answer));
        return $this->addRelatives($answer, $result);
    }

    public function performRequest (Request $request): Ingeschrevenpersoon
    {
        $requestMessage = $this->createStufMessage($request);
        echo $requestMessage;

        $response = $this->client->post('', ['body' => $requestMessage]);
        if($response->getStatusCode() != 200 && $response->getStatusCode() != 201 && $response->getStatusCode() != 202){
            echo $response->getBody()->getContents();
            die;
//            throw new HttpException($response->getStatusCode(), $response->getReasonPhrase());
        }
//        echo $response->getBody()->getContents();
        $result = $this->xmlEncoder->decode($response->getBody()->getContents(), 'xml');
        $answer = $result['s:Body']['npsLa01']['antwoord']['object'];

        var_dump($result);

//        return $this->createIngeschrevenPersoon($answer);
        die;
    }
}
