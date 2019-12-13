<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Ingeschrevenpersoon;
use App\Entity\Verblijfplaats;
use App\Entity\NaamPersoon;
use App\Entity\Geboorte;
use App\Entity\Kind;
use App\Entity\Ouder;
use App\Entity\Partner;
use App\Entity\Waardetabel;
use DateTime;

class AppFixtures extends Fixture
{
    public function loadDenBosch(ObjectManager $manager)
    {
        /*
         *  Basis waarde tabel
         */

    	$nederland = New Waardetabel();
    	$nederland->setCode('NL');
    	$nederland->setOmschrijving('Nederland');
    	$amsterdam = New Waardetabel();
    	$amsterdam->setCode('301');
    	$amsterdam->setOmschrijving('Amsterdam');

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

    	// Adres gegevens
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
    	$persoon->getGeboorte()->setDatum(["year"=>"1985","month"=>"01","day"=>"01"]);

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
    	$persoon->getGeboorte()->setDatum(["year"=>"1985","month"=>"01","day"=>"01"]);

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
    	$persoon->getGeboorte()->setDatum(["year"=>"2000","month"=>"01","day"=>"01"]);

    	$BSN900003510 = $persoon;

    	// trouwen
    	$partner1 = New Partner();
    	$partner1->setBurgerservicenummer($BSN900003508->getBurgerservicenummer());
    	$partner1->setGeslachtsaanduiding($BSN900003508->getGeslachtsaanduiding());
    	$partner1->setNaam(clone $BSN900003508->getNaam());
    	$partner1->setGeboorte(clone $BSN900003508->getGeboorte());
    	$BSN900003509->addPartner($partner1);
    	$partner2 = New Partner();
    	$partner2->setBurgerservicenummer($BSN900003509->getBurgerservicenummer());
    	$partner2->setGeslachtsaanduiding($BSN900003509->getGeslachtsaanduiding());
    	$partner2->setNaam(clone $BSN900003509->getNaam());
    	$partner2->setGeboorte(clone $BSN900003509->getGeboorte());
    	$BSN900003508->addPartner($partner2);

    	// Ouders
    	$ouder1 = New Ouder();
    	$ouder1->setBurgerservicenummer($BSN900003508->getBurgerservicenummer());
    	$ouder1->setGeslachtsaanduiding($BSN900003508->getGeslachtsaanduiding());
    	$ouder1->setOuderAanduiding('wut');
    	$ouder1->setNaam(clone $BSN900003508->getNaam());
    	$ouder1->setGeboorte(clone $BSN900003508->getGeboorte());
    	$ouder2 = New Ouder();
    	$ouder2->setBurgerservicenummer($BSN900003509->getBurgerservicenummer());
    	$ouder2->setGeslachtsaanduiding($BSN900003509->getGeslachtsaanduiding());
    	$ouder2->setOuderAanduiding('wut');
    	$ouder2->setNaam(clone $BSN900003509->getNaam());
    	$ouder2->setGeboorte(clone $BSN900003509->getGeboorte());

    	$BSN900003510->addOuder($ouder1);
    	$BSN900003510->addOuder($ouder2);

    	$kind = New Kind();
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
    }
    public function load(ObjectManager $manager){
        $csv = fopen(dirname(__FILE__).'/resources/PersonaGegevens.csv', 'r');


        $i = 0;
        while(!feof($csv)){
            $line = fgetcsv($csv);

            if($i == 0)
            {
                //skip the first line that contains the column title
                $i++;
                continue;
            }

            $firstnamessplit = explode(" ", $line[2]);
            $voorletters = "";

            foreach($firstnamessplit as $firstname){
                $voorletters .= substr($firstname, 0, 1).'.';
            }

            $ingeschrevenpersoon = new Ingeschrevenpersoon();
            $ingeschrevenpersoon->setVerblijfplaats(new Verblijfplaats());
            $ingeschrevenpersoon->setNaam(new NaamPersoon());
            $ingeschrevenpersoon->setGeboorte(new Geboorte());

            //var_dump($line[5]);
            $ingeschrevenpersoon->setBurgerservicenummer($line[5]);
            $ingeschrevenpersoon->setGeheimhoudingPersoonsgegevens(false);
            $ingeschrevenpersoon->setGeslachtsaanduiding('X');
            $ingeschrevenpersoon->setLeeftijd(null);


            $ingeschrevenpersoon->getVerblijfplaats()->setPostcode($line[13]);
            $ingeschrevenpersoon->getVerblijfplaats()->setWoonplaatsnaam($line[14]);
            $ingeschrevenpersoon->getVerblijfplaats()->setStraatnaam($line[11]);
            $ingeschrevenpersoon->getVerblijfplaats()->setHuisnummer($line[12]);
            $ingeschrevenpersoon->getVerblijfplaats()->setHuisnummertoevoeging('');
            $ingeschrevenpersoon->getVerblijfplaats()->setIngeschrevenpersoon($ingeschrevenpersoon);

            $ingeschrevenpersoon->getNaam()->setGeslachtsnaam($line[4].' '.$line[3]);
            $ingeschrevenpersoon->getNaam()->setVoorvoegsel($line[4]);
            $ingeschrevenpersoon->getNaam()->setVoornamen($line[2]);
            $ingeschrevenpersoon->getNaam()->setVoorletters($voorletters);
            $ingeschrevenpersoon->getNaam()->setAanhef('');
            $ingeschrevenpersoon->getNaam()->setAanschrijfwijze($voorletters.' '.$line[4].' '.$line[3]);
            $ingeschrevenpersoon->getNaam()->setGebuikInLopendeTekst($voorletters.' '.$line[4].' '.$line[3]);

            $nederland = New Waardetabel();
            $nederland->setCode('NL');
            $nederland->setOmschrijving('Nederland');
            $utrecht = New Waardetabel();
            $utrecht->setCode('0344');
            $utrecht->setOmschrijving('Utrecht');

            $ingeschrevenpersoon->getGeboorte()->setLand($nederland);
            $ingeschrevenpersoon->getGeboorte()->setPlaats($utrecht);
            try {
                $geboortedatum = new DateTime($line[7]);
                echo $geboortedatum->format('Y');
                echo $geboortedatum->format('m');
                echo $geboortedatum->format('d');
                $ingeschrevenpersoon->getGeboorte()->setDatum(["year"=>$geboortedatum->format('Y'), "month"=>$geboortedatum->format('m'), "day"=>$geboortedatum->format('d')]);
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

        }
        $manager->flush();
    }
}
