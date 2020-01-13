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
 *     normalizationContext={"groups"={"read"}, "enable_max_depth"=true},
 *     denormalizationContext={"groups"={"write"}, "enable_max_depth"=true}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\GezagsverhoudingRepository")
 * @Gedmo\Loggable
 */
class Gezagsverhouding
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
     * @ORM\Column(type="boolean")
     * @Assert\Type("bool")
     * @Assert\NotBlank
     */
    private $indicatieCurateleRegister;

    /**
     * @todo docblocks
     * @Groups({"read","write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255
     * )
     */
    private $indicatieGezagMinderjarige;

    /**
     * @todo docblocks
     * @Groups({"read","write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="underInvestigation", nullable=true)
     */
    private $inOnderzoek;

    /**
     * @todo docblocks
     * @Gedmo\Versioned
     * @ORM\OneToOne(targetEntity="App\Entity\Ingeschrevenpersoon", mappedBy="gezagsverhouding", cascade={"persist", "remove"})
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

    public function getIndicatieCurateleRegister(): ?bool
    {
        return $this->indicatieCurateleRegister;
    }

    public function setIndicatieCurateleRegister(bool $indicatieCurateleRegister): self
    {
        $this->indicatieCurateleRegister = $indicatieCurateleRegister;

        return $this;
    }

    public function getIndicatieGezagMinderjarige(): ?string
    {
        return $this->indicatieGezagMinderjarige;
    }

    public function setIndicatieGezagMinderjarige(?string $indicatieGezagMinderjarige): self
    {
        $this->indicatieGezagMinderjarige = $indicatieGezagMinderjarige;

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
        $newGezagsverhouding = $ingeschrevenpersoon === null ? null : $this;
        if ($newGezagsverhouding !== $ingeschrevenpersoon->getGezagsverhouding()) {
            $ingeschrevenpersoon->setGezagsverhouding($newGezagsverhouding);
        }

        return $this;
    }
}
