<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'symf:add-admin',
    description: 'Generates and inserts an admin into a database',
)]
class SymfAddAdminCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::OPTIONAL, 'Admin username')
            ->addArgument('password', InputArgument::OPTIONAL, 'Admin password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getArgument('username') ?: 'admin';
        $password = $input->getArgument('password') ?: 'admin';
        $email = "{$username}@symf.com";

        $table = new Table($output);
        $table->setHeaderTitle('Your admin login credentials');
        $table->setHeaders(['email','username','password']);
        $table->addRow([$email,$username,$password]);
        $table->render();

        return Command::SUCCESS;
    }
}
