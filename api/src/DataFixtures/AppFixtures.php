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

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
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
}
