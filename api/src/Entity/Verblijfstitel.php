<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}},
 *     denormalizationContext={"groups"={"write"}}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\VerblijfstitelRepository")
 * @Gedmo\Loggable
 */
class Verblijfstitel
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
     *
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="App\Entity\Waardetabel", cascade={"persist"})
     * @MaxDepth(1)
     */
    private $aanduiding;

    /**
     * @var string Datum einde of this Verblijftitel
     *
     * @example 01-01-2005
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\Column(type="incompleteDate")
     */
    private $datumEinde;

    /**
     * @var string Datum ingang of this Verblijftitel
     *
     * @example 01-01-2004
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\Column(type="incompleteDate")
     */
    private $datumIngang;

    /**
     * @todo docblocks
     *
     * @Groups({"read", "write", "show_family"})
     * @Gedmo\Versioned
     * @ORM\Column(type="underInvestigation", nullable=true)
     */
    private $inOnderzoek;

    /**
     * @todo docblocks
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Ingeschrevenpersoon", mappedBy="verblijfstitel", cascade={"persist", "remove"})
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

    public function getAanduiding(): ?Waardetabel
    {
        return $this->aanduiding;
    }

    public function setAanduiding(?Waardetabel $aanduiding): self
    {
        $this->aanduiding = $aanduiding;

        return $this;
    }

    public function getDatumEinde()
    {
        return $this->datumEinde;
    }

    public function setDatumEinde($datumEinde): self
    {
        $this->datumEinde = $datumEinde;

        return $this;
    }

    public function getDatumIngang()
    {
        return $this->datumIngang;
    }

    public function setDatumIngang($datumIngang): self
    {
        $this->datumIngang = $datumIngang;

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
        $newVerblijfstitel = $ingeschrevenpersoon === null ? null : $this;
        if ($newVerblijfstitel !== $ingeschrevenpersoon->getVerblijfstitel()) {
            $ingeschrevenpersoon->setVerblijfstitel($newVerblijfstitel);
        }

        return $this;
    }
}
