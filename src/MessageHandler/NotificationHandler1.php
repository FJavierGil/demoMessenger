<?php

namespace App\MessageHandler;

use App\Message\NotificationMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NotificationHandler1
{
    public function __invoke(NotificationMessage $message): void
    {
        foreach ($message->getUsers() as $user) {
            sleep(1);
            echo "Handler1: Notification send to " .  $user . PHP_EOL;
        }
    }
}
