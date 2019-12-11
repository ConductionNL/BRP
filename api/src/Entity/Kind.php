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
 * @ORM\Entity(repositoryClass="App\Repository\KindRepository")
 * @ApiResource(
 *     collectionOperations={"get"={"method"="GET","path"="/ingeschrevenpersonen/{burgerservicenummer}/kinderen.{_format}","swagger_context" = {"summary"="ingeschrevenNatuurlijkPersoonKinderen", "description"="Beschrijving"}}},
 *     itemOperations={"get"={"method"="GET","path"="/ingeschrevenpersonen/{burgerservicenummer}/kinderen/{uuid}.{_format}","swagger_context" = {"summary"="ingeschrevenNatuurlijkPersoonKindUuid", "description"="Beschrijving"}}}
 * )
 * @Gedmo\Loggable
 */
class Kind
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
     * @var string $burgerservicenummer Burgerservicenummer of this kind
     * @example 123456782
     *
     * @ORM\Column(type="string", length=9)
     * @Assert\NotBlank
     * @Assert\Length(
     *     max = 9
     * )
     */
    private $burgerservicenummer;

    /**
     * @var integer $leeftijd Leeftijd of this kind
     * @example 14
     *
     * @Groups({"read", "write"})
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type("integer")
     */
    private $leeftijd;

    /**
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="underInvestigation", nullable=true)
     */
    private $inOnderzoek;

    /**
     * @var NaamPersoon $naam NaamPersoon of this kind
     * @example Michael
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="App\Entity\NaamPersoon", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, referencedColumnName="uuid")
     * @MaxDepth(1)
     */
    private $naam;

    /**
     * @var Geboorte $geboorte Geboorte of this kind
     * @example 01-01-2000
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="App\Entity\Geboorte", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, referencedColumnName="uuid")
     * @MaxDepth(1)
     */
    private $geboorte;

    /**
     * @todo docblocks
     *
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="App\Entity\Ingeschrevenpersoon", inversedBy="kinderen")
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

    public function getLeeftijd(): ?int
    {
        return $this->leeftijd;
    }

    public function setLeeftijd(?int $leeftijd): self
    {
        $this->leeftijd = $leeftijd;

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

    public function getNaam(): ?NaamPersoon
    {
        return $this->naam;
    }

    public function setNaam(NaamPersoon $naam): self
    {
        $this->naam = $naam;

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
