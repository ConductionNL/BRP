<?php

namespace App\Service;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Conduction\CommonGroundBundle\ValueObject\IncompleteDate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class LayerService
{
    private EntityManagerInterface $entityManager;
    private ParameterBagInterface $parameterBag;
    private CommonGroundService $commonGroundService;

    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag, CommonGroundService $commonGroundService)
    {
        $this->commonGroundService = $commonGroundService;
        $this->entityManager = $entityManager;
        $this->parameterBag = $parameterBag;
    }

    public function getCommonGroundService(): CommonGroundService
    {
        return $this->commonGroundService;
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    public function getParameterBag(): ParameterBagInterface
    {
        return $this->parameterBag;
    }

    public function stringToIncompleteDate(string $input): IncompleteDate
    {
        return new IncompleteDate(
            substr($input, 0, 4),
            substr($input, 4, 2),
            substr($input, 6, 2)
        );
    }

    public function isAssociativeArray(array $array): bool
    {
        if ([] === $array) {
            return false;
        }

        return array_keys($array) !== range(0, count($array) - 1);
    }
}
