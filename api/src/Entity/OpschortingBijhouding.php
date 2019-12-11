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
 *     normalizationContext={"groups"={"read"}},
 *     denormalizationContext={"groups"={"write"}}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\OpschortingBijhoudingRepository")
 * @Gedmo\Loggable
 */
class OpschortingBijhouding
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
     * @var string $reden Reden of this OpschortingBijhouding
     * @example
     *
     * @Groups({"read","write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(
     *     max  = 255
     * )
     */
    private $reden;

    /**
     * @var string $datum Datum of this NaamPersoon
     * @example 01-01-2000
     *
     * @Groups({"read","write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="incompleteDate")
     */
    private $datum;

    /**
     * @todo docblocks
     * @Gedmo\Versioned
     * @ORM\OneToOne(targetEntity="App\Entity\Ingeschrevenpersoon", mappedBy="opschortingBijhouding", cascade={"persist", "remove"})
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

    public function getReden(): ?string
    {
        return $this->reden;
    }

    public function setReden(string $reden): self
    {
        $this->reden = $reden;

        return $this;
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

    public function getIngeschrevenpersoon(): ?Ingeschrevenpersoon
    {
    	return $this->ingeschrevenpersoon;
    }

    public function setIngeschrevenpersoon(?Ingeschrevenpersoon $ingeschrevenpersoon): self
    {
    	$this->ingeschrevenpersoon= $ingeschrevenpersoon;

        // set (or unset) the owning side of the relation if necessary
    	$newOpschortingBijhouding = $ingeschrevenpersoon=== null ? null : $this;
    	if ($newOpschortingBijhouding !== $ingeschrevenpersoon->getOpschortingBijhouding()) {
    		$ingeschrevenpersoon->setOpschortingBijhouding($newOpschortingBijhouding);
        }

        return $this;
    }
}
