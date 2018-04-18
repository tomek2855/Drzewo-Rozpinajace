<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppAddUserCommand extends Command
{
    protected static $defaultName = 'app:add-user';

    public function __construct(?string $name = null, EntityManagerInterface $em) {
        parent::__construct($name);
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->addArgument('login', InputArgument::REQUIRED, 'Login uzytkownika')
            ->addArgument('password', InputArgument::REQUIRED, 'Haslo uzytkownika')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $output->writeln(['', '<info>Dodawanie uzytkownika</info>', '']);

        $login = $input->getArgument('login');

        $hashedPassword = $input->getArgument('password');
        $hashedPassword = password_hash($hashedPassword, PASSWORD_BCRYPT);

        $user = new User();
        $user->setName($login);
        $user->setPassword($hashedPassword);

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('<info>Utworzono uzytkownika pomyslnie</info>');
    }
}
