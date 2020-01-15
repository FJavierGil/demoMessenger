<?php

namespace App\Controller;

use App\Message\NotificationMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

class DefaultController extends AbstractController
{

    public function index(MessageBusInterface $bus, Request $request): Response
    {
        $users = ['foo@mail.com', 'bar@mail.com'];
        $txtMessage = $request->get('message') ?? 'default Message Subject';
        $bus->dispatch(new NotificationMessage($txtMessage, $users));
        return $this->render(
            'default/index.html.twig',
            [
                'msg' => 'Notifications sent.',
            ]
        );
    }
}