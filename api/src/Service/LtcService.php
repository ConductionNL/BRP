<?php

namespace App\Service;

use App\Entity\Waardetabel;
use Conduction\CommonGroundBundle\Service\CommonGroundService;

class LtcService
{
    private CommonGroundService $commonGroundService;

    public function __construct(CommonGroundService $commonGroundService)
    {
        $this->commonGroundService = $commonGroundService;
    }

    public function getNationaliteit(?string $code): Waardetabel
    {
        $nationaliteit = new Waardetabel();
        $nationaliteiten = $this->commonGroundService->getResourceList(['component'=>'ltc', 'type'=>'tabel32'], ['nationaliteitcode'=>$code])['hydra:member'];
        if (
            count($nationaliteiten) > 0 &&
            $fetchedNationaliteit = $nationaliteiten[0]
        ) {
            if (key_exists('nationaliteitcode', $fetchedNationaliteit) && key_exists('omschrijving', $fetchedNationaliteit)) {
                $nationaliteit->setCode($fetchedNationaliteit['nationaliteitcode']);
                $nationaliteit->setOmschrijving($fetchedNationaliteit['omschrijving']);
            } else {
                $nationaliteit->setCode('0001');
                $nationaliteit->setOmschrijving('Nederlandse');
            }
        } else {
            $nationaliteit->setCode('0001');
            $nationaliteit->setOmschrijving('Nederlandse');
        }

        return $nationaliteit;
    }

    public function getLand(?string $code): Waardetabel
    {
        $country = new Waardetabel();
        $countries = $this->commonGroundService->getResourceList(['component'=>'ltc', 'type'=>'tabel34'], ['landcode'=>$code])['hydra:member'];
        if (
            count($countries) > 0 &&
            $fetchedCountry = $countries[0]
        ) {
            if (key_exists('landcode', $fetchedCountry) && key_exists('omschrijving', $fetchedCountry)) {
                $country->setCode($fetchedCountry['landcode']);
                $country->setOmschrijving($fetchedCountry['omschrijving']);
            } else {
                $country->setCode('0001');
                $country->setOmschrijving('Nederlandse');
            }
        } else {
            $country->setCode('0001');
            $country->setOmschrijving('Nederlandse');
        }

        return $country;
    }

    public function getGemeente(?string $code): Waardetabel
    {
        $geboorteplaats = new Waardetabel();
        $gemeentes = $this->commonGroundService->getResourceList(['component'=>'ltc', 'type'=>'tabel33'], ['gemeentecode'=>$code])['hydra:member'];
        if (
            count($gemeentes) > 0 &&
            $gemeente = $gemeentes[0]
        ) {
            if (key_exists('gemeentecode', $gemeente) && key_exists('omschrijving', $gemeente)) {
                $geboorteplaats->setCode($gemeente['gemeentecode']);
                $geboorteplaats->setOmschrijving($gemeente['omschrijving']);
            } else {
                $geboorteplaats->setCode('0000');
                $geboorteplaats->setOmschrijving('Onbekend');
            }
        } else {
            $geboorteplaats->setCode('0000');
            $geboorteplaats->setOmschrijving('Onbekend');
        }

        return $geboorteplaats;
    }
}
