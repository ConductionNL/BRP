<?php

// src/Command/CreateUserCommand.php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Ifsnop\Mysqldump as IMysqldump;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class DatabaseDumpCommand extends Command
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
        $this->setName('app:database:export')
            ->setDescription('Dump database.');
        //->addArgument('file', InputArgument::REQUIRED, 'Absolute path for the file you need to dump database to.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title(sprintf('<comment>Dumping <fg=green>%s</fg=green> to <fg=green>%s</fg=green> </comment>', $this->database, $this->path));

        try {
            $dump = new IMysqldump\Mysqldump('mysql:host=db;dbname=api', 'api-platform', '!ChangeMe!');
            $dump->start(dirname(__FILE__).'/../DataFixtures/resources/api_export.sql');
        } catch (\Exception $e) {
            $io->error('mysqldump-php error: '.$e->getMessage());

            return Command::FAILURE;
        }
        $io->title('All done.');

        return Command::SUCCESS;
    }
}
