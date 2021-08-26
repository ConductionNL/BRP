<?php

namespace App\Service;

use App\Entity\Verblijfplaats;
use Conduction\CommonGroundBundle\ValueObject\UnderInvestigation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class GbavService
{
    private Client $client;
    private LtcService $ltcService;
    private EntityManagerInterface $entityManager;
    private ParameterBagInterface $parameterBag;
    private XmlEncoder $xmlEncoder;
    private LayerService $layerService;

    public function __construct(LayerService $layerService)
    {
        $this->entityManager = $layerService->getEntityManager();
        $this->ltcService = new LtcService($layerService->getCommonGroundService());
        $this->parameterBag = $layerService->getParameterBag();
        $this->layerService = $layerService;

        if ($this->parameterBag->get('mode') != 'StUF') {
            return;
        }

//        if ($this->parameterBag->has('gbav_uri')) {
//            $baseUri = $this->parameterBag->get('gbav_uri');
//        } else {
//            throw new Exception('The base uri for the GBA-V requests has not been configured. This base uri is required for this request');
//        }
        $baseUri = $this->parameterBag->get('gbav_uri');

        if (
            !$this->parameterBag->has('app_certificate') ||
            !$this->parameterBag->has('app_ssl_key') ||
            !file_exists($this->parameterBag->get('app_certificate')) ||
            !file_exists($this->parameterBag->get('app_ssl_key'))
        ) {
            throw new Exception('The SSL certificate for two sided SSL have not been configured. The SSL certificate is required for this mode');
        }
        $this->headers = [
            'Content-Type'  => 'text/xml',
            'SOAPAction'    => 'Vraag',
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

    public function createSoapMessage(string $bsn, array $columns): string
    {
        $message = [
            '@xmlns:soap'   => 'http://schemas.xmlsoap.org/soap/envelope/',
            '@xmlns:v1'     => 'urn:Centric/PIV/GBAV/GeneriekBevragingsComponent/v1.0',
            '@xmlns:ver'    => 'http://www.bprbzk.nl/GBA/LRDPlus/version1.1',
            'soap:Header'   => [
                'v1:Header'     => [
                    'v1:AfnemerscodeGBA'    => '510155',
                    'v1:Afdeling'           => 'INFORMATIEMANAGEMENT',  //@TODO: find appropriate department
                    'v1:Gebruiker'          => 'KLANT', //@TODO: find appropriate user
                    'v1:Applicatie'         => 'WAAR',
                ],
            ],
            'soap:Body'     => [
                'vraag'         => [
                    '@xmlns'        => 'http://www.bprbzk.nl/GBA/LRDPlus/version1.1',
                    'in0'           => [
                        'indicatieAdresvraag'       => '1',
                        'indicatieZoekenInHistorie' => '1',
                        'masker'                    => [
                            'item'                      => $columns,
                        ],
                        'parameters'                => [
                            'item'                      => [
                                'rubrieknummer'             => '10120',
                                'zoekwaarde'                => "$bsn",
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $this->xmlEncoder->encode($message, 'xml', ['remove_empty_tags' => true]);
    }

    public function setBoolValue(array $keys, bool $value): array
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $value;
        }

        return $result;
    }

    public function getInvestigationProperties(string $code): array
    {
        $base = $this->setBoolValue(
            ['datumInschrijvingInGemeente', 'datumIngangGeldigheid', 'gemeenteVanInschrijving', 'functieAdres', 'datumAanvangAdreshouding', 'straat', 'naamOpenbareRuimte', 'huisnummer', 'huisletter', 'huisnummertoevoeging', 'aanduidingBijHuisnummer', 'postcode', 'woonplaats', 'nummeraanduidingIdentificatie', 'adresseerbaarObjectIdentificatie', 'locatiebeschrijving', 'verblijfBuitenland', 'datumVestigingInNederland', 'landVanWaarIngeschreven'],
            false
        );
        switch ($code) {
            case '080900':
            case '580900':
                $keys = ['datumInschrijvingInGemeente', 'datumIngangGeldigheid', 'gemeenteVanInschrijving'];
                break;
            case '081000':
            case '581000':
                $keys = ['functieAdres', 'datumAanvangAdreshouding'];
                break;
            case '081100':
            case '581100':
                $keys = ['straat', 'naamOpenbareRuimte', 'huisnummer', 'huisletter', 'huisnummertoevoeging', 'aanduidingBijHuisnummer', 'postcode', 'woonplaats', 'nummeraanduidingIdentificatie', 'adresseerbaarObjectIdentificatie'];
                break;
            case '081200':
            case '581200':
                $keys = ['locatiebeschrijving'];
                break;
            case '081300':
            case '581300':
                $keys = ['verblijfBuitenland'];
                break;
            case '081400':
            case '581400':
                $keys = ['datumVestigingInNederland', 'landVanWaarIngeschreven'];
                break;
            default:
                return $base;
        }
        $override = $this->setBoolValue($keys, true);

        return array_merge($base, $override);
    }

    public function checkUnderInvestigation(array $residence): ?UnderInvestigation
    {
        foreach ($residence as $item) {
            switch ($item['nummer']) {
                case '8310':
                    $properties = $this->getInvestigationProperties($item['waarde']);
                    break;
                case '8320':
                    $date = $this->layerService->stringToIncompleteDate($item['waarde']);
                    break;
                case '8330':
                    return null;
                default:
                    break;
            }
        }
        if (isset($properties) && isset($date)) {
            return new UnderInvestigation($properties, $date);
        } else {
            return null;
        }
    }

    public function processResidence(array $residence): Verblijfplaats
    {
        $result = new Verblijfplaats();
        foreach ($residence as $item) {
            switch ($item['nummer']) {
                case '910':
                    $gemeente = $this->ltcService->getGemeente($item['waarde']);
                    $this->entityManager->persist($gemeente);
                    $result->setGemeenteVanInschrijving($gemeente);
                    break;
                case '920':
                    $result->setDatumInschrijvingInGemeente($this->layerService->stringToIncompleteDate($item['waarde']));
                    $result->setDatumIngangGeldigheid($this->layerService->stringToIncompleteDate($item['waarde']));
                    break;
                case '1010':
                    $result->setFuntieAdres($item['waarde'] == 'W' ? 'woonadres' : 'briefadres');
                    break;
                case '1320':
                case '1030':
                    $result->setDatumAanvangAdreshouding($this->layerService->stringToIncompleteDate($item['waarde']));
                    break;
                case '1110':
                    $result->setStraatnaam($item['waarde']);
                    break;
                case '1115':
                    $result->setNaamOpenbareRuimte($item['waarde']);
                    break;
                case '1120':
                    $result->setHuisnummer($item['waarde']);
                    break;
                case '1130':
                    $result->setHuisletter($item['waarde']);
                    break;
                case '1140':
                    $result->setHuisnummertoevoeging($item['waarde']);
                    break;
                case '1150':
                    $result->setAanduidingBijHuisnummer($item['waarde']);
                    break;
                case '1160':
                    $result->setPostcode($item['waarde']);
                    break;
                case '1170':
                    $result->setWoonplaats($item['waarde']);
                    break;
                case '1180':
                    $result->setAdresseerbaarObjectIdentificatie($item['waarde']);
                    break;
                case '1190':
                    $result->setNummeraanduidingIdentificatie($item['waarde']);
//                    $result->setBagId($item['waarde']);
                    break;
                case '1210':
                    $result->setLocatiebeschrijving($item['waarde']);
                    break;
                case '1310':
                    $item['waarde'] == '0000' ? $result->setVertrokkenOnbekendWaarheen(true) : null;
                    $result->setLand($this->ltcService->getLand($item['waarde']));
                    break;
                case '1330':
                    $result->setAdresregel1($item['waarde']);
                    break;
                case '1340':
                    $result->setAdresregel2($item['waarde']);
                    break;
                case '1350':
                    $result->setAdresregel3($item['waarde']);
                    break;
                case '1410':
                    $item['waarde'] == '0000' ? $result->setVanuitVertrokkenOnbekendWaarheen(true) : $result->setIndicatieVestigingVanuitBuitenland(true);
                    $land = $this->ltcService->getLand($item['waarde']);
                    $this->entityManager->persist($land);
                    $result->setLandVanwaarIngeschreven($land);
                    break;
                case '1420':
                    $result->setDatumVestigingInNederland($this->layerService->stringToIncompleteDate($item['waarde']));
                    break;
            }
        }
        $result->setInOnderzoek($this->checkUnderInvestigation($residence));
        $this->entityManager->persist($result);

        return $result;
    }

    public function findProperCategory(array $categoryOccurence): ?object
    {
        switch ($categoryOccurence['categorienummer']) {
            case '8':
            case '58':
                return $this->processResidence($categoryOccurence['elementen']['item']);
            default:
                return null;
        }
    }

    public function processCategoryOccurences(array $categoryOccurences): array
    {
        $results = [];
        foreach ($categoryOccurences as $categoryOccurence) {
            $results[] = $this->findProperCategory($categoryOccurence);
        }

        return $results;
    }

    public function processCategoryStack(array $stack, array $results): array
    {
        if ($this->layerService->isAssociativeArray($stack['categorievoorkomens']['item'])) {
            $result = $this->findProperCategory($stack['categorievoorkomens']['item']);
            $result ? $results[] = $result : null;
        } else {
            $results = array_merge($this->processCategoryOccurences($stack['categorievoorkomens']['item']), $results);
        }

        return $results;
    }

    public function processCategoryStacks(array $stacks): array
    {
        $results = [];
        if (count($stacks) > 1) {
            foreach ($stacks as $stack) {
                $results = $this->processCategoryStack($stack, $results);
            }
        } else {
            $results = $this->processCategoryStack($stacks, $results);
        }

        return $results;
    }

    public function processGbavResult(array $data): array
    {
        $results = [];
        $people = $data['soap:Body']['vraagResponse']['vraagReturn']['persoonslijsten']['item'];

        if (count($people) > 1) {
            foreach ($people as $person) {
                $results = $this->processCategoryStacks($person['categoriestapels']['item']);
            }
        } else {
            $results = $this->processCategoryStacks($people['categoriestapels']['item']);
        }

        return $results;
    }

    public function setEndDate(array $results): array
    {
        for ($iterator = count($results) - 1; $iterator > 0; $iterator--) {
            if ($results[$iterator - 1]->getDatumAanvangAdreshouding()) {
                $results[$iterator]->setDatumTot($results[$iterator - 1]->getDatumAanvangAdreshouding());
            }
        }

        return $results;
    }

    public function removeCurrentAddress(array $results): array
    {
        foreach($results as $key=>$result){
            if($result instanceof Verblijfplaats && !$result->getDatumTot()){
                unset($results[$key]);
            }
        }
        return $results;
    }

    public function getWoongeschiedenisForBsn(string $bsn): ArrayCollection
    {
        $requestMessage = $this->createSoapMessage($bsn, [
            '580910', '80910',
            '580920', '80920',
            '581010', '81010',
            '581030', '81030',
            '581110', '81110',
            '581115', '81115',
            '581120', '81120',
            '581130', '81130',
            '581140', '81140',
            '581150', '81150',
            '581160', '81160',
            '581170', '81170',
            '581180', '81180',
            '581190', '81190',
            '581210', '81210',
            '581310', '81310',
            '581320', '81320',
            '581330', '81330',
            '581340', '81340',
            '581350', '81350',
            '581410', '81410',
            '581420', '81420',
        ]);
        $response = $this->client->post('', ['body' => $requestMessage]);
        $content = $response->getBody()->getContents();
        if (
            strpos($content, 'Geen PL-en die aan de verstrekkingscondities voldoen.') !== false ||
            strpos($content, 'Geen gegevens gevonden') !== false
        ) {
            throw new NotFoundHttpException("Geen verblijfplaatshistorie gevonden voor BSN $bsn");
        }
        try {
            $decoded = $this->xmlEncoder->decode($content, 'xml');
        } catch (NotEncodableValueException $e) {
            var_dump($content);
            var_dump($response->getBody());
            echo $content;
            echo $requestMessage
            exit;
        }
        $results = $this->processGbavResult($decoded);

        $results = $this->setEndDate($results);

        $results = $this->removeCurrentAddress($results);

        return new ArrayCollection($results);
    }

    public function getResponseData(Request $request, ArrayCollection $results): array
    {
        $accept = $request->headers->has('Accept') ? $request->headers->get('Accept') : ($request->headers->has('accept') ? $request->headers->get('accept') : 'application/ld+json');
        switch ($accept) {
            case 'application/hal+json':
                return  [
                    '_embedded' => ['verblijfplaatshistorie' => $results],
                    '_links'    => [
                        'self' => [
                            'href' => "{$this->parameterBag->get('app_url')}/ingeschrevenpersonen/{$request->attributes->get('burgerservicenummer')}/verblijfplaatshistorie",
                        ],
                    ],
                ];
            case 'application/ld+json':
            default:

                return  [
                    '@context'              => '/contexts/Verblijfplaats',
                    '@id'                   => "/ingeschrevenpersonen/{$request->attributes->get('burgerservicenummer')}/verblijfplaatshistorie",
                    '@type'                 => 'hydra:Collection',
                    'hydra:member'          => $results,
                    'hydra:totalCount'      => $results->count(),
                ];
        }
    }

    public function getWoongeschiedenis(Request $request): ArrayCollection
    {
        $results = $this->getWoongeschiedenisForBsn($request->attributes->get('burgerservicenummer'));
        $result = $this->getResponseData($request, $results);

        $result = new ArrayCollection($result);

        return $result;
    }
}
