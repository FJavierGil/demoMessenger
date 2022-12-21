<?php

namespace App\MessageHandler;

use App\Message\NotificationMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NotificationHandler2
{
    public function __invoke(NotificationMessage $message): void
    {
        foreach ($message->getUsers() as $user) {
            sleep(1);
            echo 'Handler2: Notification send to ' .  $user . PHP_EOL;
        }
    }
}
