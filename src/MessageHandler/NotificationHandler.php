<?php

namespace App\MessageHandler;

use App\Message\NotificationMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class NotificationHandler implements MessageHandlerInterface
{

    public function __invoke(NotificationMessage $message)
    {
        foreach ($message->getUsers() as $user) {
            sleep(2);
            echo 'Handler1: Notification send to ' .  $user . PHP_EOL;
        }
    }
}
