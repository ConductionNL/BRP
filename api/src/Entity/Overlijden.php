<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Conduction\CommonGroundBundle\ValueObject\IncompleteDate;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}, "enable_max_depth"=true},
 *     denormalizationContext={"groups"={"write"}, "enable_max_depth"=true}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\OverlijdenRepository")
 * @Gedmo\Loggable
 */
class Overlijden
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
     * @var bool Indicatie overleden of this Overlijden
     *
     * @example false
     *
     * @Groups({"read","write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank
     * @Assert\Type("boolean")
     */
    private $indicatieOverleden;

    /**
     * @var IncompleteDate Datum of this Overlijden
     *
     * @example 01-01-2000
     *
     * @Groups({"read","write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="incompleteDate")
     */
    private $datum;

    /**
     * @var string Land of this Overlijden
     *
     * @example The Netherlands
     *
     * @ApiProperty(
     * 	   identifier=true,
     *     attributes={
     *         "swagger_context"={
     *         	   "description" = "Land of this overlijden",
     *             "type"="string",
     *             "example"="The Netherlands"
     *         }
     *     }
     * )
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="App\Entity\Waardetabel", cascade={"persist"})
     * @MaxDepth(1)
     */
    private $land;

    /**
     * @var string Plaats of this Overlijden
     *
     * @example Amsterdam
     *
     * @ApiProperty(
     * 	   identifier=true,
     *     attributes={
     *         "swagger_context"={
     *         	   "description" = "Plaats of this overlijden",
     *             "type"="string",
     *             "example"="Amsterdam"
     *         }
     *     }
     * )
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="App\Entity\Waardetabel", cascade={"persist"})
     * @MaxDepth(1)
     */
    private $plaats;

    /**
     * @todo docblocks
     * @Groups({"read","write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="underInvestigation", nullable=true)
     */
    private $inOnderzoek;

    /**
     * @todo docblocks
     * @ORM\OneToOne(targetEntity="App\Entity\Ingeschrevenpersoon", mappedBy="overlijden", cascade={"persist", "remove"})
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

    public function getIndicatieOverleden(): ?bool
    {
        return $this->indicatieOverleden;
    }

    public function setIndicatieOverleden(bool $indicatieOverleden): self
    {
        $this->indicatieOverleden = $indicatieOverleden;

        return $this;
    }

    public function getDatum():IncompleteDate
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

    public function getIngeschrevenpersoon(): ?Ingeschrevenpersoon
    {
        return $this->ingeschrevenpersoon;
    }

    public function setIngeschrevenpersoon(?Ingeschrevenpersoon $ingeschrevenpersoon): self
    {
        $this->ingeschrevenpersoon = $ingeschrevenpersoon;

        // set (or unset) the owning side of the relation if necessary
        $newOverlijden = $ingeschrevenpersoon === null ? null : $this;
        if ($newOverlijden !== $ingeschrevenpersoon->getOverlijden()) {
            $ingeschrevenpersoon->setOverlijden($newOverlijden);
        }

        return $this;
    }
}
