<?php


namespace App\MessageHandler;

use App\Message\Notification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class NotificationHandler implements MessageHandlerInterface
{

    public function __invoke(Notification $message)
    {
        foreach ($message->getUsers() as $user) {
            echo 'Notification send to ' .  $user . PHP_EOL;
        }
    }
}