<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
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
 * @ORM\Entity(repositoryClass="App\Repository\ReisdocumentRepository")
 * @Gedmo\Loggable
 */
class Reisdocument
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
    private $id;

    /**
     * @todo docblocks
     * @Groups({"read","write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(
     *     max  = 255
     * )
     */
    private $aanduidingInhoudingOfVermissing;

    /**
     * @var string $reisdocumentnummer Reisdocumentnummer of this Reisdocument
     * @example AB1234CD0
     *
     * @Groups({"read","write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(
     *     max  = 255
     * )
     */
    private $reisdocumentnummer;

    // On an object level we stil want to be able to gett the id
    public function getId(): ?string
    {
    	return $this->uuid;
    }

    public function getUuid(): ?string
    {
    	return $this->uuid;
    }

    public function setAanduidingInhoudingOfVermissing(string $aanduidingInhoudingOfVermissing): self
    {
        $this->aanduidingInhoudingOfVermissing = $aanduidingInhoudingOfVermissing;

        return $this;
    }

    public function getReisdocumentnummer(): ?string
    {
        return $this->reisdocumentnummer;
    }

    public function setReisdocumentnummer(string $reisdocumentnummer): self
    {
        $this->reisdocumentnummer = $reisdocumentnummer;

        return $this;
    }
}
