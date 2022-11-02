<?php

namespace App\Command;

use App\Factory\User\AdminUserFactory;
use App\Repository\UserRepository;
use InvalidArgumentException;
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

    private AdminUserFactory $adminUserFactory;
    private UserRepository $userRepository;

    public function __construct(AdminUserFactory $adminUserFactory, UserRepository $userRepository)
    {
        parent::__construct();
        $this->adminUserFactory = $adminUserFactory;
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::OPTIONAL, 'Admin username')
            ->addArgument('password', InputArgument::OPTIONAL, 'Admin password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $argsCount = count(array_filter($input->getArguments()));

        if ($argsCount !== 1 && $argsCount !== 3) {
            throw new InvalidArgumentException("This commands accepts only 0 or 2 arguments. [username, password] or [none]");
        }

        $io = new SymfonyStyle($input, $output);

        $username = (string)$input->getArgument('username');
        $password = (string)$input->getArgument('password');

        $user = $this->adminUserFactory->createNew($username, $username, $password);

        $this->userRepository->save($user, true);

        $table = new Table($output);
        $table->setHeaders(['username', 'password']);
        $table->addRow([$user->getUsername(), $password ?: '<comment>DEFAULT VALUE [.env]</comment>']);
        $table->render();

        return Command::SUCCESS;
    }
}
