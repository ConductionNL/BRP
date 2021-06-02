<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class SerializerService
{
    private string $contentType;
    private string $renderType;
    private Request $request;
    private SerializerInterface $serializer;

    public function __construct(Request $request, SerializerInterface $serializer){
        $this->request = $request;
        $this->serializer = $serializer;
        $this->contentType = (string)$request->headers->get('accept');
        if (!$this->contentType) {
            $this->contentType = (string)$request->headers->get('Accept');
        }
        switch ($this->contentType) {
            case 'application/json':
                $this->renderType = 'json';
                break;
            case 'application/ld+json':
                $this->renderType = 'jsonld';
                break;
            case 'application/hal+json':
                $this->renderType = 'jsonhal';
                break;
            default:
                $this->contentType = 'application/json';
                $this->renderType = 'json';
        }
    }

    public function serialize($result): string
    {
        // now we need to overide the normal subscriber
        if (
            $this->request->query->has('geefFamilie') &&
            $this->request->query->get('geefFamilie') == 'true'
        ) {
            return $this->serializer->serialize(
                $result,
                $this->renderType,
                ['enable_max_depth' => true, 'groups' => ['show_family']]
            );
        } else {
            return $this->serializer->serialize(
                $result,
                $this->renderType,
                ['enable_max_depth' => true]
            );
        }
    }

    public function createResponse(string $json): Response
    {
        return new Response(
            $json,
            Response::HTTP_OK,
            ['content-type' => $this->contentType]
        );
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getRenderType(): string
    {
        return $this->renderType;
    }
}
