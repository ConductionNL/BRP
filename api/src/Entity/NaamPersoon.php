<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}, "enable_max_depth"=true},
 *     denormalizationContext={"groups"={"write"}, "enable_max_depth"=true}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\NaamPersoonRepository")
 * @Gedmo\Loggable
 *
 *  * @ApiFilter(SearchFilter::class, properties={
 *     "geslachtsnaam":"ipartial",
 *     "voornamen":"ipartional",
 *     "voorvoegsel":"ipartional",
 * })
 */
class NaamPersoon
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
    private $uuid;

    /**
     * @var string Geslachtsnaam of this NaamPersoon
     *
     * @example male
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(
     *     max = 255
     * )
     */
    private $geslachtsnaam;

    /**
     * @var string Voorletters of this NaamPersoon
     *
     * @example A
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(
     *     max  = 255
     * )
     */
    private $voorletters;

    /**
     * @var string Voornamen of this NaamPersoon
     *
     * @example Michael Smith
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(
     *     max  = 255
     * )
     */
    private $voornamen;

    /**
     * @var string Voorvoegsel of this NaamPersoon
     *
     * @example van der
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(
     *     max  = 255
     * )
     */
    private $voorvoegsel;

    /**
     * @todo docblocks
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="underInvestigation", nullable=true)
     */
    private $inOnderzoek;

    /**
     * @var string Aanhef of this NaamPersoon
     *
     * @example Dhr
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(
     *     max  = 255
     * )
     */
    private $aanhef;

    /**
     * @todo docblocks
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255)
     */
    private $aanschrijfwijze;

    /**
     * @var string Geslachtsnaam of this NaamPersoon
     *
     * @example male
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(
     *     max  = 255
     * )
     */
    private $gebuikInLopendeTekst;

    /**
     * @todo docblocks
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Ingeschrevenpersoon", mappedBy="naam", cascade={"persist"})
     * @MaxDepth(1)
     */
    private $ingeschrevenpersonen;

    public function __construct()
    {
        $this->ingeschrevenpersonen = new ArrayCollection();
    }

    // On an object level we stil want to be able to gett the id
    public function getId(): ?string
    {
        return $this->uuid;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getGeslachtsnaam(): ?string
    {
        return $this->geslachtsnaam;
    }

    public function setGeslachtsnaam(string $geslachtsnaam): self
    {
        $this->geslachtsnaam = $geslachtsnaam;

        return $this;
    }

    public function getVoorletters(): ?string
    {
        return $this->voorletters;
    }

    public function setVoorletters(string $voorletters): self
    {
        $this->voorletters = $voorletters;

        return $this;
    }

    public function getVoornamen(): ?string
    {
        return $this->voornamen;
    }

    public function setVoornamen(string $voornamen): self
    {
        $this->voornamen = $voornamen;

        return $this;
    }

    public function getVoorvoegsel(): ?string
    {
        return $this->voorvoegsel;
    }

    public function setVoorvoegsel(string $voorvoegsel): self
    {
        $this->voorvoegsel = $voorvoegsel;

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

    public function getAanhef(): ?string
    {
        return $this->aanhef;
    }

    public function setAanhef(string $aanhef): self
    {
        $this->aanhef = $aanhef;

        return $this;
    }

    public function getAanschrijfwijze(): ?string
    {
        return $this->aanschrijfwijze;
    }

    public function setAanschrijfwijze(string $aanschrijfwijze): self
    {
        $this->aanschrijfwijze = $aanschrijfwijze;

        return $this;
    }

    public function getGebuikInLopendeTekst(): ?string
    {
        return $this->gebuikInLopendeTekst;
    }

    public function setGebuikInLopendeTekst(string $gebuikInLopendeTekst): self
    {
        $this->gebuikInLopendeTekst = $gebuikInLopendeTekst;

        return $this;
    }

    /**
     * @return Collection|Ingeschrevenpersoon[]
     */
    public function getIngeschrevenpersonen(): Collection
    {
        return $this->ingeschrevenpersonen;
    }

    public function addIngeschrevenpersonen(Ingeschrevenpersoon $ingeschrevenpersonen): self
    {
        if (!$this->ingeschrevenpersonen->contains($ingeschrevenpersonen)) {
            $this->ingeschrevenpersonen[] = $ingeschrevenpersonen;
            $ingeschrevenpersonen->setNaam($this);
        }

        return $this;
    }

    public function removeIngeschrevenpersonen(Ingeschrevenpersoon $ingeschrevenpersonen): self
    {
        if ($this->ingeschrevenpersonen->contains($ingeschrevenpersonen)) {
            $this->ingeschrevenpersonen->removeElement($ingeschrevenpersonen);
            // set the owning side to null (unless already changed)
            if ($ingeschrevenpersonen->getNaam() === $this) {
                $ingeschrevenpersonen->setNaam(null);
            }
        }

        return $this;
    }
}
