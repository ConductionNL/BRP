<?php

namespace App\DataFixtures;

use App\Entity\AangaanHuwelijkPartnerschap;
use App\Entity\Geboorte;
use App\Entity\Ingeschrevenpersoon;
use App\Entity\Kind;
use App\Entity\NaamPersoon;
use App\Entity\OpschortingBijhouding;
use App\Entity\Ouder;
use App\Entity\Overlijden;
use App\Entity\Partner;
use App\Entity\Verblijfplaats;
use App\Entity\Waardetabel;
use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Conduction\CommonGroundBundle\ValueObject\IncompleteDate;
use Conduction\CommonGroundBundle\ValueObject\UnderInvestigation;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class BZKFixtures extends Fixture
{
    private $params;

    private $commonGroundService;

//    private $encoder;
//
    public function __construct(ParameterBagInterface $params, CommonGroundService $commonGroundService)
    {
        $this->params = $params;
        $this->commonGroundService = $commonGroundService;
//        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        ini_set('memory_limit', '2G');
        /*
         *  Basis waarde tabel
         */

        //if (!$this->params->get('app_build_all_fixtures') || $this->params->get('app_build_all_fixtures') == 'false') {
            $this->loadFromExcel($manager, 'BZKgegevens');
        //}
    }

    public function createReader(): Xlsx
    {
        $reader = new Xlsx();
        $reader->setReadDataOnly(true);

        return $reader;
    }

    public function loadXlsx(string $filename): ?Spreadsheet
    {
        $reader = $this->createReader();

        try {
            return $reader->load($filename);
        } catch (Exception $e) {
        }

        return null;
    }

    public function iterateSpreadSheet(array $rows, int $highestRow, ObjectManager $manager)
    {
        $i = 0;
        foreach ($rows as $key=>$row) {
            if ($i == 0) {
                //skip the first line that contains the column title
                $i++;
                continue;
            } elseif ($i >= $highestRow) {
                break;
            } elseif ($row[1] || $row[28] || $row[51] || $row[91] || $row[186]) {
                if ($row[1]) {
                    $firstnamessplit = explode(' ', $row[2]);
                    $voorletters = '';

                    foreach ($firstnamessplit as $firstname) {
                        $voorletters .= mb_substr($firstname, 0, 1).'.';
                    }

                    $ingeschrevenpersoon = new Ingeschrevenpersoon();

                    $ingeschrevenpersoon->setNaam(new NaamPersoon());
                    $ingeschrevenpersoon->setGeboorte(new Geboorte());

                    $ingeschrevenpersoon->setBurgerservicenummer($row[1]);
                    $ingeschrevenpersoon->setGeheimhoudingPersoonsgegevens(false);
                    if ($row[9]) {
                        $ingeschrevenpersoon->setGeslachtsaanduiding($row[9]);
                    } else {
                        $ingeschrevenpersoon->setGeslachtsaanduiding('X');
                    }

                    if ($row[13] != '' || $row[14] != '' || $row[11] != '' || $row[12] != '') {
                        $ingeschrevenpersoon->setVerblijfplaats(new Verblijfplaats());

                        $ingeschrevenpersoon->getVerblijfplaats()->setPostcode($row[162]);
                        $ingeschrevenpersoon->getVerblijfplaats()->setWoonplaatsnaam($row[163]);
                        $ingeschrevenpersoon->getVerblijfplaats()->setStraatnaam($row[156]);
                        $ingeschrevenpersoon->getVerblijfplaats()->setHuisnummer($row[158]);
                        if ($row[159]) {
                            $ingeschrevenpersoon->getVerblijfplaats()->setHuisnummertoevoeging($row[159]);
                        } else {
                            $ingeschrevenpersoon->getVerblijfplaats()->setHuisnummertoevoeging('');
                        }
                        if ($row[160]) {
                            $ingeschrevenpersoon->getVerblijfplaats()->setHuisletter($row[160]);
                        }
                        $ingeschrevenpersoon->getVerblijfplaats()->setIngeschrevenpersoon($ingeschrevenpersoon);
                        if (array_key_exists(164, $row)) {
                            $ingeschrevenpersoon->getVerblijfplaats()->setIdentificatiecodeVerblijfplaats($row[164]);
                        }
                    }

                    $voorvoegsel = ''.$row[4];
                    $ingeschrevenpersoon->getNaam()->setGeslachtsnaam($row[4].' '.$row[5]);
                    $ingeschrevenpersoon->getNaam()->setVoorvoegsel($voorvoegsel);
                    if ($row[2]) {
                        $ingeschrevenpersoon->getNaam()->setVoornamen($row[2]);
                    } else {
                        $ingeschrevenpersoon->getNaam()->setVoornamen('');
                    }
                    $ingeschrevenpersoon->getNaam()->setVoorletters($voorletters);
                    if ($row[3]) {
                        $ingeschrevenpersoon->getNaam()->setAanhef($row[3]);
                        $ingeschrevenpersoon->getNaam()->setAanschrijfwijze($row[3].' '.$voorletters.' '.$row[4].' '.$row[5]);
                        $ingeschrevenpersoon->getNaam()->setGebuikInLopendeTekst($row[3].' '.$voorletters.' '.$row[4].' '.$row[5]);
                    } else {
                        $ingeschrevenpersoon->getNaam()->setAanhef('');
                        $ingeschrevenpersoon->getNaam()->setAanschrijfwijze($voorletters.' '.$row[4].' '.$row[5]);
                        $ingeschrevenpersoon->getNaam()->setGebuikInLopendeTekst($voorletters.' '.$row[4].' '.$row[5]);
                    }

                    $nationaliteit = new Waardetabel();
                    $nationaliteiten = $this->commonGroundService->getResourceList(['component'=>'ltc', 'type'=>'tabel32'], ['nationaliteitcode'=>$row[73]])['hydra:member'];
                    if (
                        count($nationaliteiten) > 0 &&
                        $fetchedNationaliteit = $nationaliteiten[0]
                    ) {
                        if (key_exists('nationaliteitcode', $fetchedNationaliteit) && key_exists('omschrijving', $fetchedNationaliteit)) {
                            $nationaliteit->setCode($fetchedNationaliteit['nationaliteitcode']);
                            $nationaliteit->setOmschrijving($fetchedNationaliteit['omschrijving']);
                        } else {
                            $nationaliteit->setCode('0001');
                            $nationaliteit->setOmschrijving('Nederlandse');
                        }
                    } else {
                        $nationaliteit->setCode('0001');
                        $nationaliteit->setOmschrijving('Nederlandse');
                    }
                    $geboorteplaats = new Waardetabel();
                    $gemeentes = $this->commonGroundService->getResourceList(['component'=>'ltc', 'type'=>'tabel33'], ['gemeentecode'=>$row[151]])['hydra:member'];
                    if (
                        count($gemeentes) > 0 &&
                        $gemeente = $gemeentes[0]
                    ) {
                        if (key_exists('gemeentecode', $gemeente) && key_exists('omschrijving', $gemeente)) {
                            $geboorteplaats->setCode($gemeente['gemeentecode']);
                            $geboorteplaats->setOmschrijving($gemeente['omschrijving']);
                        } else {
                            $geboorteplaats->setCode('1999');
                            $geboorteplaats->setOmschrijving('Registratie Niet Ingezetenen (RNI)');
                        }
                    } else {
                        $geboorteplaats->setCode('1999');
                        $geboorteplaats->setOmschrijving('Registratie Niet Ingezetenen (RNI)');
                    }

                    $ingeschrevenpersoon->getGeboorte()->setLand($nationaliteit);
                    $ingeschrevenpersoon->getGeboorte()->setPlaats($geboorteplaats);

                    try {
                        $geboortedatum = $row[6];
                        $ingeschrevenpersoon->getGeboorte()->setDatum(new IncompleteDate((int) substr($geboortedatum, 0, 4), (int) substr($geboortedatum, 4, 2), (int) substr($geboortedatum, 6, 2)));
                        $geboortedatum = new DateTime($row[6]);
                        $leeftijd = $geboortedatum->diff(new DateTime('now'), true)->format('%Y');
                        $ingeschrevenpersoon->setLeeftijd($leeftijd);
                    } catch (\Exception $e) {
                    }
                    if ($row[18]) {
                        $inOnderzoek = new UnderInvestigation(['aanduiding' => $row[18]], $row[19]);
                        $ingeschrevenpersoon->setInOnderzoek($inOnderzoek);
                    }
                    if ($row[142] > 0) {
                        $ingeschrevenpersoon->setGeheimhoudingPersoonsgegevens(true);
                    } else {
                        $ingeschrevenpersoon->setGeheimhoudingPersoonsgegevens(false);
                    }
                    if ($row[120]) {
                        $overlijden = new Overlijden();
                        $overlijdensdatum = $row[120];
                        $overlijden->setDatum(new IncompleteDate((int) substr($overlijdensdatum, 0, 4), (int) substr($overlijdensdatum, 4, 2), (int) substr($overlijdensdatum, 6, 2)));
                        $overlijden->setLand($nationaliteit);
                        $overlijden->setPlaats($geboorteplaats);
                        if ($row[131]) {
                            $overlijden->setIndicatieOverleden(false);
                        } else {
                            $overlijden->setIndicatieOverleden(true);
                        }

                        if ($row[128]) {
                            $overlijden->setInOnderzoek(new UnderInvestigation(['aanduiding' => $row[128]], $row[129]));
                        }
                        $overlijden->setIngeschrevenpersoon($ingeschrevenpersoon);

                        $ingeschrevenpersoon->setOverlijden($overlijden);
                        $manager->persist($overlijden);
                    }
                    if ($row[138]) {
                        $opschortingBijhouding = new OpschortingBijhouding();
                        $datumOpschorting = $row[138];
                        $opschortingBijhouding->setDatum(new IncompleteDate((int) substr($datumOpschorting, 0, 4), (int) substr($datumOpschorting, 4, 2), (int) substr($datumOpschorting, 6, 2)));
                        $opschortingBijhouding->setReden($row[139]);
                        $opschortingBijhouding->setIngeschrevenpersoon($ingeschrevenpersoon);
                        $ingeschrevenpersoon->setOpschortingBijhouding($opschortingBijhouding);
                        $manager->persist($opschortingBijhouding);
                    }

                    $manager->persist($nationaliteit);
                    $manager->persist($geboorteplaats);
                    $manager->persist($ingeschrevenpersoon);
                    $manager->flush();
                }

                if ($row[28]) {
                    $ouder = new Ouder();

                    $firstnamessplit = explode(' ', $row[29]);
                    $voorletters = '';

                    foreach ($firstnamessplit as $firstname) {
                        $voorletters .= mb_substr($firstname, 0, 1).'.';
                    }
                    $voorvoegsel = ''.$row[31];

                    $ouder->setBurgerservicenummer($row[28]);

                    $ouder->setNaam(new NaamPersoon());
                    $ouder->getNaam()->setGeslachtsnaam($row[31].' '.$row[32]);
                    $ouder->getNaam()->setVoorvoegsel($voorvoegsel);
                    $ouder->getNaam()->setVoornamen($row[29]);
                    $ouder->getNaam()->setVoorletters($voorletters);
                    if ($row[30]) {
                        $ouder->getNaam()->setAanhef($row[30]);
                        $ouder->getNaam()->setAanschrijfwijze($row[30].' '.$voorletters.' '.$row[31].' '.$row[32]);
                        $ouder->getNaam()->setGebuikInLopendeTekst($row[30].' '.$voorletters.' '.$row[31].' '.$row[32]);
                    } else {
                        $ouder->getNaam()->setAanhef('');
                        $ouder->getNaam()->setAanschrijfwijze($voorletters.' '.$row[31].' '.$row[32]);
                        $ouder->getNaam()->setGebuikInLopendeTekst($voorletters.' '.$row[31].' '.$row[32]);
                    }
                    if ($row[36]) {
                        $ouder->setGeslachtsaanduiding($row[36]);
                    } else {
                        $ouder->setGeslachtsaanduiding('X');
                    }
                    $ouder->setOuderAanduiding('ouder1');

                    $ouder->setGeboorte(new Geboorte());
                    $ouder->getGeboorte()->setLand($nationaliteit);
                    $ouder->getGeboorte()->setPlaats($geboorteplaats);

                    try {
                        $geboortedatum = $row[33];
                        $ouder->getGeboorte()->setDatum(new IncompleteDate((int) substr($geboortedatum, 0, 4), (int) substr($geboortedatum, 4, 2), (int) substr($geboortedatum, 6, 2)));
                    } catch (\Exception $e) {
                    }
                    if ($row[43]) {
                        $inOnderzoek = new UnderInvestigation(['aanduiding' => $row[43]], $row[44]);
                        $ouder->setInOnderzoek($inOnderzoek);
                    }

                    $manager->persist($ouder);
                    $ingeschrevenpersoon->addOuder($ouder);
                }
                if ($row[51]) {
                    $ouder = new Ouder();

                    $firstnamessplit = explode(' ', $row[52]);
                    $voorletters = '';

                    foreach ($firstnamessplit as $firstname) {
                        $voorletters .= mb_substr($firstname, 0, 1).'.';
                    }
                    $voorvoegsel = ''.$row[54];

                    $ouder->setBurgerservicenummer($row[51]);

                    $ouder->setNaam(new NaamPersoon());
                    $ouder->getNaam()->setGeslachtsnaam($row[54].' '.$row[55]);

                    $ouder->getNaam()->setVoorvoegsel($voorvoegsel);
                    $ouder->getNaam()->setVoornamen($row[52]);
                    $ouder->getNaam()->setVoorletters($voorletters);

                    if ($row[53]) {
                        $ouder->getNaam()->setAanhef($row[53]);
                        $ouder->getNaam()->setAanschrijfwijze($row[53].' '.$voorletters.' '.$ouder->getNaam()->getGeslachtsnaam());
                        $ouder->getNaam()->setGebuikInLopendeTekst($row[53].' '.$voorletters.' '.$ouder->getNaam()->getGeslachtsnaam());
                    } else {
                        $ouder->getNaam()->setAanhef('');
                        $ouder->getNaam()->setAanschrijfwijze($voorletters.' '.$ouder->getNaam()->getGeslachtsnaam());
                        $ouder->getNaam()->setGebuikInLopendeTekst($voorletters.' '.$ouder->getNaam()->getGeslachtsnaam());
                    }
                    if ($row[59]) {
                        $ouder->setGeslachtsaanduiding($row[59]);
                    } else {
                        $ouder->setGeslachtsaanduiding('X');
                    }
                    $ouder->setOuderAanduiding('ouder2');

                    $ouder->setGeboorte(new Geboorte());
                    $ouder->getGeboorte()->setLand($nationaliteit);
                    $ouder->getGeboorte()->setPlaats($geboorteplaats);

                    try {
                        $geboortedatum = $row[56];
                        $ouder->getGeboorte()->setDatum(new IncompleteDate((int) substr($geboortedatum, 0, 4), (int) substr($geboortedatum, 4, 2), (int) substr($geboortedatum, 6, 2)));
                    } catch (\Exception $e) {
                    }
                    if ($row[66]) {
                        $inOnderzoek = new UnderInvestigation(['aanduiding' => $row[66]], $row[67]);
                        $ouder->setInOnderzoek($inOnderzoek);
                    }

                    $manager->persist($ouder);
                    $ingeschrevenpersoon->addOuder($ouder);
                }
                if ($row[91]) {
                    $partner = new Partner();

                    $firstnamessplit = explode(' ', $row[92]);
                    $voorletters = '';

                    foreach ($firstnamessplit as $firstname) {
                        $voorletters .= mb_substr($firstname, 0, 1).'.';
                    }
                    $voorvoegsel = ''.$row[94];

                    $partner->setBurgerservicenummer($row[91]);

                    $partner->setNaam(new NaamPersoon());
                    $partner->getNaam()->setGeslachtsnaam($row[94].' '.$row[95]);
                    $partner->getNaam()->setVoorvoegsel($voorvoegsel);
                    $partner->getNaam()->setVoornamen($row[92]);
                    $partner->getNaam()->setVoorletters($voorletters);
                    if ($row[93]) {
                        $partner->getNaam()->setAanhef($row[93]);
                        $partner->getNaam()->setAanschrijfwijze($row[93].' '.$voorletters.' '.$row[94].' '.$row[95]);
                        $partner->getNaam()->setGebuikInLopendeTekst($row[93].' '.$voorletters.' '.$row[94].' '.$row[95]);
                    } else {
                        $partner->getNaam()->setAanhef('');
                        $partner->getNaam()->setAanschrijfwijze($voorletters.' '.$row[94].' '.$row[95]);
                        $partner->getNaam()->setGebuikInLopendeTekst($voorletters.' '.$row[94].' '.$row[95]);
                    }
                    if ($row[99]) {
                        $partner->setGeslachtsaanduiding($row[99]);
                    } else {
                        $partner->setGeslachtsaanduiding('X');
                    }

                    $partner->setGeboorte(new Geboorte());
                    $partner->getGeboorte()->setLand($nationaliteit);
                    $partner->getGeboorte()->setPlaats($geboorteplaats);

                    try {
                        $geboortedatum = $row[96];
                        $partner->getGeboorte()->setDatum(new IncompleteDate((int) substr($geboortedatum, 0, 4), (int) substr($geboortedatum, 4, 2), (int) substr($geboortedatum, 6, 2)));
                    } catch (\Exception $e) {
                    }
                    $partner->setAangaanHuwelijkPartnerschap(new AangaanHuwelijkPartnerschap());
                    $huwelijksdatum = $row[100];
                    $partner->getAangaanHuwelijkPartnerschap()->setDatum(new IncompleteDate((int) substr($huwelijksdatum, 0, 4), (int) substr($huwelijksdatum, 4, 2), (int) substr($huwelijksdatum, 6, 2)));
                    $partner->getAangaanHuwelijkPartnerschap()->setPlaats($geboorteplaats);
                    $partner->getAangaanHuwelijkPartnerschap()->setLand($nationaliteit);
                    $partner->getAangaanHuwelijkPartnerschap()->setPartner($partner);

                    if ($row[113]) {
                        $partner->getAangaanHuwelijkPartnerschap()->setInOnderzoek(new UnderInvestigation(['aanduiding' => $row[113]], $row[114]));
                    }

                    $manager->persist($partner);
                    $ingeschrevenpersoon->addPartner($partner);
                }
                if ($row[186]) {
                    $kind = new Kind();

                    $firstnamessplit = explode(' ', $row[187]);
                    $voorletters = '';

                    foreach ($firstnamessplit as $firstname) {
                        $voorletters .= mb_substr($firstname, 0, 1).'.';
                    }
                    $voorvoegsel = ''.$row[189];

                    $kind->setBurgerservicenummer($row[186]);

                    $kind->setNaam(new NaamPersoon());
                    $kind->getNaam()->setGeslachtsnaam($row[189].' '.$row[190]);
                    $kind->getNaam()->setVoorvoegsel($voorvoegsel);
                    $kind->getNaam()->setVoornamen($row[187]);
                    $kind->getNaam()->setVoorletters($voorletters);
                    if ($row[188]) {
                        $kind->getNaam()->setAanhef($row[188]);
                        $kind->getNaam()->setAanschrijfwijze($row[188].' '.$voorletters.' '.$row[189].' '.$row[190]);
                        $kind->getNaam()->setGebuikInLopendeTekst($row[188].' '.$voorletters.' '.$row[189].' '.$row[190]);
                    } else {
                        $kind->getNaam()->setAanhef('');
                        $kind->getNaam()->setAanschrijfwijze($voorletters.' '.$row[189].' '.$row[190]);
                        $kind->getNaam()->setGebuikInLopendeTekst($voorletters.' '.$row[189].' '.$row[190]);
                    }

                    $kind->setGeboorte(new Geboorte());
                    $kind->getGeboorte()->setLand($nationaliteit);
                    $kind->getGeboorte()->setPlaats($geboorteplaats);

                    try {
                        $geboortedatum = $row[191];
                        $partner->getGeboorte()->setDatum(new IncompleteDate((int) substr($geboortedatum, 0, 4), (int) substr($geboortedatum, 4, 2), (int) substr($geboortedatum, 6, 2)));
                    } catch (\Exception $e) {
                    }

                    if ($row[199]) {
                        $kind->setInOnderzoek(new UnderInvestigation(['aanduiding'=>$row[199]], $row[200]));
                    }

                    $manager->persist($kind);
                    $ingeschrevenpersoon->addKind($kind);
                }

                $manager->persist($ingeschrevenpersoon);
                $manager->flush();
                $i++;
            }
        }
    }

    public function iterateSpreadSheets(string $filename, ObjectManager $manager)
    {
        $spreadsheet = $this->loadXlsx($filename);
        $sheets = $spreadsheet->getAllSheets();

        $sheet = $sheets[0];
        $rows = $sheet->toArray();
        $highestRow = $sheet->getHighestRow();
        $this->iterateSpreadSheet($rows, $highestRow, $manager);
        $manager->flush();
    }

    public function loadFromExcel(ObjectManager $manager, string $filename)
    {
        $this->iterateSpreadSheets(dirname(__FILE__)."/resources/$filename.xlsx", $manager);
    }
}
