<?php

namespace App\Controller;

use App\Message\NotificationMessage;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MessageController
 *
 * @package App\Controller
 */
#[Route(path: MessageController::RUTA_API, name: "api_messenger_")]
class MessageController extends AbstractController
{

    public const RUTA_API = '/api/v1/NotificationMessages';

    /**
     * @param MessageBusInterface $bus
     * @param Request $request
     *
     * @return Response
     */
    #[Route(path: "", name: "producer", methods: [ Request::METHOD_POST ])]
    public function producer(MessageBusInterface $bus, Request $request): Response
    {
        $body = $request->getContent();
        $postData = json_decode($body, true);

        if (!isset($postData['users'], $postData['textMessage'])) { // 422
            // 422 - Unprocessable Entity - Faltan datos
            return new JsonResponse(
                [
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => Response::$statusTexts[422]
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $notificationMessage = new NotificationMessage(
            $postData['textMessage'],
            $postData['users']
        );
        $bus->dispatch($notificationMessage);

        return new JsonResponse(
            [
                'textMessage' => $postData['textMessage'],
                'users' => $postData['users'],
            ],
            Response::HTTP_CREATED,
            [
                'Location' => $request->getPathInfo(),
            ]
        );
    }

    /**
     * @param KernelInterface $kernel
     *
     * @return Response
     * @throws \Exception
     */
    #[Route(path: "", name: "consumer", methods: [ Request::METHOD_GET ])]
    public function consumer(KernelInterface $kernel): Response
    {
        $application = new Application($kernel);
        $application->setAutoExit(true);

        $input = new ArrayInput([
            'command' => 'messenger:consume',
            '--ansi' => true,
            '--limit' => 1,
            '-vvv' => true
        ]);

        $output = new BufferedOutput();
        $application->run($input, $output);
        $content = $output->fetch();

        return new Response(
            $content,
            Response::HTTP_OK
        );
    }

    /**
     * @return Response
     */
    #[Route(path: "", name: "options", methods: [ Request::METHOD_OPTIONS ])]
    public function optionsAction(): Response
    {

        return new JsonResponse(
            null,
            Response::HTTP_NO_CONTENT,
            [
                'Access-Control-Allow-Methods' => 'OPTIONS',
                'Allow' => 'OPTIONS,GET,POST',
            ]
        );
    }
}
