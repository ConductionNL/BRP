<?php

namespace App\DataFixtures;

use App\Entity\Geboorte;
use App\Entity\Ingeschrevenpersoon;
use App\Entity\Kind;
use App\Entity\NaamPersoon;
use App\Entity\Ouder;
use App\Entity\Partner;
use App\Entity\Verblijfplaats;
use App\Entity\Waardetabel;
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
//        print_r($highestRow.'\n
//        ');
        $i = 0;
        echo $highestRow;
        foreach ($rows as $key=>$row) {
            echo "line: $i\n";
//            print_r($i . '
//            ');
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
//                var_dump($row);
                $firstnamessplit = explode(' ', $row[2]);
                $voorletters = '';

                foreach ($firstnamessplit as $firstname) {
                    $voorletters .= substr($firstname, 0, 1).'.';
                }

                $ingeschrevenpersoon = new Ingeschrevenpersoon();

                $ingeschrevenpersoon->setNaam(new NaamPersoon());
                $ingeschrevenpersoon->setGeboorte(new Geboorte());

                $ingeschrevenpersoon->setBurgerservicenummer($row[1]);
                $ingeschrevenpersoon->setGeheimhoudingPersoonsgegevens(false);
                if($row[9]){
                    $ingeschrevenpersoon->setGeslachtsaanduiding($row[9]);
                }else{
                    $ingeschrevenpersoon->setGeslachtsaanduiding('X');
                }

                if ($row[13] != '' || $row[14] != '' || $row[11] != '' || $row[12] != '') {
                    $ingeschrevenpersoon->setVerblijfplaats(new Verblijfplaats());

                    $ingeschrevenpersoon->getVerblijfplaats()->setPostcode($row[162]);
                    $ingeschrevenpersoon->getVerblijfplaats()->setWoonplaatsnaam($row[163]);
                    $ingeschrevenpersoon->getVerblijfplaats()->setStraatnaam($row[156]);
                    $ingeschrevenpersoon->getVerblijfplaats()->setHuisnummer($row[158]);
                    if ($row[159]){
                        $ingeschrevenpersoon->getVerblijfplaats()->setHuisnummertoevoeging($row[159]);
                    }else{
                        $ingeschrevenpersoon->getVerblijfplaats()->setHuisnummertoevoeging('');
                    }
                    if($row[160]){
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
                if($row[2]){
                    var_dump($row[2]);
                    $ingeschrevenpersoon->getNaam()->setVoornamen($row[2]);
                }else{
                    $ingeschrevenpersoon->getNaam()->setVoornamen('');

                }
                $ingeschrevenpersoon->getNaam()->setVoorletters($voorletters);
                if($row[3]){
                    $ingeschrevenpersoon->getNaam()->setAanhef($row[3]);
                    $ingeschrevenpersoon->getNaam()->setAanschrijfwijze($row[3].$voorletters.' '.$row[4].' '.$row[5]);
                    $ingeschrevenpersoon->getNaam()->setGebuikInLopendeTekst($row[3].$voorletters.' '.$row[4].' '.$row[5]);
                }
                else{
                    $ingeschrevenpersoon->getNaam()->setAanhef('');
                    $ingeschrevenpersoon->getNaam()->setAanschrijfwijze($voorletters.' '.$row[4].' '.$row[5]);
                    $ingeschrevenpersoon->getNaam()->setGebuikInLopendeTekst($voorletters.' '.$row[4].' '.$row[5]);
                }

                $nederland = new Waardetabel();
                $nederland->setCode('NL');
                $nederland->setOmschrijving('Nederland');
                $utrecht = new Waardetabel();
                $utrecht->setCode('0344');
                $utrecht->setOmschrijving('Utrecht');

                $ingeschrevenpersoon->getGeboorte()->setLand($nederland);
                $ingeschrevenpersoon->getGeboorte()->setPlaats($utrecht);

                try {
                    $geboortedatum = $row[6];
                    $ingeschrevenpersoon->getGeboorte()->setDatum(['year'=>substr($geboortedatum,0,4), 'month'=>substr($geboortedatum,4,2), 'day'=>substr($geboortedatum,6,8)]);
//                    var_dump($ingeschrevenpersoon->getGeboorte()->getDatum());

                    $geboortedatum = new DateTime($row[6]);
                    $leeftijd = $geboortedatum->diff(new DateTime('now'), true)->format('%Y');
                    $ingeschrevenpersoon->setLeeftijd($leeftijd);
                } catch (\Exception $e) {
                }

                //            if($line[18] == 'Ja'){
                //                $ingeschrevenpersoon->setInOnderzoek(true);
                //            }else{
                //                $ingeschrevenpersoon->setInOnderzoek(false);
                //            }

//                if (array_key_exists(21, $row) && $partnerRowNr = (int) $row[21]) {
//                    $partnerRow = $rows[$partnerRowNr];
//                    $partner = new Partner();
//
//                    $firstnamessplit = explode(' ', $partnerRow[2]);
//                    $voorletters = '';
//
//                    foreach ($firstnamessplit as $firstname) {
//                        $voorletters .= substr($firstname, 0, 1).'.';
//                    }
//                    $voorvoegsel = ''.$partnerRow[4];
//
//                    $partner->setBurgerservicenummer($partnerRow[5]);
//                    $partner->setGeslachtsaanduiding('X');
//
//                    $partner->setNaam(new NaamPersoon());
//                    $partner->getNaam()->setGeslachtsnaam($partnerRow[4].' '.$partnerRow[3]);
//                    $partner->getNaam()->setVoorvoegsel($voorvoegsel);
//                    $partner->getNaam()->setVoornamen($partnerRow[2]);
//                    $partner->getNaam()->setVoorletters($voorletters);
//                    $partner->getNaam()->setAanhef('');
//                    $partner->getNaam()->setAanschrijfwijze($voorletters.' '.$partnerRow[4].' '.$partnerRow[3]);
//                    $partner->getNaam()->setGebuikInLopendeTekst($voorletters.' '.$partnerRow[4].' '.$partnerRow[3]);
//
//                    $partner->setGeboorte(new Geboorte());
//                    $partner->getGeboorte()->setLand($nederland);
//                    $partner->getGeboorte()->setPlaats($utrecht);
//
//                    try {
//                        $geboortedatum = new DateTime($partnerRow[7]);
//                        echo $geboortedatum->format('Y');
//                        echo $geboortedatum->format('m');
//                        echo $geboortedatum->format('d');
//                        $partner->getGeboorte()->setDatum(['year'=>$geboortedatum->format('Y'), 'month'=>$geboortedatum->format('m'), 'day'=>$geboortedatum->format('d')]);
//                    } catch (\Exception $e) {
//                    }
//                    $manager->persist($partner);
//                    $ingeschrevenpersoon->addPartner($partner);
//                }
//                if (array_key_exists(23, $row) && $children = $row[23]) {
//                    $children = explode(';', $children);
//                    foreach ($children as $childRowNr) {
//                        $childRow = $rows[$childRowNr];
//                        $kind = new Kind();
//
//                        $firstnamessplit = explode(' ', $childRow[2]);
//                        $voorletters = '';
//
//                        foreach ($firstnamessplit as $firstname) {
//                            $voorletters .= substr($firstname, 0, 1).'.';
//                        }
//                        $voorvoegsel = ''.$childRow[4];
//
//                        $kind->setBurgerservicenummer($childRow[5]);
//
//                        $kind->setNaam(new NaamPersoon());
//                        $kind->getNaam()->setGeslachtsnaam($childRow[4].' '.$childRow[3]);
//                        $kind->getNaam()->setVoorvoegsel($voorvoegsel);
//                        $kind->getNaam()->setVoornamen($childRow[2]);
//                        $kind->getNaam()->setVoorletters($voorletters);
//                        $kind->getNaam()->setAanhef('');
//                        $kind->getNaam()->setAanschrijfwijze($voorletters.' '.$childRow[4].' '.$childRow[3]);
//                        $kind->getNaam()->setGebuikInLopendeTekst($voorletters.' '.$childRow[4].' '.$childRow[3]);
//
//                        $kind->setGeboorte(new Geboorte());
//                        $kind->getGeboorte()->setLand($nederland);
//                        $kind->getGeboorte()->setPlaats($utrecht);
//
//                        try {
//                            $geboortedatum = new DateTime($childRow[7]);
//                            echo $geboortedatum->format('Y');
//                            echo $geboortedatum->format('m');
//                            echo $geboortedatum->format('d');
//                            $kind->getGeboorte()->setDatum(['year'=>$geboortedatum->format('Y'), 'month'=>$geboortedatum->format('m'), 'day'=>$geboortedatum->format('d')]);
//                        } catch (\Exception $e) {
//                        }
//                        $manager->persist($kind);
//                        $ingeschrevenpersoon->addKind($kind);
//                    }
//                }
//                if (array_key_exists(22, $row) && $parents = $row[22]) {
//                    $parents = explode(';', $parents);
//                    foreach ($parents as $parentRowNr) {
//                        $parentRow = $rows[$parentRowNr];
//                        $ouder = new Ouder();
//
//                        $firstnamessplit = explode(' ', $parentRow[2]);
//                        $voorletters = '';
//
//                        foreach ($firstnamessplit as $firstname) {
//                            $voorletters .= substr($firstname, 0, 1).'.';
//                        }
//                        $voorvoegsel = ''.$parentRow[4];
//
//                        $ouder->setBurgerservicenummer($parentRow[5]);
//
//                        $ouder->setNaam(new NaamPersoon());
//                        $ouder->getNaam()->setGeslachtsnaam($parentRow[4].' '.$parentRow[3]);
//                        $ouder->getNaam()->setVoorvoegsel($voorvoegsel);
//                        $ouder->getNaam()->setVoornamen($parentRow[2]);
//                        $ouder->getNaam()->setVoorletters($voorletters);
//                        $ouder->getNaam()->setAanhef('');
//                        $ouder->getNaam()->setAanschrijfwijze($voorletters.' '.$parentRow[4].' '.$parentRow[3]);
//                        $ouder->getNaam()->setGebuikInLopendeTekst($voorletters.' '.$parentRow[4].' '.$parentRow[3]);
//
//                        $ouder->setGeslachtsaanduiding('X');
//                        $ouder->setOuderAanduiding('wut');
//
//                        $ouder->setGeboorte(new Geboorte());
//                        $ouder->getGeboorte()->setLand($nederland);
//                        $ouder->getGeboorte()->setPlaats($utrecht);
//
//                        try {
//                            $geboortedatum = new DateTime($parentRow[7]);
//                            echo $geboortedatum->format('Y');
//                            echo $geboortedatum->format('m');
//                            echo $geboortedatum->format('d');
//                            $ouder->getGeboorte()->setDatum(['year'=>$geboortedatum->format('Y'), 'month'=>$geboortedatum->format('m'), 'day'=>$geboortedatum->format('d')]);
//                        } catch (\Exception $e) {
//                        }
//                        $manager->persist($ouder);
//                        $ingeschrevenpersoon->addOuder($ouder);
//                    }
//                }

                $manager->persist($nederland);
                $manager->persist($utrecht);
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
