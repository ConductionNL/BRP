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

class AppFixtures extends Fixture
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
        /*
         *  Basis waarde tabel
         */

        $nederland = new Waardetabel();
        $nederland->setCode('NL');
        $nederland->setOmschrijving('Nederland');
        $amsterdam = new Waardetabel();
        $amsterdam->setCode('301');
        $amsterdam->setOmschrijving('Amsterdam');

        if ($this->params->get('app_domain') == 'mijncluster.nl' || strpos($this->params->get('app_domain'), 'mijncluster.nl') !== false) {
            $this->loadFromExcel($manager, 'mijncluster.nl');

            return;
        }

        /*
    	 * Vader figuur
    	 */

        // Lets set up our person
        $persoon = new Ingeschrevenpersoon();
        $persoon->setVerblijfplaats(new Verblijfplaats());
        $persoon->setNaam(new NaamPersoon());
        $persoon->setGeboorte(new Geboorte());

        // Dan de persoons data
        $persoon->setBurgerservicenummer('900003509');
        $persoon->setGeheimhoudingPersoonsgegevens(false);
        $persoon->setGeslachtsaanduiding('Man');
        $persoon->setLeeftijd(22);

        // Adres gegevensv
        $persoon->getVerblijfplaats()->setBagId(123456789);
        $persoon->getVerblijfplaats()->setPostcode('1012RJ');
        $persoon->getVerblijfplaats()->setWoonplaatsnaam('Amsterdam');
        $persoon->getVerblijfplaats()->setStraatnaam('Nieuwezijds Voorburgwal ');
        $persoon->getVerblijfplaats()->setHuisnummer('147');
        $persoon->getVerblijfplaats()->setHuisnummertoevoeging('');
        $persoon->getVerblijfplaats()->setIngeschrevenpersoon($persoon);

        // Naamgeving
        $persoon->getNaam()->setGeslachtsnaam('de Kieft');
        $persoon->getNaam()->setVoorvoegsel('de');
        $persoon->getNaam()->setVoorletters('J.W.');
        $persoon->getNaam()->setVoornamen('John Willem');
        $persoon->getNaam()->setaanhef('Dhr.');
        $persoon->getNaam()->setAanschrijfwijze('Dhr. de Kieft, Jan Willem');
        $persoon->getNaam()->setGebuikInLopendeTekst('Dhr. de Kieft, Jan Willem');

        // Geboorte
        $persoon->getGeboorte()->setLand($nederland);
        $persoon->getGeboorte()->setPlaats($amsterdam);
        $persoon->getGeboorte()->setDatum(['year'=>'1985', 'month'=>'01', 'day'=>'01']);

        $BSN900003509 = $persoon;

        /*
    	 * Moeder figuur
    	 */

        // Lets set up our person
        $persoon = new Ingeschrevenpersoon();
        $persoon->setVerblijfplaats(new Verblijfplaats());
        $persoon->setNaam(new NaamPersoon());
        $persoon->setGeboorte(new Geboorte());

        // Dan de persoons data
        $persoon->setBurgerservicenummer('900003508');
        $persoon->setGeheimhoudingPersoonsgegevens(false);
        $persoon->setGeslachtsaanduiding('Vrouw');
        $persoon->setLeeftijd(23);

        // Adres gegevens
        $persoon->getVerblijfplaats()->setBagId(123456789);
        $persoon->getVerblijfplaats()->setPostcode('1012RJ');
        $persoon->getVerblijfplaats()->setWoonplaatsnaam('Amsterdam');
        $persoon->getVerblijfplaats()->setStraatnaam('Nieuwezijds Voorburgwal ');
        $persoon->getVerblijfplaats()->setHuisnummer('147');
        $persoon->getVerblijfplaats()->setHuisnummertoevoeging('');
        $persoon->getVerblijfplaats()->setIngeschrevenpersoon($persoon);

        // Naamgeving
        $persoon->getNaam()->setGeslachtsnaam('de Kieft');
        $persoon->getNaam()->setVoorvoegsel('de');
        $persoon->getNaam()->setVoorletters('A.H.');
        $persoon->getNaam()->setVoornamen('Anita Henrika');
        $persoon->getNaam()->setaanhef('Mvr.');
        $persoon->getNaam()->setAanschrijfwijze('Mvr. de Kieft, Anita Henrika');
        $persoon->getNaam()->setGebuikInLopendeTekst('Mvr. de Kieft, Anita Henrika');

        // Geboorte
        $persoon->getGeboorte()->setLand($nederland);
        $persoon->getGeboorte()->setPlaats($amsterdam);
        $persoon->getGeboorte()->setDatum(['year'=>'1985', 'month'=>'01', 'day'=>'01']);

        $BSN900003508 = $persoon;

        /*
    	 * kind
    	 */

        // Lets set up our person
        $persoon = new Ingeschrevenpersoon();
        $persoon->setVerblijfplaats(new Verblijfplaats());
        $persoon->setNaam(new NaamPersoon());
        $persoon->setGeboorte(new Geboorte());

        // Dan de persoons data
        $persoon->setBurgerservicenummer('900003510');
        $persoon->setGeheimhoudingPersoonsgegevens(false);
        $persoon->setGeslachtsaanduiding('Man');
        $persoon->setLeeftijd(5);

        // Adres gegevens
        $persoon->getVerblijfplaats()->setBagId(123456789);
        $persoon->getVerblijfplaats()->setPostcode('1012RJ');
        $persoon->getVerblijfplaats()->setWoonplaatsnaam('Amsterdam');
        $persoon->getVerblijfplaats()->setStraatnaam('Nieuwezijds Voorburgwal ');
        $persoon->getVerblijfplaats()->setHuisnummer('147');
        $persoon->getVerblijfplaats()->setHuisnummertoevoeging('');
        $persoon->getVerblijfplaats()->setIngeschrevenpersoon($persoon);

        // Naamgeving
        $persoon->getNaam()->setGeslachtsnaam('de Kieft');
        $persoon->getNaam()->setVoorvoegsel('de');
        $persoon->getNaam()->setVoorletters('J.H.');
        $persoon->getNaam()->setVoornamen('Jan Henrik');
        $persoon->getNaam()->setaanhef('Dhr.');
        $persoon->getNaam()->setAanschrijfwijze('Dhr. de Kieft, Jan Henrik');
        $persoon->getNaam()->setGebuikInLopendeTekst('Dhr. de Kieft, Jan Henrik');

        // Geboorte
        $persoon->getGeboorte()->setLand($nederland);
        $persoon->getGeboorte()->setPlaats($amsterdam);
        $persoon->getGeboorte()->setDatum(['year'=>'2000', 'month'=>'01', 'day'=>'01']);

        $BSN900003510 = $persoon;

        // trouwen
        $partner1 = new Partner();
        $partner1->setBurgerservicenummer($BSN900003508->getBurgerservicenummer());
        $partner1->setGeslachtsaanduiding($BSN900003508->getGeslachtsaanduiding());
        $partner1->setNaam(clone $BSN900003508->getNaam());
        $partner1->setGeboorte(clone $BSN900003508->getGeboorte());
        $BSN900003509->addPartner($partner1);
        $partner2 = new Partner();
        $partner2->setBurgerservicenummer($BSN900003509->getBurgerservicenummer());
        $partner2->setGeslachtsaanduiding($BSN900003509->getGeslachtsaanduiding());
        $partner2->setNaam(clone $BSN900003509->getNaam());
        $partner2->setGeboorte(clone $BSN900003509->getGeboorte());
        $BSN900003508->addPartner($partner2);

        // Ouders
        $ouder1 = new Ouder();
        $ouder1->setBurgerservicenummer($BSN900003508->getBurgerservicenummer());
        $ouder1->setGeslachtsaanduiding($BSN900003508->getGeslachtsaanduiding());
        $ouder1->setOuderAanduiding('wut');
        $ouder1->setNaam(clone $BSN900003508->getNaam());
        $ouder1->setGeboorte(clone $BSN900003508->getGeboorte());
        $ouder2 = new Ouder();
        $ouder2->setBurgerservicenummer($BSN900003509->getBurgerservicenummer());
        $ouder2->setGeslachtsaanduiding($BSN900003509->getGeslachtsaanduiding());
        $ouder2->setOuderAanduiding('wut');
        $ouder2->setNaam(clone $BSN900003509->getNaam());
        $ouder2->setGeboorte(clone $BSN900003509->getGeboorte());

        $BSN900003510->addOuder($ouder1);
        $BSN900003510->addOuder($ouder2);

        $kind = new Kind();
        $kind->setBurgerservicenummer($BSN900003510->getBurgerservicenummer());
        $kind->setLeeftijd($BSN900003510->getLeeftijd());
        $kind->setNaam(clone $BSN900003510->getNaam());
        $kind->setGeboorte(clone $BSN900003510->getGeboorte());

        $BSN900003508->addKind(clone $kind);
        $BSN900003509->addKind(clone $kind);
        /*
    	 * opslaan gegevens
    	 */

        $manager->persist($nederland);
        $manager->persist($amsterdam);
        $manager->persist($BSN900003508); // moeder
        $manager->persist($BSN900003509); // vader
        $manager->persist($BSN900003510); // kind
        $manager->flush();

        $this->loadFromExcel($manager, 'PersonaGegevens');
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
        foreach ($rows as $key=>$row) {
//            print_r($i . '
//            ');
            if ($i == 0) {
                //skip the first line that contains the column title
                $i++;
                continue;
            } elseif ($i >= $highestRow) {
                break;
            } else {
//                var_dump($row);
                $firstnamessplit = explode(' ', $row[2]);
                $voorletters = '';

                foreach ($firstnamessplit as $firstname) {
                    $voorletters .= substr($firstname, 0, 1).'.';
                }

                $ingeschrevenpersoon = new Ingeschrevenpersoon();
                $ingeschrevenpersoon->setVerblijfplaats(new Verblijfplaats());
                $ingeschrevenpersoon->setNaam(new NaamPersoon());
                $ingeschrevenpersoon->setGeboorte(new Geboorte());

                //var_dump($line[5]);
                $ingeschrevenpersoon->setBurgerservicenummer($row[5]);
                $ingeschrevenpersoon->setGeheimhoudingPersoonsgegevens(false);
                $ingeschrevenpersoon->setGeslachtsaanduiding('X');
                $ingeschrevenpersoon->setLeeftijd(null);

                $ingeschrevenpersoon->getVerblijfplaats()->setPostcode($row[13]);
                $ingeschrevenpersoon->getVerblijfplaats()->setWoonplaatsnaam($row[14]);
                $ingeschrevenpersoon->getVerblijfplaats()->setStraatnaam($row[11]);
                $ingeschrevenpersoon->getVerblijfplaats()->setHuisnummer($row[12]);
                $ingeschrevenpersoon->getVerblijfplaats()->setHuisnummertoevoeging('');
                $ingeschrevenpersoon->getVerblijfplaats()->setIngeschrevenpersoon($ingeschrevenpersoon);

                $voorvoegsel = ''.$row[4];
                $ingeschrevenpersoon->getNaam()->setGeslachtsnaam($row[4].' '.$row[3]);
                $ingeschrevenpersoon->getNaam()->setVoorvoegsel($voorvoegsel);
                $ingeschrevenpersoon->getNaam()->setVoornamen($row[2]);
                $ingeschrevenpersoon->getNaam()->setVoorletters($voorletters);
                $ingeschrevenpersoon->getNaam()->setAanhef('');
                $ingeschrevenpersoon->getNaam()->setAanschrijfwijze($voorletters.' '.$row[4].' '.$row[3]);
                $ingeschrevenpersoon->getNaam()->setGebuikInLopendeTekst($voorletters.' '.$row[4].' '.$row[3]);

                $nederland = new Waardetabel();
                $nederland->setCode('NL');
                $nederland->setOmschrijving('Nederland');
                $utrecht = new Waardetabel();
                $utrecht->setCode('0344');
                $utrecht->setOmschrijving('Utrecht');

                $ingeschrevenpersoon->getGeboorte()->setLand($nederland);
                $ingeschrevenpersoon->getGeboorte()->setPlaats($utrecht);

                try {
                    $geboortedatum = new DateTime($row[7]);
                    echo $geboortedatum->format('Y');
                    echo $geboortedatum->format('m');
                    echo $geboortedatum->format('d');
                    $ingeschrevenpersoon->getGeboorte()->setDatum(['year'=>$geboortedatum->format('Y'), 'month'=>$geboortedatum->format('m'), 'day'=>$geboortedatum->format('d')]);
                } catch (\Exception $e) {
                }

                //            if($line[18] == 'Ja'){
                //                $ingeschrevenpersoon->setInOnderzoek(true);
                //            }else{
                //                $ingeschrevenpersoon->setInOnderzoek(false);
                //            }

                $manager->persist($nederland);
                $manager->persist($utrecht);
                $manager->persist($ingeschrevenpersoon);
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
