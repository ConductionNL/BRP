<?php

// src/Command/CreateUserCommand.php
namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Ifsnop\Mysqldump as IMysqldump;


class DatabaseImportCommand extends Command
{

    /** @var OutputInterface */
    private $output;

    /** @var InputInterface */
    private $input;


    private $database;
    private $username;
    private $password;
    private $path;

    /** filesystem utility */
    private $fs;

    private ParameterBagInterface $params;

    private EntityManagerInterface $em;

    public function __construct(
        ParameterBagInterface $params,
        EntityManagerInterface $entityManagerInterface
    ) {
        $this->params = $params;
        $this->em = $entityManagerInterface;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('app:database:import')
            ->setDescription('Dump database.');
            //->addArgument('file', InputArgument::REQUIRED, 'Absolute path for the file you need to dump database to.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title(sprintf('<comment>Importing from <fg=green>%s</fg=green> to <fg=green>%s</fg=green> </comment>', $this->database, $this->path ));

        $filename = dirname(__FILE__)."/../DataFixtures/resources/api_export.sql";
        $maxRuntime = 8; // less then your max script execution limit


        $deadline = time()+$maxRuntime;
        $progressFilename = $filename.'_filepointer'; // tmp file for progress
        $errorFilename = $filename.'_error'; // tmp file for erro

        $sql = file_get_contents(dirname(__FILE__)."/../DataFixtures/resources/api_export.sql");

        $mysqli = new \mysqli("db", "api-platform", "!ChangeMe!", "api");


        ($fp = fopen($filename, 'r')) OR die('failed to open file:'.$filename);

// check for previous error
        if( file_exists($errorFilename) ){
            die('<pre> previous error: '.file_get_contents($errorFilename));
            return Command::FAILURE;
        }

// activate automatic reload in browser
        echo '<html><head> <meta http-equiv="refresh" content="'.($maxRuntime+2).'"><pre>';

// go to previous file position
        $filePosition = 0;
        if( file_exists($progressFilename) ){
            $filePosition = file_get_contents($progressFilename);
            fseek($fp, $filePosition);
        }

        $queryCount = 0;
        $query = '';
        while( $deadline>time() AND ($line=fgets($fp, 1024000)) ){
            if(substr($line,0,2)=='--' OR trim($line)=='' ){
                continue;
            }

            $query .= $line;
            if( substr(trim($query),-1)==';' ){
                if( !$mysqli->query($query) ){
                    $error = 'Error performing query \'<strong>' . $query . '\': ' . mysql_error();
                    file_put_contents($errorFilename, $error."\n");
                    return Command::FAILURE;
                }
                $query = '';
                file_put_contents($progressFilename, ftell($fp)); // save the current file position for
                $queryCount++;
            }
        }

        if( feof($fp) ){
            $io->title('dump successfully restored!');
        }else{
            echo ftell($fp).'/'.filesize($filename).' '.(round(ftell($fp)/filesize($filename), 2)*100).'%'."\n";
            echo $queryCount.' queries processed! please reload or wait for automatic browser refresh!';
        }


        $io->title('All done.');

        return Command::SUCCESS;

    }

}
