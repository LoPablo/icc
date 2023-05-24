<?php

namespace App\Notification\Email;

use App\Notification\MessageNotification;
use App\Notification\Notification;

class MessageStrategy implements EmailStrategyInterface {

    public function supports(Notification $notification): bool {
        return $notification instanceof MessageNotification;
    }

    /**
     * @param MessageNotification $notification
     * @return string|null
     */
    public function getReplyTo(Notification $notification): ?string {
        return $notification->getMessage()->getCreatedBy()?->getEmail();
    }

    /**
     * @param MessageNotification $notification
     * @return string
     */
    public function getSender(Notification $notification): string {
        $user = $notification->getMessage()->getCreatedBy();

        if($user === null) {
            return 'N/A';
        }

        return sprintf('%s %s', $user->getFirstname(), $user->getLastname());
    }

    public function getTemplate(): string {
        return 'email/message.txt.twig';
    }

    public function getHtmlTemplate(): ?string {
        return 'email/message.html.twig';
    }
}