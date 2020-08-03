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
use Conduction\CommonGroundBundle\ValueObject\UnderInvestigation;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class BZKFixtures extends Fixture
{
    private $params;

//    private $encoder;
//
    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
//        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        ini_set("memory_limit", "2G");
        /*
         *  Basis waarde tabel
         */

        if (
            $this->params->get('app_domain') == 'zuid-drecht.nl' ||
            strpos($this->params->get('app_domain'), 'zuid-drecht.nl') !== false
        ) {
            $this->loadFromExcel($manager, 'BZKgegevens');
        }

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
        echo $highestRow;
        foreach ($rows as $key=>$row) {
            echo "line: $i\n";
            if ($i == 0) {
                //skip the first line that contains the column title
                $i++;
                continue;
            } elseif ($i >= $highestRow) {
                echo 'The end';
                break;
            } elseif ($row[1] == null) {
                $i++;
                continue;
            } else {
                $firstnamessplit = explode(' ', $row[2]);
                $voorletters = '';

                foreach ($firstnamessplit as $firstname) {
                    $voorletters .= mb_substr($firstname, 0, 1) . '.';
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

                $voorvoegsel = '' . $row[4];
                $ingeschrevenpersoon->getNaam()->setGeslachtsnaam($row[4] . ' ' . $row[5]);
                $ingeschrevenpersoon->getNaam()->setVoorvoegsel($voorvoegsel);
                if ($row[2]) {
                    $ingeschrevenpersoon->getNaam()->setVoornamen($row[2]);
                } else {
                    $ingeschrevenpersoon->getNaam()->setVoornamen('');

                }
                $ingeschrevenpersoon->getNaam()->setVoorletters($voorletters);
                if ($row[3]) {
                    $ingeschrevenpersoon->getNaam()->setAanhef($row[3]);
                    $ingeschrevenpersoon->getNaam()->setAanschrijfwijze($row[3] . ' ' . $voorletters . ' ' . $row[4] . ' ' . $row[5]);
                    $ingeschrevenpersoon->getNaam()->setGebuikInLopendeTekst($row[3] . ' ' . $voorletters . ' ' . $row[4] . ' ' . $row[5]);
                } else {
                    $ingeschrevenpersoon->getNaam()->setAanhef('');
                    $ingeschrevenpersoon->getNaam()->setAanschrijfwijze($voorletters . ' ' . $row[4] . ' ' . $row[5]);
                    $ingeschrevenpersoon->getNaam()->setGebuikInLopendeTekst($voorletters . ' ' . $row[4] . ' ' . $row[5]);
                }

                $nederland = new Waardetabel();
                $nederland->setCode('NL');
                $nederland->setOmschrijving('Nederland');
                $utrecht = new Waardetabel();
                $utrecht->setCode('0344');
                $utrecht->setOmschrijving('Utrecht');

                $ingeschrevenpersoon->getGeboorte()->setLand($nederland);
                $ingeschrevenpersoon->getGeboorte()->setPlaats($utrecht);

                echo "setting geboortedatum\n";
                try {
                    $geboortedatum = $row[6];
                    var_dump($geboortedatum);
                    $ingeschrevenpersoon->getGeboorte()->setDatum(['year' => substr($geboortedatum, 0, 4), 'month' => substr($geboortedatum, 4, 2), 'day' => substr($geboortedatum, 6, 2)]);
                    $geboortedatum = new DateTime($row[6]);
                    $leeftijd = $geboortedatum->diff(new DateTime('now'), true)->format('%Y');
                    $ingeschrevenpersoon->setLeeftijd($leeftijd);
                } catch (\Exception $e) {

                }
                if ($row[18]) {

                    echo "setting onderzoek\n";
                    $inOnderzoek = new UnderInvestigation(['aanduiding' => $row[18]], $row[19]);
                    $ingeschrevenpersoon->setInOnderzoek($inOnderzoek);
                }

                if ($row[28]) {
                    echo "setting ouder1\n";
                    $ouder = new Ouder();

                    $firstnamessplit = explode(' ', $row[29]);
                    $voorletters = '';

                    foreach ($firstnamessplit as $firstname) {
                        $voorletters .= mb_substr($firstname, 0, 1) . '.';
                    }
                    $voorvoegsel = '' . $row[31];

                    $ouder->setBurgerservicenummer($row[28]);

                    $ouder->setNaam(new NaamPersoon());
                    $ouder->getNaam()->setGeslachtsnaam($row[31] . ' ' . $row[32]);
                    $ouder->getNaam()->setVoorvoegsel($voorvoegsel);
                    $ouder->getNaam()->setVoornamen($row[29]);
                    $ouder->getNaam()->setVoorletters($voorletters);
                    if ($row[30]) {
                        $ouder->getNaam()->setAanhef($row[30]);
                        $ouder->getNaam()->setAanschrijfwijze($row[30] . ' ' . $voorletters . ' ' . $row[31] . ' ' . $row[32]);
                        $ouder->getNaam()->setGebuikInLopendeTekst($row[30] . ' ' . $voorletters . ' ' . $row[31] . ' ' . $row[32]);
                    } else {
                        $ouder->getNaam()->setAanhef('');
                        $ouder->getNaam()->setAanschrijfwijze($voorletters . ' ' . $row[31] . ' ' . $row[32]);
                        $ouder->getNaam()->setGebuikInLopendeTekst($voorletters . ' ' . $row[31] . ' ' . $row[32]);
                    }
                    if ($row[36]) {
                        $ouder->setGeslachtsaanduiding($row[36]);
                    } else {
                        $ouder->setGeslachtsaanduiding('X');
                    }
                    $ouder->setOuderAanduiding('ouder1');

                    $ouder->setGeboorte(new Geboorte());
                    $ouder->getGeboorte()->setLand($nederland);
                    $ouder->getGeboorte()->setPlaats($utrecht);

                    try {
                        $geboortedatum = $row[33];
                        $ouder->getGeboorte()->setDatum(['year' => substr($geboortedatum, 0, 4), 'month' => substr($geboortedatum, 4, 2), 'day' => substr($geboortedatum, 6, 2)]);
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

                    echo "setting ouder2\n";
                    $ouder = new Ouder();

                    $firstnamessplit = explode(' ', $row[52]);
                    $voorletters = '';

                    foreach ($firstnamessplit as $firstname) {
                        $voorletters .= mb_substr($firstname, 0, 1) . '.';
                    }
                    $voorvoegsel = '' . $row[54];

                    $ouder->setBurgerservicenummer($row[51]);

                    $ouder->setNaam(new NaamPersoon());
                    $ouder->getNaam()->setGeslachtsnaam($row[54] . ' ' . $row[55]);


                    $ouder->getNaam()->setVoorvoegsel($voorvoegsel);
                    $ouder->getNaam()->setVoornamen($row[52]);
                    $ouder->getNaam()->setVoorletters($voorletters);
                    
                    if ($row[53]) {
                        $ouder->getNaam()->setAanhef($row[53]);
                        $ouder->getNaam()->setAanschrijfwijze($row[53] . ' ' . $voorletters . ' ' . $ouder->getNaam()->getGeslachtsnaam());
                        $ouder->getNaam()->setGebuikInLopendeTekst($row[53] . ' ' . $voorletters . ' ' . $ouder->getNaam()->getGeslachtsnaam());
                    } else {
                        $ouder->getNaam()->setAanhef('');
                        $ouder->getNaam()->setAanschrijfwijze($voorletters . ' ' . $ouder->getNaam()->getGeslachtsnaam());
                        $ouder->getNaam()->setGebuikInLopendeTekst($voorletters . ' ' . $ouder->getNaam()->getGeslachtsnaam());
                    }
                    if ($row[59]) {
                        $ouder->setGeslachtsaanduiding($row[59]);
                    } else {
                        $ouder->setGeslachtsaanduiding('X');
                    }
                    $ouder->setOuderAanduiding('ouder2');

                    $ouder->setGeboorte(new Geboorte());
                    $ouder->getGeboorte()->setLand($nederland);
                    $ouder->getGeboorte()->setPlaats($utrecht);

                    try {
                        $geboortedatum = $row[57];
                        $ouder->getGeboorte()->setDatum(['year' => substr($geboortedatum, 0, 4), 'month' => substr($geboortedatum, 4, 2), 'day' => substr($geboortedatum, 6, 2)]);
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
                    echo "setting partner\n";
                    $partner = new Partner();

                    $firstnamessplit = explode(' ', $row[92]);
                    $voorletters = '';

                    foreach ($firstnamessplit as $firstname) {
                        $voorletters .= mb_substr($firstname, 0, 1) . '.';
                    }
                    $voorvoegsel = '' . $row[94];

                    $partner->setBurgerservicenummer($row[91]);

                    $partner->setNaam(new NaamPersoon());
                    $partner->getNaam()->setGeslachtsnaam($row[94] . ' ' . $row[95]);
                    $partner->getNaam()->setVoorvoegsel($voorvoegsel);
                    $partner->getNaam()->setVoornamen($row[92]);
                    $partner->getNaam()->setVoorletters($voorletters);
                    if ($row[93]) {
                        $partner->getNaam()->setAanhef($row[93]);
                        $partner->getNaam()->setAanschrijfwijze($row[93] . ' ' . $voorletters . ' ' . $row[94] . ' ' . $row[95]);
                        $partner->getNaam()->setGebuikInLopendeTekst($row[93] . ' ' . $voorletters . ' ' . $row[94] . ' ' . $row[95]);
                    } else {
                        $partner->getNaam()->setAanhef('');
                        $partner->getNaam()->setAanschrijfwijze($voorletters . ' ' . $row[94] . ' ' . $row[95]);
                        $partner->getNaam()->setGebuikInLopendeTekst($voorletters . ' ' . $row[94] . ' ' . $row[95]);
                    }
                    if ($row[99]) {
                        $partner->setGeslachtsaanduiding($row[99]);
                    } else {
                        $partner->setGeslachtsaanduiding('X');
                    }


                    $partner->setGeboorte(new Geboorte());
                    $partner->getGeboorte()->setLand($nederland);
                    $partner->getGeboorte()->setPlaats($utrecht);

                    try {
                        $geboortedatum = $row[96];
                        $partner->getGeboorte()->setDatum(['year' => substr($geboortedatum, 0, 4), 'month' => substr($geboortedatum, 4, 2), 'day' => substr($geboortedatum, 6, 2)]);
                    } catch (\Exception $e) {
                    }
                    $partner->setAangaanHuwelijkPartnerschap(new AangaanHuwelijkPartnerschap());
                    $huwelijksdatum = $row[100];
                    $partner->getAangaanHuwelijkPartnerschap()->setDatum(['year' => substr($huwelijksdatum, 0, 4), 'month' => substr($huwelijksdatum, 4, 2), 'day' => substr($huwelijksdatum, 6, 8)]);
                    $partner->getAangaanHuwelijkPartnerschap()->setPlaats($utrecht);
                    $partner->getAangaanHuwelijkPartnerschap()->setLand($nederland);
                    $partner->getAangaanHuwelijkPartnerschap()->setPartner($partner);

                    if ($row[113]) {
                        $partner->getAangaanHuwelijkPartnerschap()->setInOnderzoek(new UnderInvestigation(['aanduiding' => $row[113]], $row[114]));
                    }

                    $manager->persist($partner);
                    $ingeschrevenpersoon->addPartner($partner);
                }
                if ($row[186]) {

                    echo "setting kind\n";
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
                    if($row[188]) {
                        $kind->getNaam()->setAanhef($row[188]);
                        $kind->getNaam()->setAanschrijfwijze($row[188].' '.$voorletters.' '.$row[189].' '.$row[190]);
                        $kind->getNaam()->setGebuikInLopendeTekst($row[188].' '.$voorletters.' '.$row[189].' '.$row[190]);
                    }else {
                        $kind->getNaam()->setAanhef('');
                        $kind->getNaam()->setAanschrijfwijze($voorletters.' '.$row[189].' '.$row[190]);
                        $kind->getNaam()->setGebuikInLopendeTekst($voorletters.' '.$row[189].' '.$row[190]);
                    }


                    $kind->setGeboorte(new Geboorte());
                    $kind->getGeboorte()->setLand($nederland);
                    $kind->getGeboorte()->setPlaats($utrecht);

                    try {
                        $geboortedatum = $row[191];
                        $partner->getGeboorte()->setDatum(['year'=>substr($geboortedatum,0,4), 'month'=>substr($geboortedatum,4,2), 'day'=>substr($geboortedatum,6,2)]);
                    } catch (\Exception $e) {
                    }


                    if($row[199]){
                        $kind->setInOnderzoek(new UnderInvestigation(['aanduiding'=>$row[199]], $row[200]));
                    }

                    $manager->persist($kind);
                    $ingeschrevenpersoon->addKind($kind);
                }


                if ($row[142] > 0) {
                    $ingeschrevenpersoon->setGeheimhoudingPersoonsgegevens(true);
                } else {
                    $ingeschrevenpersoon->setGeheimhoudingPersoonsgegevens(false);
                }

                $manager->persist($nederland);
                $manager->persist($utrecht);
                $manager->persist($ingeschrevenpersoon);
                $manager->flush();

                if ($row[120]) {
                    echo "setting overlijden\n";
                    $overlijden = new Overlijden();
                    $overlijdensdatum = $row[120];
                    $overlijden->setDatum(['year' => substr($overlijdensdatum, 0, 4), 'month' => substr($overlijdensdatum, 4, 2), 'day' => substr($overlijdensdatum, 6, 2)]);
                    $overlijden->setLand($nederland);
                    $overlijden->setPlaats($utrecht);
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

                    echo "setting opschorting\n";
                    $opschortingBijhouding = new OpschortingBijhouding();
                    $datumOpschorting = $row[138];
                    $opschortingBijhouding->setDatum(['year' => substr($datumOpschorting, 0, 4), 'month' => substr($datumOpschorting, 4, 2), 'day' => substr($datumOpschorting, 6, 2)]);
                    $opschortingBijhouding->setReden($row[139]);
                    $opschortingBijhouding->setIngeschrevenpersoon($ingeschrevenpersoon);
                    $ingeschrevenpersoon->setOpschortingBijhouding($opschortingBijhouding);
                    $manager->persist($opschortingBijhouding);
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
