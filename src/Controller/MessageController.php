<?php

namespace App\Controller;

use App\Message\NotificationMessage;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class MessageController
 *
 * @package App\Controller
 */
#[Route(
    path: MessageController::RUTA_API,
    name: 'api_messenger_'
)]
class MessageController extends AbstractController
{
    public const string RUTA_API = '/api/v1/NotificationMessages';

    /**
     * @param MessageBusInterface $bus
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    #[Route(
        path: '',
        name: 'producer',
        methods: [ Request::METHOD_POST ]
    )]
    public function producer(MessageBusInterface $bus, Request $request): JsonResponse
    {
        $body = $request->getContent();
        $postData = json_decode($body, true);

        if (!isset($postData['users'], $postData['textMessage'])) { // 422
            // 422 - Unprocessable Entity - Faltan datos
            return new JsonResponse(
                data: [
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => Response::$statusTexts[422]
                ],
                status: Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $notificationMessage = new NotificationMessage(
            message: $postData['textMessage'],
            users: $postData['users']
        );
        $bus->dispatch($notificationMessage);

        return new JsonResponse(
            data: [
                'textMessage' => $postData['textMessage'],
                'users' => $postData['users'],
            ],
            status: Response::HTTP_CREATED,
            headers: [
                'Location' => $request->getPathInfo(),
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'POST, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, api_key, Authorization',
            ]
        );
    }

    /**
     * @param KernelInterface $kernel
     *
     * @return Response
     * @throws Exception
     */
    #[Route(
        path: '',
        name: 'consumer',
        methods: [ Request::METHOD_GET ]
    )]
    public function consumer(KernelInterface $kernel): Response
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

        return new Response(
            content: $content,
            status: Response::HTTP_OK,
            headers: [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, api_key, Authorization',
            ]
        );
    }

    /**
     * @return Response
     */
    #[Route(
        path: '',
        name: 'options',
        methods: [ Request::METHOD_OPTIONS ]
    )]
    public function optionsAction(): Response
    {

        return new Response(
            status: Response::HTTP_NO_CONTENT,
            headers: [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, api_key, Authorization',
                'Vary' => 'Origin',
                'Allow' => 'OPTIONS,GET,POST',
            ]
        );
    }
}
