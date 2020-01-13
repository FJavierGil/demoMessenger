<?php

namespace App\MessageHandler;

use App\Message\Notification;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class NotificationHandler implements MessageHandlerInterface
{
    /** @var Swift_Mailer $mailer */
    private $mailer;

    public function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(Notification $message)
    {
        foreach ($message->getUsers() as $user) {
            // send e-mail
            $email = (new Swift_Message($message->getMessage()))
                ->setFrom('send@example.com')
                ->setTo($user)
                ->setBody($message->getMessage(), 'text/plain');
            $this->mailer->send($email);

            echo 'Notification send to ' .  $user . PHP_EOL;
        }
    }
}