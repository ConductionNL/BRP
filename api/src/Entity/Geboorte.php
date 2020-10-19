<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Conduction\CommonGroundBundle\ValueObject\IncompleteDate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}, "enable_max_depth"=true},
 *     denormalizationContext={"groups"={"write"}, "enable_max_depth"=true}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\GeboorteRepository")
 * @Gedmo\Loggable
 *
 * @ApiFilter(SearchFilter::class, properties={
 *     "datum":"exact",
 *     "plaats.omschrijving":"ipartial",
 * })
 */
class Geboorte
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
     * @var string Land this person is born in
     *
     * @example The Netherlands
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="App\Entity\Waardetabel", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @MaxDepth(1)
     */
    private $land;

    /**
     * @var string Plaats this person is born in
     *
     * @example Amsterdam
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="App\Entity\Waardetabel", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @MaxDepth(1)
     */
    private $plaats;

    /**
     * @var IncompleteDate Datum this person is born at
     *
     * @example 01-01-2000
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="incompleteDate",nullable=true)
     */
    private $datum;

    /**
     * @todo docblocks
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="underInvestigation", nullable=true)
     */
    private $inOnderzoek;

    /**
     * @todo docblocks
     * @ORM\OneToMany(targetEntity="App\Entity\Ingeschrevenpersoon", mappedBy="geboorte")
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

    public function getDatum() :IncompleteDate
    {
        return $this->datum;
    }

    public function setDatum(IncompleteDate $datum): self
    {
        $this->datum = $datum;

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

    public function getPlaats(): ?Waardetabel
    {
        return $this->plaats;
    }

    public function setPlaats(?Waardetabel $plaats): self
    {
        $this->plaats = $plaats;

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
            $ingeschrevenpersonen->setGeboorte($this);
        }

        return $this;
    }

    public function removeIngeschrevenpersonen(Ingeschrevenpersoon $ingeschrevenpersonen): self
    {
        if ($this->ingeschrevenpersonen->contains($ingeschrevenpersonen)) {
            $this->ingeschrevenpersonen->removeElement($ingeschrevenpersonen);
            // set the owning side to null (unless already changed)
            if ($ingeschrevenpersonen->getGeboorte() === $this) {
                $ingeschrevenpersonen->setGeboorte(null);
            }
        }

        return $this;
    }
}
