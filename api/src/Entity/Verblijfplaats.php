<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}},
 *     denormalizationContext={"groups"={"write"}}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\VerblijfplaatsRepository")
 * @Gedmo\Loggable
 */
class Verblijfplaats
{
    /**
     * @var UuidInterface
     * @example e2984465-190a-4562-829e-a8cca81aa35d
     *
     * @Groups({"read"})
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
	private $uuid;

    /**
     * @todo docblocks
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max  = 255
     * )
     */
    private $aanduidingBijHuisnummer;

    /**
     * @todo docblocks
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max  = 255
     * )
     */
    private $funtieAdres;

    /**
     * @var string $huisletter Huisletter of this Verblijfplaats
     * @example B
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max  = 255
     * )
     */
    private $huisletter;

    /**
     * @var integer $huisnummer Huisnummer of this Verblijfplaats
     * @example 21
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type("integer")
     */
    private $huisnummer;

    /**
     * @var string $huisnummertoevoeging Huisnummertoevoeging of this Verblijfplaats
     * @example B
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255
     * )
     */
    private $huisnummertoevoeging;

    /**
     * @todo docblocks
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255
     * )
     */
    private $identificatiecodeNummeraanduiding;

    /**
     * @todo docblocks
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255
     * )
     */
    private $identificatiecodeVerblijfplaats;

    /**
     * @todo docblocks
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="boolean")
     * @Assert\Type("boolean")
     */
    private $indentificatieVestigingVanuitBuitenland  = false;

    /**
     * @var string $locatiebeschrijving Locatiebeschrijving of this Verblijfplaats
     * @example Appartment
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255
     * )
     */
    private $locatiebeschrijving;

    /**
     * @todo docblocks
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255
     * )
     */
    private $naamOpenbareRuimte;

    /**
     * @var string $postcode Postcode of this Verblijfplaats
     * @example 08040
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255
     * )
     */
    private $postcode;

    /**
     * @var string $straatnaam Straatnaam of this Verblijfplaats
     * @example Passeig de Sant Joan
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255
     * )
     */
    private $straatnaam;

    /**
     * @todo docblocks
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="boolean")
     * @Assert\Type("boolean")
     */
    private $vanuitVertrokkenOnbekendWaarheen = false;

    /**
     * @var string $woonplaatsnaam Woonplaatsnaam of this Verblijfplaats
     * @example Barcelona
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255
     * )
     */
    private $woonplaatsnaam;

    /**
     * @var string $datumAanvangAdreshouding Datum aanvang adreshouding of this Verblijfplaats
     * @example 2005-01-01
     *
     * @Groups({"read", "write"})
     * @ORM\Column(type="object", nullable=true)
     */
    private $datumAanvangAdreshouding;

    /**
     * @var string $datumIngangGeldigheid Datum ingang geldigheid of this Verblijfplaats
     * @example 01-01-2005
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="incompleteDate", nullable=true)
     */
    private $datumIngangGeldigheid;

    /**
     * @var string $datumInschrijvingInGemeente Datum inschrijving in gemeente of this Verblijfplaats
     * @example 01-01-2005
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="incompleteDate", nullable=true)
     */
    private $datumInschrijvingInGemeente;

    /**
     * @var string $datumVestigingInNederland Datum vestiging in Nederland of this Verblijfplaats
     * @example 01-01-2005
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="incompleteDate", nullable=true)
     */
    private $datumVestigingInNederland;

    /**
     * @var string $gemeenteVanInschrijving Gemeente van inschrijving of this Verblijfplaats
     * @example Barcelona
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", nullable=true)
     */
    private $gemeenteVanInschrijving;

    /**
     * @var string $landVanwaarIngeschreven Land van  waar ingeschreven of this Verblijfplaats
     * @example Spain
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="App\Entity\Waardetabel")
     * @MaxDepth(1)
     */
    private $landVanwaarIngeschreven;

    /**
     * @var VerblijfBuitenland $verblijfBuitenland VerblijfBuitenland of this Verblijfplaats
     * @example Spain
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\OneToOne(targetEntity="App\Entity\VerblijfBuitenland", inversedBy="verblijfplaats", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true, referencedColumnName="uuid")
     * @MaxDepth(1)
     */
    private $verblijfBuitenland;

    /**
     * @todo docblocks
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="underInvestigation", nullable=true)
     */
    private $inOnderzoek;

    /**
     * @todo docblocks
     *
     * @Gedmo\Versioned
     * @ORM\OneToOne(targetEntity="App\Entity\Ingeschrevenpersoon", mappedBy="verblijfplaats", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     * @MaxDepth(1)
     */
    private $ingeschrevenpersoon;

    /**
     * @todo docblocks
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bagId;

    // On an object level we stil want to be able to gett the id
    public function getId(): ?string
    {
    	return $this->uuid;
    }

    public function getUuid(): ?string
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

    public function getIdentificatiecodeNummeraanduiding(): ?string
    {
        return $this->identificatiecodeNummeraanduiding;
    }

    public function setIdentificatiecodeNummeraanduiding(?string $identificatiecodeNummeraanduiding): self
    {
        $this->identificatiecodeNummeraanduiding = $identificatiecodeNummeraanduiding;

        return $this;
    }

    public function getIdentificatiecodeVerblijfplaats(): ?string
    {
        return $this->identificatiecodeVerblijfplaats;
    }

    public function setIdentificatiecodeVerblijfplaats(?string $identificatiecodeVerblijfplaats): self
    {
        $this->identificatiecodeVerblijfplaats = $identificatiecodeVerblijfplaats;

        return $this;
    }

    public function getIndentificatieVestigingVanuitBuitenland(): ?bool
    {
        return $this->indentificatieVestigingVanuitBuitenland;
    }

    public function setIndentificatieVestigingVanuitBuitenland(bool $indentificatieVestigingVanuitBuitenland): self
    {
        $this->indentificatieVestigingVanuitBuitenland = $indentificatieVestigingVanuitBuitenland;

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

    public function getWoonplaatsnaam(): ?string
    {
        return $this->woonplaatsnaam;
    }

    public function setWoonplaatsnaam(?string $woonplaatsnaam): self
    {
        $this->woonplaatsnaam = $woonplaatsnaam;

        return $this;
    }

    public function getDatumAanvangAdreshouding()
    {
        return $this->datumAanvangAdreshouding;
    }

    public function setDatumAanvangAdreshouding($datumAanvangAdreshouding): self
    {
        $this->datumAanvangAdreshouding = $datumAanvangAdreshouding;

        return $this;
    }

    public function getDatumIngangGeldigheid()
    {
        return $this->datumIngangGeldigheid;
    }

    public function setDatumIngangGeldigheid($datumIngangGeldigheid): self
    {
        $this->datumIngangGeldigheid = $datumIngangGeldigheid;

        return $this;
    }

    public function getDatumInschrijvingInGemeente()
    {
        return $this->datumInschrijvingInGemeente;
    }

    public function setDatumInschrijvingInGemeente($datumInschrijvingInGemeente): self
    {
        $this->datumInschrijvingInGemeente = $datumInschrijvingInGemeente;

        return $this;
    }

    public function getDatumVestigingInNederland()
    {
        return $this->datumVestigingInNederland;
    }

    public function setDatumVestigingInNederland($datumVestigingInNederland): self
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

    public function getVerblijfBuitenland(): ?VerblijfBuitenland
    {
        return $this->verblijfBuitenland;
    }

    public function setVerblijfBuitenland(?VerblijfBuitenland $verblijfBuitenland): self
    {
        $this->verblijfBuitenland = $verblijfBuitenland;

        return $this;
    }

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

    public function setIngeschrevenpersoon(Ingeschrevenpersoon$ingeschrevenpersoon): self
    {
    	$this->ingeschrevenpersoon= $ingeschrevenpersoon;

        // set the owning side of the relation if necessary
    	if ($this !== $ingeschrevenpersoon->getVerblijfplaats()) {
    		$ingeschrevenpersoon->setVerblijfplaats($this);
        }

        return $this;
    }

    public function getBagId(): ?int
    {
        return $this->bagId;
    }

    public function setBagId(?int $bagId): self
    {
        $this->bagId = $bagId;

        return $this;
    }
}
