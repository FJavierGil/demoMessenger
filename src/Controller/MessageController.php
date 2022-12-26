<?php

namespace App\Controller;

use App\Message\NotificationMessage;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MessageController
 *
 * @package App\Controller
 */
#[Route(
    path: MessageController::RUTA_API,
    name: "api_messenger_"
)]
class MessageController extends AbstractController
{
    public const RUTA_API = '/api/v1/NotificationMessages';

    /**
     * @param MessageBusInterface $bus
     * @param HttpFoundation\Request $request
     *
     * @return HttpFoundation\Response
     */
    #[Route(
        path: "",
        name: "producer",
        methods: [ HttpFoundation\Request::METHOD_POST ]
    )]
    public function producer(MessageBusInterface $bus, HttpFoundation\Request $request): HttpFoundation\Response
    {
        $body = $request->getContent();
        $postData = json_decode($body, true);

        if (!isset($postData['users'], $postData['textMessage'])) { // 422
            // 422 - Unprocessable Entity - Faltan datos
            return new HttpFoundation\JsonResponse(
                data: [
                    'code' => HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => HttpFoundation\Response::$statusTexts[422]
                ],
                status: HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $notificationMessage = new NotificationMessage(
            message: $postData['textMessage'],
            users: $postData['users']
        );
        $bus->dispatch($notificationMessage);

        return new HttpFoundation\JsonResponse(
            data: [
                'textMessage' => $postData['textMessage'],
                'users' => $postData['users'],
            ],
            status: HttpFoundation\Response::HTTP_CREATED,
            headers: [
                'Location' => $request->getPathInfo(),
            ]
        );
    }

    /**
     * @param KernelInterface $kernel
     *
     * @return HttpFoundation\Response
     * @throws Exception
     */
    #[Route(
        path: "",
        name: "consumer",
        methods: [ HttpFoundation\Request::METHOD_GET ]
    )]
    public function consumer(KernelInterface $kernel): HttpFoundation\Response
    {
        $application = new Application($kernel);
        $application->setAutoExit(true);

        $input = new Console\Input\ArrayInput([
            'command' => 'messenger:consume',
            '--ansi' => true,
            '--limit' => 1,
            '-vvv' => true
        ]);

        $output = new Console\Output\BufferedOutput();
        $application->run($input, $output);
        $content = $output->fetch();

        return new HttpFoundation\Response(
            content: $content,
            status: HttpFoundation\Response::HTTP_OK
        );
    }

    /**
     * @return HttpFoundation\Response
     */
    #[Route(
        path: "",
        name: "options",
        methods: [ HttpFoundation\Request::METHOD_OPTIONS ]
    )]
    public function optionsAction(): HttpFoundation\Response
    {

        return new HttpFoundation\Response(
            status: HttpFoundation\Response::HTTP_NO_CONTENT,
            headers: [
                'Access-Control-Allow-Methods' => 'OPTIONS',
                'Allow' => 'OPTIONS,GET,POST',
            ]
        );
    }
}
