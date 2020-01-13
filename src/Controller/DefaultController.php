<?php


namespace App\Controller;


use App\Message\Notification;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

class DefaultController
{

    public function index(MessageBusInterface $bus, Request $request): Response
    {
        $users = ['foo@mail.com', 'bar@mail.com'];
        $txtMessage = $request->get('message') ?? 'default Message Subject';
        $bus->dispatch(new Notification($txtMessage, $users));
        return new Response('Notifications sent.');
    }
}