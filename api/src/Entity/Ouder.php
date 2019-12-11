<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}},
 *     denormalizationContext={"groups"={"write"}}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\OuderRepository")
 * @ApiResource(
 *     collectionOperations={"get"={"method"="GET","path"="/ingeschrevenpersonen/{burgerservicenummer}/ouders.{_format}","swagger_context" = {"summary"="ingeschrevenNatuurlijkPersoonOuders", "description"="Beschrijving"}}},
 *     itemOperations={"get"={"method"="GET","path"="/ingeschrevenpersonen/{burgerservicenummer}/ouders/{uuid}.{_format}","swagger_context" = {"summary"="ingeschrevenNatuurlijkPersoonOuderUuid", "description"="Beschrijving"}}}
 * )
 * @Gedmo\Loggable
 */
class Ouder
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
     * @var string $burgerservicenummer Burgerservicenummer of this Ouder
     * @example 123456782
     *
     * @Groups({"read", "write"})
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(
     *     max  = 255
     * )
     */
    private $burgerservicenummer;

    /**
     * @var string $geslachtsaanduiding Geslachts aanduiding of this Ouder
     * @example female
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=7)
     * @Assert\NotBlank
     * @Assert\Length(
     *     max  = 7
     * )
     */
    private $geslachtsaanduiding;

    /**
     * @todo docblocks
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=7)
     * @Assert\NotBlank
     * @Assert\Length(
     *     max  = 7
     * )
     */
    private $ouderAanduiding;

    /**
     *
     * @var string $burgerservicenummer Burgerservicenummer of this Ouder
     * @example 123456782
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="incompleteDate", nullable=true)
     */
    private $datumIngangFamilierechtelijkeBetreking;

    /**
     *
     * @var NaamPersoon $naam Naam of this Ouder
     * @example Joe
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\OneToOne(targetEntity="App\Entity\NaamPersoon", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, referencedColumnName="uuid")
     * @MaxDepth(1)
     */
    private $naam;

    /**
     * @todo docblocks
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="underInvestigation", nullable=true)
     */
    private $inOnderzoek;

    /**
     *
     * @var Geboorte $geboorte Geboorte of this Ouder
     * @example 01-01-2000
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\OneToOne(targetEntity="App\Entity\Geboorte", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, referencedColumnName="uuid")
     * @MaxDepth(1)
     */
    private $geboorte;

    /**
     *
     * @var Ingeschrevenpersoon $ingeschrevenpersoon IngeschrevenPersoon of this Ouder
     * @example Joe
     *
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="App\Entity\Ingeschrevenpersoon", inversedBy="ouders")
     * @ORM\JoinColumn(nullable=false)
     * @MaxDepth(1)
     */
    private $ingeschrevenpersoon;

    // On an object level we stil want to be able to gett the id
    public function getId(): ?string
    {
    	return $this->uuid;
    }

    public function getUuid(): ?string
    {
    	return $this->uuid;
    }

    public function getBurgerservicenummer(): ?string
    {
        return $this->burgerservicenummer;
    }

    public function setBurgerservicenummer(string $burgerservicenummer): self
    {
        $this->burgerservicenummer = $burgerservicenummer;

        return $this;
    }

    public function getGeslachtsaanduiding(): ?string
    {
        return $this->geslachtsaanduiding;
    }

    public function setGeslachtsaanduiding(string $geslachtsaanduiding): self
    {
        $this->geslachtsaanduiding = $geslachtsaanduiding;

        return $this;
    }

    public function getOuderAanduiding(): ?string
    {
        return $this->ouderAanduiding;
    }

    public function setOuderAanduiding(string $ouderAanduiding): self
    {
        $this->ouderAanduiding = $ouderAanduiding;

        return $this;
    }

    public function getDatumIngangFamilierechtelijkeBetreking()
    {
        return $this->datumIngangFamilierechtelijkeBetreking;
    }

    public function setDatumIngangFamilierechtelijkeBetreking($datumIngangFamilierechtelijkeBetreking): self
    {
        $this->datumIngangFamilierechtelijkeBetreking = $datumIngangFamilierechtelijkeBetreking;

        return $this;
    }

    public function getNaam(): ?NaamPersoon
    {
        return $this->naam;
    }

    public function setNaam(NaamPersoon $naam): self
    {
        $this->naam = $naam;

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

    public function getGeboorte(): ?Geboorte
    {
        return $this->geboorte;
    }

    public function setGeboorte(Geboorte $geboorte): self
    {
        $this->geboorte = $geboorte;

        return $this;
    }

    public function getIngeschrevenpersoon(): ?Ingeschrevenpersoon
    {
    	return $this->ingeschrevenpersoon;
    }

    public function setIngeschrevenpersoon(?Ingeschrevenpersoon $ingeschrevenpersoon): self
    {
    	$this->ingeschrevenpersoon = $ingeschrevenpersoon;

        return $this;
    }
}
