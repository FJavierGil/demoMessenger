<?php

namespace App\MessageHandler;

use App\Message\NotificationMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class NotificationHandler implements MessageHandlerInterface
{

    public function __invoke(NotificationMessage $message)
    {
        foreach ($message->getUsers() as $user) {
            sleep(10);
            echo 'Notification send to ' .  $user . PHP_EOL;
        }
    }
}