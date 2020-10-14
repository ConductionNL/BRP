<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
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
 * @ORM\Entity(repositoryClass="App\Repository\VerblijfBuitenlandRepository")
 * @Gedmo\Loggable
 */
class VerblijfBuitenland
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
     * @todo docblocks
     * @Groups({"read","write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max  = 255
     * )
     */
    private $adresregel1;

    /**
     * @todo docblocks
     *
     * @Groups({"read","write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max  = 255
     * )
     */
    private $adresRegel2;

    /**
     * @todo docblocks
     *
     * @Groups({"read","write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max  = 255
     * )
     */
    private $adresRegel3;

    /**
     * @todo docblocks
     *
     * @Groups({"read","write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="boolean")
     * @Assert\Type("boolean")
     */
    private $vertrokkenOnbekendWaarheen;

    /**
     * @var string Land of this VerblijfBuitenland
     *
     * @example Spain
     *
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="App\Entity\Waardetabel", cascade={"persist"})
     * @MaxDepth(1)
     */
    private $land;

    /**
     * @var string Plaats of this VerblijfBuitenland
     *
     * @example Barcelona
     *
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="App\Entity\Waardetabel", cascade={"persist"})
     * @MaxDepth(1)
     */
    private $plaats;

    /**
     * @var Verblijfplaats Verblijfplaats of this VerblijfBuitenland
     *
     * @example Passeig de Sant Joan 21
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Verblijfplaats", mappedBy="verblijfBuitenland", cascade={"persist", "remove"})
     * @MaxDepth(1)
     */
    private $verblijfplaats;

    public function getAdresregel1(): ?string
    {
        return $this->adresregel1;
    }

    public function setAdresregel1(?string $adresregel1): self
    {
        $this->adresregel1 = $adresregel1;

        return $this;
    }

    public function getAdresRegel2(): ?string
    {
        return $this->adresRegel2;
    }

    public function setAdresRegel2(?string $adresRegel2): self
    {
        $this->adresRegel2 = $adresRegel2;

        return $this;
    }

    public function getAdresRegel3(): ?string
    {
        return $this->adresRegel3;
    }

    public function setAdresRegel3(?string $adresRegel3): self
    {
        $this->adresRegel3 = $adresRegel3;

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

    public function getVerblijfplaats(): ?Verblijfplaats
    {
        return $this->verblijfplaats;
    }

    public function setVerblijfplaats(?Verblijfplaats $verblijfplaats): self
    {
        $this->verblijfplaats = $verblijfplaats;

        // set (or unset) the owning side of the relation if necessary
        $newVerblijfBuitenland = $verblijfplaats === null ? null : $this;
        if ($newVerblijfBuitenland !== $verblijfplaats->getVerblijfBuitenland()) {
            $verblijfplaats->setVerblijfBuitenland($newVerblijfBuitenland);
        }

        return $this;
    }
}
