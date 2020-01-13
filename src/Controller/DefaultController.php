<?php


namespace App\Controller;


use App\Message\Notification;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

class DefaultController
{

    public function index(MessageBusInterface $bus, Request $request)
    {
        $users = ['foo@mail.com', 'bar@mail.com'];
        $bus->dispatch(new Notification($request->get('message'), $users));
    }
}