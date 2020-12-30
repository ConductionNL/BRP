Additional Information
----

For deployment to kubernetes clusters we use Helm 3.

For an in depth installation guide you can refer to the [installation guide](INSTALLATION.md).

- [Contributing](CONTRIBUTING.md)

- [ChangeLogs](CHANGELOG.md)

- [RoadMap](ROADMAP.md)

- [Security](SECURITY.md)

- [Licence](LICENSE.md)

Description
----

Het Basis Registratie Personen component is een implementatie van de door Haal Centraal ontwikkelde API-specificatie in de vorm van een Common Ground container. Dat betekent dat het ten opzichte van die referentie een aantal extra opties kent (zoals extended en filteren van data). Dit Common Ground component kan op twee manieren worden ingezet. Om te beginnen kan het aan de achterkant worden voorzien van datafixtures in de vorm van een excel sheet. Op deze manier kan er een “specifieke” mock worden gecreëerd voor het testen van applicaties (al dan niet in combinatie met Digispoof). Het is echter ook mogelijke om het BRP Component in te zetten in samenhang met het StuF component, in dat geval worden API-verzoeken op het BRP vertaald naar StuF berichten op GBA of GBAV aansluiting en levert het component daadwerkelijk een BRP koppeling.

Tutorial
----

For information on how to work with the component you can refer to the tutorial [here](TUTORIAL.md).

#### Setup your local environment
Before we can spin up our component we must first get a local copy from our repository, we can either do this through the command line or use a Git client. 

For this example we're going to use [GitKraken](https://www.gitkraken.com/) but you can use any tool you like, feel free to skip this part if you are already familiar with setting up a local clone of your repository.

Open gitkraken press "clone a repo" and fill in the form (select where on your local machine you want the repository to be stored, and fill in the link of your repository on github), press "clone a repo" and you should then see GitKraken downloading your code. After it's done press "open now" (in the box on top) and voilá your codebase (you should see an initial commit on a master branch).

You can now navigate to the folder where you just installed your code, it should contain some folders and files and generally look like this. We will get into the files later, lets first spin up our component!

Next make sure you have [docker desktop](https://www.docker.com/products/docker-desktop) running on your computer.

Open a command window (example) and browse to the folder where you just stuffed your code, navigating in a command window is done by cd, so for our example we could type 
cd c:\repos\common-ground\my-component (if you installed your code on a different disk then where the cmd window opens first type <diskname>: for example D: and hit enter to go to that disk, D in this case). We are now in our folder, so let's go! Type docker-compose up and hit enter. From now on whenever we describe a command line command we will document it as follows (the $ isn't actually typed but represents your folder structure):

```CLI
$ docker-compose up
```

Your computer should now start up your local development environment. Don't worry about al the code coming by, let's just wait until it finishes. You're free to watch along and see what exactly docker is doing, you will know when it's finished when it tells you that it is ready to handle connections. 

Open your browser type [<http://localhost/>](https://localhost) as address and hit enter, you should now see your common ground component up and running.

#### Additional Calls
The BRP component is a bit different to most components regarding calls. First and formost, the BRP component only supports GET calls to its entities.
On top of that, the IngeschrevenPersoon entity contains a number of extra supported calls:
- /ingeschrevenpersoon/uuid/{id}: This call identifies and returns the requested person based on their unique id in the database
- /ingeschrevenpersoon/{bsn}: This call identifies and returns the requested person based on their social security number (burgerservicenummer/bsn) as per specification by HaalCentraal.
- /ingeschrevenpersonen/{bsn}/ouders: This call returns the parent-resources linked to the person that is requested as per specification by Haal Centraal.
- /ingeschrevenpersonen/{bsn}/kinderen: This call returns the child-resources linked to the person that is requested as per specification by Haal Centraal.
- /ingeschrevenpersonen/{bsn}/partners: This call returns the partner-resources linked to the person that is requested as per specification by Haal Centraal.

Credits
----

Information about the authors of this component can be found [here](AUTHORS.md)

The source used for this component is [Haal Centraal](https://github.com/VNG-Realisatie/Haal-Centraal-BRP-bevragen)

Copyright © [Utrecht](https://www.utrecht.nl/) 2019
