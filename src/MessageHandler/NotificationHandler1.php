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
            echo sprintf("Handler1: Notification send to %s\n", $user);
        }
    }
}
