<?php

namespace App\MessageHandler;

use App\Message\NotificationMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AnotherNotificationHandler implements MessageHandlerInterface
{

    public function __invoke(NotificationMessage $message)
    {
        foreach ($message->getUsers() as $user) {
            sleep(4);
            echo 'Another Notification send to ' .  $user . PHP_EOL;
        }
    }
}