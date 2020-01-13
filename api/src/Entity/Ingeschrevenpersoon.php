<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
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
 *     			"path"="/ingeschrevenpersonen",
 *     			"swagger_context" = {
 *     				"summary"="ingeschrevenNatuurlijkPersonen",
 *     				"description"="Beschrijving",
 *                  "parameters" = {
 *                      {
 *                          "in" = "query",
 *                          "name" = "expand",
 *                          "description" = "Hier kan aangegeven worden welke gerelateerde resources meegeladen moeten worden. Resources en velden van resources die gewenst zijn kunnen in de expand parameter kommagescheiden worden opgegeven. Specifieke velden van resource kunnen worden opgegeven door het opgeven van de resource-naam gevolgd door de veldnaam, met daartussen een punt. Zie [functionele specificaties](https://github.com/VNG-Realisatie/Bevragingen-ingeschreven-personen/blob/master/features/expand.feature)",
 *                          "required" = "true",
 *                          "type" : "string",
 *                      },
 *                      {
 *                      	"in": "query",
 *                          "name": "fields",
 *                          "description": "Geeft de mogelijkheid de inhoud van de body van het antwoord naar behoefte aan te passen. Bevat een door komma's gescheiden lijst van veldennamen. Als niet-bestaande veldnamen worden meegegeven wordt een 400 Bad Request teruggegeven. Wanneer de parameter fields niet is opgenomen, worden alle gedefinieerde velden die een waarde hebben teruggegeven. Zie [functionele specificaties](https://github.com/VNG-Realisatie/Bevragingen-ingeschreven-personen/blob/master/features/fields.feature)",
 *                          "required": false,
 *                          "type": "string"
 *                      },
 *                      {
 *                      	"in": "query",
 *                          "name": "burgerservicenummer",
 *                          "description": "Het burgerservicenummer, bedoeld in artikel 1.1 van de Wet algemene bepalingen burgerservicenummer. Alle nummers waarvoor geldt dat, indien aangeduid als (s0 s1 s2 s3 s4 s5 s6 s7 s8), het resultaat van (9*s0) + (8*s1) + (7*s2) +...+ (2*s7) - (1*s8) deelbaar is door elf. Er moeten dus 9 cijfers aanwezig zijn.",
 *                          "required": false,
 *                          "type": "string",
 *                          "maxLength": 9,
 *                          "minLength": 9
 *                      },
 *                      {
 *                      	"in": "query",
 *                          "name": "familie__eerstegraad",
 *                          "description": "Filterd op de eerstegraads familie leden van een opgegeven burgerservicenummer",
 *                          "required": false,
 *                          "type": "string",
 *                          "maxLength": 9,
 *                          "minLength": 9
 *                      },
 *                      {
 *                      	"in": "query",
 *                          "name": "geboorte__datum",
 *                          "description": "Datum waarop de INGESCHREVEN NATUURLIJK PERSOON geboren is. Er kan alleen gezocht worden met een volledige geboortedatum. Zie [functionele specificaties](https://github.com/VNG-Realisatie/Bevragingen-ingeschreven-personen/blob/master/features/parametervalidatie.feature)",
 *                          "required": false,
 *                          "type": "string",
 *                          "format": "date",
 *                          "example": "1964-09-24"
 *                      },
 *                      {
 *                      	"in": "query",
 *                          "name": "geboorte__plaats",
 *                          "description": "Gemeentenaam of een buitenlandse plaats of een plaatsbepaling, die aangeeft waar de persoon is geboren. **Zoeken met tekstvelden is [case-Insensitive](https://github.com/VNG-Realisatie/Bevragingen-ingeschreven-personen/blob/master/features/case_insensitive.feature).**",
 *                          "required": false,
 *                          "type": "string",
 *                          "maxLength": 40,
 *                          "example": "Utrecht"
 *                      },
 *                      {
 *                      	"in": "query",
 *                          "name": "geslachtsaanduiding",
 *                          "description": "Een aanduiding die aangeeft dat de ingeschrevene een man of een vrouw is, of dat het geslacht (nog) onbekend is.",
 *                          "required": false
 *                      },
 *                      {
 *                      	"in": "query",
 *                          "name": "inclusiefoverledenpersonen",
 *                          "description": "Indien in het antwoord op de zoekvraag ook overleden personen moeten worden geretourneerd, dan dient de parameter *inclusiefOverledenPersonen* opgenomen te zijn met de waarde _True_. Indien de parameter *inclusiefOverledenPersonen* ontbreekt of de waarde _False_ heeft worden geen overleden personen opgenomen in het zoekresultaat. Zie [functionele specificaties](https://github.com/VNG-Realisatie/Bevragingen-ingeschreven-personen/blob/master/features/overleden_personen.feature)",
 *                          "required": false,
 *                          "type": "boolean",
 *                          "example": false
 *                      },
 *                      {
 *                      	"in": "query",
 *                          "name": "naam__geslachtsnaam",
 *                          "description": "De (geslachts)naam waarvan de eventueel aanwezige voorvoegsels en adellijke titel/predikaat zijn afgesplitst. **Gebruik van de wildcard is toegestaan. Zie [feature-beschrijving](https://github.com/VNG-Realisatie/Bevragingen-ingeschreven-personen/blob/master/features/wildcard.feature)** **Zoeken met tekstvelden is [case-Insensitive](https://github.com/VNG-Realisatie/Bevragingen-ingeschreven-personen/blob/master/features/case_insensitive.feature).**",
 *                          "required": false,
 *                          "type": "string",
 *                          "maxLength": 200,
 *                          "example": "Vries"
 *                      },
 *                      {
 *                      	"in": "query",
 *                          "name": "naam__voornamen",
 *                          "description": "De verzameling namen die, gescheiden door spaties, aan de geslachtsnaam voorafgaat. ** Bij deze query-parameter is het gebruik van een [wildcard](https://github.com/VNG-Realisatie/Bevragingen-ingeschreven-personen/blob/master/features/wildcard.feature) toegestaan in combinatie met minimaal 2 karakters.** **Zoeken met tekstvelden is [case-Insensitive](https://github.com/VNG-Realisatie/Bevragingen-ingeschreven-personen/blob/master/features/case_insensitive.feature).**",
 *                          "required": false,
 *                          "type": "string",
 *                          "maxLength": 200,
 *                          "example": "Dirk"
 *                      },
 *                      {
 *                      	"in": "query",
 *                          "name": "verblijfplaats__gemeentevaninschrijving",
 *                          "description": "Een code die aangeeft in welke gemeente de PL zich bevindt of de gemeente waarnaar de PL is uitgeschreven of de gemeente waar de PL voor de eerste keer is opgenomen. De waarde (0000) is geen geldige inhoud voor de query-parameter.",
 *                          "required": false,
 *                          "type": "string",
 *                          "maxLength": 4,
 *                          "example": "0518"
 *                      },
 *                      {
 *                      	"in": "query",
 *                          "name": "verblijfplaats__huisletter",
 *                          "description": "Een door of namens het bevoegd gemeentelijk orgaan ten aanzien van een adresseerbaar object toegekende toevoeging aan een huisnummer in de vorm van een alfanumeriek teken.",
 *                          "required": false,
 *                          "type": "string",
 *                          "maxLength": 1,
 *                          "example": "a"
 *                      },
 *                      {
 *                      	"in": "query",
 *                          "name": "verblijfplaats__huisnummer",
 *                          "description": "Een door of namens het bevoegd gemeentelijk orgaan ten aanzien van een adresseerbaar object toegekende nummering. Alle natuurlijke getallen tussen 1 en 99999.",
 *                          "required": false,
 *                          "type": "integer",
 *                          "maximum": 99999,
 *                          "example": 14
 *                      },
 *                      {
 *                      	"in": "query",
 *                          "name": "verblijfplaats__huisnummertoevoeging",
 *                          "description": "Een door of namens het bevoegd gemeentelijk orgaan ten aanzien van een adresseerbaar object toegekende nadere toevoeging aan een huisnummer of een combinatie van huisnummer en huisletter. ",
 *                          "required": false,
 *                          "type": "string",
 *                          "maxLength": 4,
 *                          "example": "bis"
 *                      },
 *                      {
 *                      	"in": "query",
 *                          "name": "verblijfplaats__identificatiecodenummeraanduiding",
 *                          "description": "De unieke aanduiding van een NUMMERAANDUIDING. Combinatie van de viercijferige 'gemeentecode' , de tweecijferige 'objecttypecode' en een voor het betreffende objecttype binnen een gemeente uniek tiencijferig 'objectvolgnummer'. De objecttypecode kent in de BAG de volgende waarde:20 nummeraanduiding.",
 *                          "required": false,
 *                          "type": "string",
 *                          "maxLength": 16,
 *                          "example": "0518200000366054"
 *                      },
 *                      {
 *                      	"in": "query",
 *                          "name": "verblijfplaats__naamopenbareruimte",
 *                          "description": "Een door het bevoegde gemeentelijke orgaan aan een OPENBARE RUIMTE toegekende benaming **Gebruik van de wildcard is toegestaan. Zie [feature-beschrijving](https://github.com/VNG-Realisatie/Bevragingen-ingeschreven-personen/blob/master/features/wildcard.feature)** **Zoeken met tekstvelden is [case-Insensitive](https://github.com/VNG-Realisatie/Bevragingen-ingeschreven-personen/blob/master/features/case_insensitive.feature).** Tekens gecodeerd volgens de UTF-8 standaard",
 *                          "required": false,
 *                          "type": "string",
 *                          "maxLength": 80,
 *                          "example": "Tulpstraat"
 *                      },
 *                      {
 *                      	"in": "query",
 *                          "name": "verblijfplaats__postcode",
 *                          "description": "De door PostNL vastgestelde code behorende bij een bepaalde combinatie van een naam van een woonplaats, naam van een openbare ruimte en een huisnummer",
 *                          "required": false,
 *                          "type": "string",
 *                          "pattern": "^[1-9]{1}[0-9]{3}[A-Z]{2}$",
 *                          "example": "2341SX"
 *                      },
 *                      {
 *                      	"in": "query",
 *                          "name": "naam__voorvoegsel",
 *                          "description": "Dat deel van de geslachtsnaam dat voorkomt in de Voorvoegseltabel en, gescheiden door een spatie, vooraf gaat aan de rest van de geslachtsnaam. **De tabel bevat vorvoegsels met hoofdletters en met kleine letters. Het zoeken op het voorvoegsel is [case-Insensitive](https://github.com/VNG-Realisatie/Bevragingen-ingeschreven-personen/blob/master/features/case_insensitive.feature).**",
 *                          "required": false,
 *                          "type": "string",
 *                          "maxLength": 10,
 *                          "example": "de"
 *                      }
 *                  }
 *     			}
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
