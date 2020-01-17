<?php

namespace App\Controller;

use App\Message\NotificationMessage;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    /**
     * @Route(
     *     path="/",
     *     name="sender"
     * )
     * @param MessageBusInterface $bus
     * @param Request $request
     *
     * @return Response
     */
    public function index(MessageBusInterface $bus, Request $request): Response
    {
        $users = ['foo@mail.com', 'bar@mail.com'];
        $txtMessage = $request->get('message') ?? 'default Message Subject';
        $bus->dispatch(new NotificationMessage($txtMessage, $users));
        return $this->render(
            'default/index.html.twig',
            [
                'msg' => 'Notificaciones enviadas.',
            ]
        );
    }

    /**
     * @Route(
     *     path="/workers",
     *     name="workers"
     * )
     * @param KernelInterface $kernel
     *
     * @return Response
     * @throws \Exception
     */
    public function consumer(KernelInterface $kernel): Response
    {
        $application = new Application($kernel);
        $application->setAutoExit(true);

        $input = new ArrayInput([
            'command' => 'messenger:consume',
            '--limit' => 1,
        ]);

        $output = new BufferedOutput();
        $application->run($input, $output);
        $content = $output->fetch();

        return new Response($content);
    }
}