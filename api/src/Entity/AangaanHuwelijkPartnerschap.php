<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}, "enable_max_depth"=true},
 *     denormalizationContext={"groups"={"write"}, "enable_max_depth"=true}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\AangaanHuwelijkPartnerschapRepository")
 * @Gedmo\Loggable
 */
class AangaanHuwelijkPartnerschap
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
     * @var string $datum Datum this huwelijk has been requested
     * @example 01-01-2000
     *
     * @Groups({"read","write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="incompleteDate")
     */
    private $datum;

    /**
     * @var string $land Land this huwelijk is in
     * @example The Netherlands
     *
     * @Groups({"read","write"})
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="App\Entity\Waardetabel")
     * @MaxDepth(1)
     */
    private $land;

    /**
     * @var string $plaats Plaats this huwelijk is in
     * @example Amsterdam
     *
     * @Groups({"read","write"})
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="App\Entity\Waardetabel")
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
     * @var Partner $partner Other partner of this huwelijk
     * @example John
     *
     * @Groups({"read","write"})
     * @Gedmo\Versioned
     * @ORM\OneToOne(targetEntity="App\Entity\Partner", mappedBy="aangaanHuwelijkPartnerschap", cascade={"persist", "remove"})
     * @MaxDepth(1)
     */
    private $partner;

    // On an object level we stil want to be able to gett the id
    public function getId(): ?string
    {
    	return $this->uuid;
    }

    public function getUuid(): ?string
    {
    	return $this->uuid;
    }

    public function getDatum()
    {
        return $this->datum;
    }

    public function setDatum($datum): self
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

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    public function setPartner(Partner $partner): self
    {
        $this->partner = $partner;

        // set the owning side of the relation if necessary
        if ($this !== $partner->getAangaanHuwelijkPartnerschap()) {
            $partner->setAangaanHuwelijkPartnerschap($this);
        }

        return $this;
    }
}
