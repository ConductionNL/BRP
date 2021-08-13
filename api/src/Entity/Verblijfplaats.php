<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Conduction\CommonGroundBundle\ValueObject\IncompleteDate;
use Conduction\CommonGroundBundle\ValueObject\UnderInvestigation;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}},
 *     denormalizationContext={"groups"={"write"}}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\VerblijfplaatsRepository")
 * @Gedmo\Loggable
 * @ApiFilter(SearchFilter::class, properties={
 *     "postcode": "exact",
 *     "huisnummer":"exact",
 *     "huisnummertoevoeging":"exact",
 *     "huisletter":"exact",
 *     "naamopenbareruimte":"exact",
 *     "gemeentevaninschrijving":"exact",
 *     "identificatiecodenummeraanduiding":"exact",
 *     })
 */
class Verblijfplaats
{
    /**
     * @var UuidInterface
     *
     * @example e2984465-190a-4562-829e-a8cca81aa35d
     *
     * @Groups({"read"})
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private UuidInterface $uuid;

    /**
     * @todo docblocks
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max  = 255
     * )
     */
    private ?string $aanduidingBijHuisnummer = null;

    /**
     * @todo docblocks
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max  = 255
     * )
     */
    private ?string $funtieAdres = null;

    /**
     * @var string|null Huisletter of this Verblijfplaats
     *
     * @example B
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max  = 255
     * )
     */
    private ?string $huisletter = null;

    /**
     * @var int|null Huisnummer of this Verblijfplaats
     *
     * @example 21
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type("integer")
     */
    private ?int $huisnummer = null;

    /**
     * @var string Huisnummertoevoeging of this Verblijfplaats
     *
     * @example B
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255
     * )
     */
    private ?string $huisnummertoevoeging = null;

    /**
     * @todo docblocks
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255
     * )
     */
    private ?string $nummeraanduidingIdentificatie = null;

    /**
     * @todo docblocks
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255
     * )
     */
    private ?string $adresseerbaarObjectIdentificatie = null;

    /**
     * @todo docblocks
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\Column(type="boolean")
     * @Assert\Type("boolean")
     */
    private bool $indicatieVestigingVanuitBuitenland = false;

    /**
     * @var string|null Locatiebeschrijving of this Verblijfplaats
     *
     * @example Appartment
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255
     * )
     */
    private ?string $locatiebeschrijving = null;

    /**
     * @todo docblocks
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255
     * )
     */
    private ?string $naamOpenbareRuimte = null;

    /**
     * @var string Postcode of this Verblijfplaats
     *
     * @example 08040
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255
     * )
     */
    private ?string $postcode = null;

    /**
     * @var string|null Straatnaam of this Verblijfplaats
     *
     * @example Passeig de Sant Joan
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255
     * )
     */
    private ?string $straatnaam = null;

    /**
     * @todo docblocks
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\Column(type="boolean")
     * @Assert\Type("boolean")
     */
    private bool $vanuitVertrokkenOnbekendWaarheen = false;

    /**
     * @todo docblocks
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\Column(type="boolean")
     * @Assert\Type("boolean")
     */
    private bool $vertrokkenOnbekendWaarheen = false;

    /**
     * @var string|null Woonplaatsnaam of this Verblijfplaats
     *
     * @example Barcelona
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255
     * )
     */
    private ?string $woonplaats = null;

    /**
     * @var IncompleteDate|null Datum aanvang adreshouding of this Verblijfplaats
     *
     * @example 2005-01-01
     *
     * @Groups({"read", "write", "show_family"})
     * @ORM\Column(type="incompleteDate", nullable=true)
     */
    private ?IncompleteDate $datumAanvangAdreshouding = null;

    /**
     * @var IncompleteDate|null Datum ingang geldigheid of this Verblijfplaats
     *
     * @example 01-01-2005
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\Column(type="incompleteDate", nullable=true)
     */
    private ?IncompleteDate $datumIngangGeldigheid = null;

    /**
     * @var IncompleteDate|null Datum inschrijving in gemeente of this Verblijfplaats
     *
     * @example 01-01-2005
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\Column(type="incompleteDate", nullable=true)
     */
    private ?IncompleteDate $datumInschrijvingInGemeente = null;

    /**
     * @var IncompleteDate|null Datum vestiging in Nederland of this Verblijfplaats
     *
     * @example 01-01-2005
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\Column(type="incompleteDate", nullable=true)
     */
    private ?IncompleteDate $datumVestigingInNederland = null;

    /**
     * @var Waardetabel|null Gemeente van inschrijving of this Verblijfplaats
     *
     * @example Barcelona
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="App\Entity\Waardetabel", cascade={"persist"})
     * @MaxDepth(1)
     */
    private ?Waardetabel $gemeenteVanInschrijving = null;

    /**
     * @var string Land van  waar ingeschreven of this Verblijfplaats
     *
     * @example Spain
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="App\Entity\Waardetabel", cascade={"persist"})
     * @MaxDepth(1)
     */
    private ?Waardetabel $landVanwaarIngeschreven = null;

//    /**
//     * @var VerblijfBuitenland VerblijfBuitenland of this Verblijfplaats
//     *
//     * @example Spain
//     *
//     * @Groups({"read", "write", "show_family"})
//     * @Gedmo\Versioned
//     * @ORM\OneToOne(targetEntity="App\Entity\VerblijfBuitenland", inversedBy="verblijfplaats", cascade={"persist", "remove"})
//     * @ORM\JoinColumn(nullable=true, referencedColumnName="uuid")
//     * @MaxDepth(1)
//     */
//    private ?$verblijfBuitenland;

    /**
     * @todo docblocks
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\Column(type="underInvestigation", nullable=true)
     */
    private ?UnderInvestigation $inOnderzoek = null;

    /**
     * @todo docblocks
     *
     * @Gedmo\Versioned
     * @ORM\OneToOne(targetEntity="App\Entity\Ingeschrevenpersoon", mappedBy="verblijfplaats", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     * @MaxDepth(1)
     */
    private ?Ingeschrevenpersoon $ingeschrevenpersoon = null;

//    /**
//     * @todo docblocks
//     *
//     * @Groups({"read", "write", "show_family"})
//     * @Gedmo\Versioned
//     * @ORM\Column(type="integer", nullable=true)
//     */
//    private $bagId;

    /**
     * @Groups({"read"})
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $adresregel1 = null;

    /**
     * @Groups({"read"})
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $adresregel2 = null;

    /**
     * @Groups({"read"})
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $adresregel3 = null;

    /**
     * @var Waardetabel|null Land of this VerblijfBuitenland
     *
     * @example Spain
     *
     * @Gedmo\Versioned
     * @Groups({"read"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Waardetabel", cascade={"persist"})
     * @MaxDepth(1)
     */
    private ?Waardetabel $land = null;

    /**
     * @Groups({"read"})
     * @ORM\Column(type="incompleteDate",nullable=true)
     */
    private ?IncompleteDate $datumTot = null;

    /**
     * @ORM\Column(type="incompleteDate",nullable=true)
     */
    private ?IncompleteDate $datumAanvangAdresBuitenland = null;


    public function getId(): ?string
    {
        return $this->uuid;
    }

    // On an object level we stil want to be able to gett the id
    public function getUuid(): ?UuidInterface
    {
        return $this->uuid;
    }

    public function getAanduidingBijHuisnummer(): ?string
    {
        return $this->aanduidingBijHuisnummer;
    }

    public function setAanduidingBijHuisnummer(?string $aanduidingBijHuisnummer): self
    {
        $this->aanduidingBijHuisnummer = $aanduidingBijHuisnummer;

        return $this;
    }

    public function getFuntieAdres(): ?string
    {
        return $this->funtieAdres;
    }

    public function setFuntieAdres(?string $funtieAdres): self
    {
        $this->funtieAdres = $funtieAdres;

        return $this;
    }

    public function getHuisletter(): ?string
    {
        return $this->huisletter;
    }

    public function setHuisletter(?string $huisletter): self
    {
        $this->huisletter = $huisletter;

        return $this;
    }

    public function getHuisnummer(): ?int
    {
        return $this->huisnummer;
    }

    public function setHuisnummer(?int $huisnummer): self
    {
        $this->huisnummer = $huisnummer;

        return $this;
    }

    public function getHuisnummertoevoeging(): ?string
    {
        return $this->huisnummertoevoeging;
    }

    public function setHuisnummertoevoeging(?string $huisnummertoevoeging): self
    {
        $this->huisnummertoevoeging = $huisnummertoevoeging;

        return $this;
    }

    public function getNummeraanduidingIdentificatie(): ?string
    {
        return $this->nummeraanduidingIdentificatie;
    }

    public function setNummeraanduidingIdentificatie(?string $nummeraanduidingIdentificatie): self
    {
        $this->nummeraanduidingIdentificatie = $nummeraanduidingIdentificatie;

        return $this;
    }

    public function getAdresseerbaarObjectIdentificatie(): ?string
    {
        return $this->adresseerbaarObjectIdentificatie;
    }

    public function setAdresseerbaarObjectIdentificatie(?string $adresseerbaarObjectIdentificatie): self
    {
        $this->adresseerbaarObjectIdentificatie = $adresseerbaarObjectIdentificatie;

        return $this;
    }

    public function getIndicatieVestigingVanuitBuitenland(): ?bool
    {
        return $this->indicatieVestigingVanuitBuitenland;
    }

    public function setIndicatieVestigingVanuitBuitenland(bool $indicatieVestigingVanuitBuitenland): self
    {
        $this->indicatieVestigingVanuitBuitenland = $indicatieVestigingVanuitBuitenland;

        return $this;
    }

    public function getLocatiebeschrijving(): ?string
    {
        return $this->locatiebeschrijving;
    }

    public function setLocatiebeschrijving(?string $locatiebeschrijving): self
    {
        $this->locatiebeschrijving = $locatiebeschrijving;

        return $this;
    }

    public function getNaamOpenbareRuimte(): ?string
    {
        return $this->naamOpenbareRuimte;
    }

    public function setNaamOpenbareRuimte(?string $naamOpenbareRuimte): self
    {
        $this->naamOpenbareRuimte = $naamOpenbareRuimte;

        return $this;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(?string $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function getStraatnaam(): ?string
    {
        return $this->straatnaam;
    }

    public function setStraatnaam(?string $straatnaam): self
    {
        $this->straatnaam = $straatnaam;

        return $this;
    }

    public function getVanuitVertrokkenOnbekendWaarheen(): ?bool
    {
        return $this->vanuitVertrokkenOnbekendWaarheen;
    }

    public function setVanuitVertrokkenOnbekendWaarheen(bool $vanuitVertrokkenOnbekendWaarheen): self
    {
        $this->vanuitVertrokkenOnbekendWaarheen = $vanuitVertrokkenOnbekendWaarheen;

        return $this;
    }

    public function getVertrokkenOnbekendWaarheen(): ?bool
    {
        return $this->vertrokkenOnbekendWaarheen;
    }

    public function setVertrokkenOnbekendWaarheen(bool $vertrokkenOnbekendWaarheen): self
    {
        $this->vertrokkenOnbekendWaarheen = $vertrokkenOnbekendWaarheen;

        return $this;
    }

    public function getWoonplaats(): ?string
    {
        return $this->woonplaats;
    }

    public function setWoonplaats(?string $woonplaats): self
    {
        $this->woonplaats = $woonplaats;

        return $this;
    }

    public function getDatumAanvangAdreshouding(): ?IncompleteDate
    {
        return $this->datumAanvangAdreshouding;
    }

    public function setDatumAanvangAdreshouding(?IncompleteDate $datumAanvangAdreshouding): self
    {
        $this->datumAanvangAdreshouding = $datumAanvangAdreshouding;

        return $this;
    }

    public function getDatumIngangGeldigheid(): ?IncompleteDate
    {
        return $this->datumIngangGeldigheid;
    }

    public function setDatumIngangGeldigheid(?IncompleteDate $datumIngangGeldigheid): self
    {
        $this->datumIngangGeldigheid = $datumIngangGeldigheid;

        return $this;
    }

    public function getDatumInschrijvingInGemeente(): ?IncompleteDate
    {
        return $this->datumInschrijvingInGemeente;
    }

    public function setDatumInschrijvingInGemeente(?IncompleteDate $datumInschrijvingInGemeente): self
    {
        $this->datumInschrijvingInGemeente = $datumInschrijvingInGemeente;

        return $this;
    }

    public function getDatumVestigingInNederland(): ?IncompleteDate
    {
        return $this->datumVestigingInNederland;
    }

    public function setDatumVestigingInNederland(?IncompleteDate $datumVestigingInNederland): self
    {
        $this->datumVestigingInNederland = $datumVestigingInNederland;

        return $this;
    }

    public function getGemeenteVanInschrijving(): ?Waardetabel
    {
        return $this->gemeenteVanInschrijving;
    }

    public function setGemeenteVanInschrijving(?Waardetabel $gemeenteVanInschrijving): self
    {
        $this->gemeenteVanInschrijving = $gemeenteVanInschrijving;

        return $this;
    }

    public function getLandVanwaarIngeschreven(): ?Waardetabel
    {
        return $this->landVanwaarIngeschreven;
    }

    public function setLandVanwaarIngeschreven(?Waardetabel $landVanwaarIngeschreven): self
    {
        $this->landVanwaarIngeschreven = $landVanwaarIngeschreven;

        return $this;
    }

//    public function getVerblijfBuitenland(): ?VerblijfBuitenland
//    {
//        return $this->verblijfBuitenland;
//    }
//
//    public function setVerblijfBuitenland(?VerblijfBuitenland $verblijfBuitenland): self
//    {
//        $this->verblijfBuitenland = $verblijfBuitenland;
//
//        return $this;
//    }

    public function getInOnderzoek()
    {
        return $this->inOnderzoek;
    }

    public function setInOnderzoek($inOnderzoek): self
    {
        $this->inOnderzoek = $inOnderzoek;

        return $this;
    }

    public function getIngeschrevenpersoon(): ?Ingeschrevenpersoon
    {
        return $this->ingeschrevenpersoon;
    }

    public function setIngeschrevenpersoon(Ingeschrevenpersoon $ingeschrevenpersoon): self
    {
        $this->ingeschrevenpersoon = $ingeschrevenpersoon;

        // set the owning side of the relation if necessary
        if ($this !== $ingeschrevenpersoon->getVerblijfplaats()) {
            $ingeschrevenpersoon->setVerblijfplaats($this);
        }

        return $this;
    }

//    public function getBagId(): ?int
//    {
//        return $this->bagId;
//    }
//
//    public function setBagId(?int $bagId): self
//    {
//        $this->bagId = $bagId;
//
//        return $this;
//    }

    public function getAdresregel1(): ?string
    {
        return $this->adresregel1;
    }

    public function setAdresregel1(?string $adresregel1): self
    {
        $this->adresregel1 = $adresregel1;

        return $this;
    }

    public function getAdresregel2(): ?string
    {
        return $this->adresregel2;
    }

    public function setAdresregel2(?string $adresregel2): self
    {
        $this->adresregel2 = $adresregel2;

        return $this;
    }

    public function getAdresregel3(): ?string
    {
        return $this->adresregel3;
    }

    public function setAdresregel3(?string $adresregel3): self
    {
        $this->adresregel3 = $adresregel3;

        return $this;
    }

    public function getDatumTot(): ?IncompleteDate
    {
        return $this->datumTot;
    }

    public function setDatumTot(?IncompleteDate $datumTot): self
    {
        $this->datumTot = $datumTot;

        return $this;
    }
    public function getDatumAanvangAdresBuitenland(): ?IncompleteDate
    {
        return $this->datumAanvangAdresBuitenland;
    }

    public function setDatumAanvangAdresBuitenland(?IncompleteDate $datumAanvangAdresBuitenland): self
    {
        $this->datumAanvangAdresBuitenland = $datumAanvangAdresBuitenland;

        return $this;
    }

    public function getLand(): ?Waardetabel
    {
        return $this->land;
    }

    public function setLand(?Waardetabel $land): self
    {
        $this->land = $land;

        return $this;
    }
}
