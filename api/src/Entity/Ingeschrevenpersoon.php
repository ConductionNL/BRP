<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}, "enable_max_depth"=true},
 *     denormalizationContext={"groups"={"write"}, "enable_max_depth"=true},
 *     subresourceOperations={
 *          "api_ingeschrevenpersoons_kinderens_get_subresource"={
 *              "method"="GET",
 *              "path"="/ingeschrevenpersonen/{burgerservicenummer}/kinderen",
 *              "swagger_context" = {"summary"="ingeschrevenNatuurlijkPersonenKinderen", "description"="Beschrijving"}
 *          },
 *          "api_ingeschrevenpersoons_ouders_get_subresource"={
 *              "method"="GET",
 *              "path"="/ingeschrevenpersonen/{burgerservicenummer}/ouders",
 *              "swagger_context" = {"summary"="ingeschrevenNatuurlijkPersonenOuders", "description"="Beschrijving"}
 *          },
 *          "api_ingeschrevenpersoons_partners_get_subresource"={
 *              "method"="GET",
 *              "path"="/ingeschrevenpersonen/{burgerservicenummer}/partners",
 *              "swagger_context" = {"summary"="ingeschrevenNatuurlijkPersonenPartners", "description"="Beschrijving"}
 *          },
 *      },
 *     collectionOperations={
 *     		"get"={
 *     			"method"="GET",
 *     			"path"="/ingeschrevenpersonen"
 *     		},
 *     		"get_on_bsn"={
 *     			"method"="GET",
 *     			"path"="/ingeschrevenpersonen/{burgerservicenummer}",
 *     			"requirements"={"burgerservicenummer"="\d+"},
 *     			"defaults"={"color"="brown"},
 *     			"options"={"my_option"="my_option_value"},
 *     			"swagger_context" = {
 *     				"summary"="ingeschrevenNatuurlijkPersoon",
 *     				"description"="Beschrijving"
 *     			}
 *     		},
 *     		"get_bsn_ouders"={
 *     			"method"="GET",
 *     			"path"="/ingeschrevenpersonen/{burgerservicenummer}/ouders",
 *     			"requirements"={"burgerservicenummer"="\d+"},
 *     			"defaults"={"color"="brown"},
 *     			"options"={"my_option"="my_option_value"},
 *     			"swagger_context" = {
 *     				"summary"="ingeschrevenNatuurlijkPersoon",
 *     				"description"="Beschrijving"
 *     			}
 *     		},
 *     		"get_bsn_kinderen"={
 *     			"method"="GET",
 *     			"path"="/ingeschrevenpersonen/{burgerservicenummer}/kinderen",
 *     			"requirements"={"burgerservicenummer"="\d+"},
 *     			"defaults"={"color"="brown"},
 *     			"options"={"my_option"="my_option_value"},
 *     			"swagger_context" = {
 *     				"summary"="ingeschrevenNatuurlijkPersoon",
 *     				"description"="Beschrijving"
 *     			}
 *     		},
 *     		"get_bsn_partners"={
 *     			"method"="GET",
 *     			"path"="/ingeschrevenpersonen/{burgerservicenummer}/partners",
 *     			"requirements"={"burgerservicenummer"="\d+"},
 *     			"defaults"={"color"="brown"},
 *     			"options"={"my_option"="my_option_value"},
 *     			"swagger_context" = {
 *     				"summary"="ingeschrevenNatuurlijkPersoon",
 *     				"description"="Beschrijving"
 *     			}
 *     		},
 *     },
 *     itemOperations={
 *     		"get"={
 *     			"method"="GET",
 *     			"path"="/ingeschrevenpersonen/uuid/{id}",
 *     			"swagger_context" = {"summary"="ingeschrevenNatuurlijkPersoonUui", "description"="Beschrijving"}
 *     		}
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\IngeschrevenpersoonRepository")
 * @Gedmo\Loggable
 *
 * @ApiFilter(SearchFilter::class, properties={
 *     "geboorte.datum":"exact",
 *     "geboorte.plaats":"ipartional",
 *     "geslachtsaanduiding":"exact",
 *     "burgerservicenummer":"exact",
 *     "naam.geslachtsnaam":"ipartial",
 *     "naam.voornamen":"ipartional",
 *     "naam.voorvoegsel":"ipartional",
 *     "verblijfplaats.postcode": "exact",
 *     "verblijfplaats.huisnummer":"exact",
 *     "verblijfplaats.huisnummertoevoeging":"exact",
 *     "verblijfplaats.huisletter":"exact",
 *     "verblijfplaats.naamopenbareruimte":"exact",
 *     "verblijfplaats.gemeentevaninschrijving":"exact",
 *     "verblijfplaats.identificatiecodenummeraanduiding":"exact",
 * })
 */
class Ingeschrevenpersoon
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
    private $id;

    /**
     * @var string Burgerservicenummer of this ingeschreven persoon
     *
     * @example 123456782
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=9)
     * @Assert\NotBlank
     * @Assert\Length(
     *     max = 9
     * )
     */
    private $burgerservicenummer;

    /**
     * @var string Naam of this ingeschreven persoon
     *
     * @example John
     *
     * @Groups({"read", "write"})
     * @ORM\ManyToOne(targetEntity="App\Entity\NaamPersoon", inversedBy="ingeschrevenpersonen", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, referencedColumnName="uuid")
     * @MaxDepth(1)
     */
    private $naam;

    /**
     * @var Geboorte Geboorte of this ingeschreven persoon
     *
     * @example 01-01-2000
     *
     * @Groups({"read", "write"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Geboorte", inversedBy="ingeschrevenpersonen", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, referencedColumnName="uuid")
     * @MaxDepth(1)
     */
    private $geboorte;

    /**
     * @var bool Geheim houding persoongegevens of this ingeschreven persoon
     *
     * @example true
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank
     * @Assert\Type("bool")
     */
    private $geheimhoudingPersoonsgegevens;

    /**
     * @var string Geslachts aanduiding of this ingeschreven persoon
     *
     * @example male
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length=7)
     * @Assert\NotBlank
     * @Assert\Length(
     *     max = 7
     * )
     */
    private $geslachtsaanduiding;

    /**
     * @var int Leeftijd of this ingeschreven persoon
     *
     * @example 18
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type("integer")
     */
    private $leeftijd;

    /**
     * @var string Datum eerste inschrijving gba of this ingeschreven persoon
     *
     * @example 01-01-2000
     *
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="incompleteDate",nullable=true)
     */
    private $datumEersteInschrijvingGBA;

    /**
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="object")
     */
    private $kiesrecht;

    /**
     * @todo docblocks
     * @Groups({"read", "write"})
     * @Gedmo\Versioned
     * @ORM\Column(type="underInvestigation", nullable=true)
     */
    private $inOnderzoek;

    /**
     * @var string Nationaliteit of this ingeschreven persoon
     *
     * @example Dutch
     *
     * @Groups({"read", "write"})
     * @ORM\OneToMany(targetEntity="App\Entity\Nationaliteit", mappedBy="ingeschrevenpersoon", orphanRemoval=true)
     * @MaxDepth(1)
     */
    private $nationaliteit;

    /**
     * @Groups({"read", "write"})
     * @ORM\OneToOne(targetEntity="App\Entity\OpschortingBijhouding", inversedBy="ingeschrevenpersoon", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true, referencedColumnName="uuid")
     * @MaxDepth(1)
     */
    private $opschortingBijhouding;

    /**
     * @var Overlijden Checks if ingeschreven persoon is overlijden
     *
     * @example false
     *
     * @Groups({"read", "write"})
     * @ORM\OneToOne(targetEntity="App\Entity\Overlijden", inversedBy="ingeschrevenpersoon", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true, referencedColumnName="uuid")
     * @MaxDepth(1)
     */
    private $overlijden;

    /**
     * @var Overlijden Checks if ingeschreven persoon is overlijden
     *
     * @example false
     *
     * @Groups({"read", "write"})
     * @ORM\OneToOne(targetEntity="App\Entity\Verblijfplaats", inversedBy="ingeschrevenpersoon", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true, referencedColumnName="uuid")
     * @MaxDepth(1)
     */
    private $verblijfplaats;

    /**
     * @todo docblocks
     *
     * @Groups({"read", "write"})
     * @ORM\OneToOne(targetEntity="App\Entity\Gezagsverhouding", inversedBy="ingeschrevenpersoon", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true, referencedColumnName="uuid")
     * @MaxDepth(1)
     */
    private $gezagsverhouding;

    /**
     * @todo docblocks
     *
     * @Groups({"read", "write"})
     * @ORM\OneToOne(targetEntity="App\Entity\Verblijfstitel", inversedBy="ingeschrevenpersoon", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true, referencedColumnName="uuid")
     * @MaxDepth(1)
     */
    private $verblijfstitel;

    /**
     * @var Ouder Ouders of ingeschreven persoon
     *
     * @example James, Jessica
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Ouder", mappedBy="ingeschrevenpersoon", orphanRemoval=true, cascade={"persist", "remove"})
     * @MaxDepth(1)
     */
    private $ouders;

    /**
     * @var Kind Kinderen of ingeschreven persoon
     *
     * @example John
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Kind", mappedBy="ingeschrevenpersoon", orphanRemoval=true, cascade={"persist", "remove"})
     * @MaxDepth(1)
     */
    private $kinderen;

    /**
     * @var Partner Partner of ingeschreven persoon
     *
     * @example Mike
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Partner", mappedBy="ingeschrevenpersoon", orphanRemoval=true, cascade={"persist", "remove"})
     * @MaxDepth(1)
     */
    private $partners;

    public function __construct()
    {
        $this->nationaliteit = new ArrayCollection();
        $this->ouders = new ArrayCollection();
        $this->kinderen = new ArrayCollection();
        $this->partners = new ArrayCollection();
    }

    // On an object level we stil want to be able to gett the id
    public function getId(): ?string
    {
        return $this->id;
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

    public function getGeheimhoudingPersoonsgegevens(): ?bool
    {
        return $this->geheimhoudingPersoonsgegevens;
    }

    public function setGeheimhoudingPersoonsgegevens(bool $geheimhoudingPersoonsgegevens): self
    {
        $this->geheimhoudingPersoonsgegevens = $geheimhoudingPersoonsgegevens;

        return $this;
    }

    public function getGeslachtsaanduiding(): ?string
    {
        return $this->geslachtsaanduiding;
    }

    public function setGeslachtsaanduiding(string $geslachtsaanduiding): self
    {
        $this->geslachtsaanduiding = $geslachtsaanduiding;

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

    public function getDatumEersteInschrijvingGBA()
    {
        return $this->datumEersteInschrijvingGBA;
    }

    public function setDatumEersteInschrijvingGBA($datumEersteInschrijvingGBA): self
    {
        $this->datumEersteInschrijvingGBA = $datumEersteInschrijvingGBA;

        return $this;
    }

    public function getKiesrecht()
    {
        return $this->kiesrecht;
    }

    public function setKiesrecht($kiesrecht): self
    {
        $this->kiesrecht = $kiesrecht;

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
     * @return Collection|Nationaliteit[]
     */
    public function getNationaliteit(): Collection
    {
        return $this->nationaliteit;
    }

    public function addNationaliteit(Nationaliteit $nationaliteit): self
    {
        if (!$this->nationaliteit->contains($nationaliteit)) {
            $this->nationaliteit[] = $nationaliteit;
            $nationaliteit->setNatuurlijkPersoon($this);
        }

        return $this;
    }

    public function removeNationaliteit(Nationaliteit $nationaliteit): self
    {
        if ($this->nationaliteit->contains($nationaliteit)) {
            $this->nationaliteit->removeElement($nationaliteit);
            // set the owning side to null (unless already changed)
            if ($nationaliteit->getNatuurlijkPersoon() === $this) {
                $nationaliteit->setNatuurlijkPersoon(null);
            }
        }

        return $this;
    }

    public function getOpschortingBijhouding(): ?OpschortingBijhouding
    {
        return $this->opschortingBijhouding;
    }

    public function setOpschortingBijhouding(?OpschortingBijhouding $opschortingBijhouding): self
    {
        $this->opschortingBijhouding = $opschortingBijhouding;

        return $this;
    }

    public function getOverlijden(): ?Overlijden
    {
        return $this->overlijden;
    }

    public function setOverlijden(?Overlijden $overlijden): self
    {
        $this->overlijden = $overlijden;

        return $this;
    }

    public function getVerblijfplaats(): ?Verblijfplaats
    {
        return $this->verblijfplaats;
    }

    public function setVerblijfplaats(Verblijfplaats $verblijfplaats): self
    {
        $this->verblijfplaats = $verblijfplaats;

        return $this;
    }

    public function getGezagsverhouding(): ?Gezagsverhouding
    {
        return $this->gezagsverhouding;
    }

    public function setGezagsverhouding(?Gezagsverhouding $gezagsverhouding): self
    {
        $this->gezagsverhouding = $gezagsverhouding;

        return $this;
    }

    public function getVerblijfstitel(): ?Verblijfstitel
    {
        return $this->verblijfstitel;
    }

    public function setVerblijfstitel(?Verblijfstitel $verblijfstitel): self
    {
        $this->verblijfstitel = $verblijfstitel;

        return $this;
    }

    /**
     * @return Collection|Ouder[]
     */
    public function getOuders(): Collection
    {
        return $this->ouders;
    }

    public function addOuder(Ouder $ouder): self
    {
        if (!$this->ouders->contains($ouder)) {
            $this->ouders[] = $ouder;
            $ouder->setIngeschrevenpersoon($this);
        }

        return $this;
    }

    public function removeOuder(Ouder $ouder): self
    {
        if ($this->ouders->contains($ouder)) {
            $this->ouders->removeElement($ouder);
            // set the owning side to null (unless already changed)
            if ($ouder->getIngeschrevenpersoon() === $this) {
                $ouder->setIngeschrevenpersoon(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Kind[]
     */
    public function getKinderen(): Collection
    {
        return $this->kinderen;
    }

    public function addKind(Kind $kinderen): self
    {
        if (!$this->kinderen->contains($kinderen)) {
            $this->kinderen[] = $kinderen;
            $kinderen->setIngeschrevenpersoon($this);
        }

        return $this;
    }

    public function removeKind(Kind $kinderen): self
    {
        if ($this->kinderen->contains($kinderen)) {
            $this->kinderen->removeElement($kinderen);
            // set the owning side to null (unless already changed)
            if ($kinderen->getIngeschrevenpersoon() === $this) {
                $kinderen->setIngeschrevenpersoon(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Partner[]
     */
    public function getPartners(): Collection
    {
        return $this->partners;
    }

    public function addPartner(Partner $partner): self
    {
        if (!$this->partners->contains($partner)) {
            $this->partners[] = $partner;
            $partner->setIngeschrevenpersoon($this);
        }

        return $this;
    }

    public function removePartner(Partner $partner): self
    {
        if ($this->partners->contains($partner)) {
            $this->partners->removeElement($partner);
            // set the owning side to null (unless already changed)
            if ($partner->getIngeschrevenpersoon() === $this) {
                $partner->setIngeschrevenpersoon(null);
            }
        }

        return $this;
    }

    public function getGeboorte(): ?Geboorte
    {
        return $this->geboorte;
    }

    public function setGeboorte(?Geboorte $geboorte): self
    {
        $this->geboorte = $geboorte;

        return $this;
    }

    public function getNaam(): ?NaamPersoon
    {
        return $this->naam;
    }

    public function setNaam(?NaamPersoon $naam): self
    {
        $this->naam = $naam;

        return $this;
    }
}
