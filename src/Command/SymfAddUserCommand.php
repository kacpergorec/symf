<?php

namespace App\Command;

use App\Factory\User\VerifiedUserFactory;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'symf:add:user',
    description: 'Generates and inserts an user into a database',
)]
class SymfAddUserCommand extends Command
{

    private VerifiedUserFactory $userFactory;
    private UserRepository $userRepository;

    public function __construct(VerifiedUserFactory $userFactory, UserRepository $userRepository)
    {
        parent::__construct();
        $this->userFactory = $userFactory;
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('username', InputArgument::REQUIRED, 'User username')
            ->addArgument('password', InputArgument::REQUIRED, 'User password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $user = $this->userFactory->createNew(
            $input->getArgument('username'),
            $input->getArgument('email'),
            $input->getArgument('password')
        );

        $this->userRepository->save($user, true);

        $table = new Table($output);
        $table->setHeaders(['username', 'password']);
        $table->addRow([$user->getUsername(),  $input->getArgument('password') ?: '<comment>DEFAULT VALUE [.env]</comment>']);
        $table->render();

        return Command::SUCCESS;
    }
}
