<?php

namespace App\Command;

use App\Repository\UserRepositoryInterface;
use Shapecode\Bundle\CronBundle\Attribute\AsCronJob;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCronJob('@daily')]
#[AsCommand('app:user:remove_orphaned', 'Löscht Lernende, Eltern und Lehrkräfte ohne verknüpfte Entität.')]
class RemoveOrphanedUsersCommand extends Command {

    public function __construct(private UserRepositoryInterface $userRepository, string $name = null) {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

        $count = $this->userRepository->removeOrphaned();

        $style->success(sprintf('%d verwaiste Benutzer gelöscht', $count));

        return 0;
    }
}