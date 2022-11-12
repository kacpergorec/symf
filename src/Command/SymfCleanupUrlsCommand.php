<?php

namespace App\Command;

use App\Repository\UrlRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'symf:cleanup:urls',
    description: 'Removes the Expired URLS from the database.',
)]
class SymfCleanupUrlsCommand extends Command
{

    private UrlRepository $repository;

    public function __construct(UrlRepository $repository, string $name = null)
    {
        parent::__construct($name);
        $this->repository = $repository;
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('dry-run')) {
            $io->note('Dry mode enabled');

            $count = $this->repository->countExpired();
        } else {
            $count = $this->repository->deleteExpired();
        }

        $io->success(sprintf('Deleted "%d" expired URLS.', $count));

        return Command::SUCCESS;
    }
}
