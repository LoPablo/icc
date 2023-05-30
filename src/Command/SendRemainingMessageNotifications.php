<?php

namespace App\Command;

use App\Event\MessageCreatedEvent;
use App\Notification\EventSubscriber\MessageCreatedEventSubscriber;
use App\Repository\MessageRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Shapecode\Bundle\CronBundle\Attribute\AsCronJob;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCronJob('*\/5 * * * *')]
#[AsCommand('app:notifications:send', 'Versendet E-Mail-Benachrichtigungen für Mitteilungen, für die noch keine Benachrichtigung versendet wurde (z.B. weil die Mitteilung für ein zukünftiges Datum erstellt wurde.')]
class SendRemainingMessageNotifications extends Command {

    public function __construct(private readonly DateHelper $dateHelper, private readonly MessageCreatedEventSubscriber $eventSubscriber,
                                private readonly MessageRepositoryInterface $messageRepository, string $name = null) {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

        $today = $this->dateHelper->getToday();
        $messages = $this->messageRepository->findAllNotificationNotSent($today);

        if(count($messages) > 0) {
            $message = $messages[0];
            $style->section(sprintf('Sende Benachrichtigungen für Mitteilung "%s"', $message->getTitle()));

            $this->eventSubscriber->onMessageCreated(new MessageCreatedEvent($message));
            $style->success(sprintf('Fertig (%d Mitteilungen mit offenen Benachrichtigungen stehen noch aus)', count($messages) - 1));
        } else {
            $style->success('Keine Mitteilungen mit offenen Benachrichtigungen gefunden');
        }

        return 0;
    }
}