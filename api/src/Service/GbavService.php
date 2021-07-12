<?php


namespace App\Service;


use Exception;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class GbavService
{
    private Client $client;
    private ParameterBagInterface $parameterBag;
    private XmlEncoder $xmlEncoder;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;

        if ($this->parameterBag->get('mode') != 'StUF') {
            return;
        }

//        if ($this->parameterBag->has('gbav_uri')) {
//            $baseUri = $this->parameterBag->get('gbav_uri');
//        } else {
//            throw new Exception('The base uri for the GBA-V requests has not been configured. This base uri is required for this request');
//        }
        $baseUri = 'https://services.sandbox.digikoppeling.eu/hoorn/generiekbevragingscomponent/v1/vraag';

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
            'SOAPAction'    => '""',
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
    public function createSoapMessage(string $bsn): string
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
                        'indicatieAdresvraag'       => '0',
                        'indicatieZoekenInHistorie' => '0',
                        'masker'                    => [
                            'item'                      => ['10120', '10240'],
                        ],
                        'parameters'                => [
                            'item'                      => [
                                'rubrieknummer'             => '10120',
                                'zoekwaarde'                => "$bsn",
                            ]
                        ]
                    ]
                ]
            ]
        ];
        return $this->xmlEncoder->encode($message, 'xml', ['remove_empty_tags' => true]);
    }


    public function getWoongeschiedenisForBsn (string $bsn): array
    {
        $requestMessage = $this->createSoapMessage($bsn);
        var_dump($requestMessage);
        $response = $this->client->post('', ['body' => $requestMessage]);
        echo $response->getStatusCode();
        echo $response->getReasonPhrase();
        echo $response->getBody()->getContents();
        die;
        $result = [];

        return $result;
    }

    public function getWoongeschiedenis (Request $request, SerializerInterface $serializer): Response
    {
        $result = $this->getWoongeschiedenisForBsn($request->attributes->get('burgerservicenummer'));
    }
}
