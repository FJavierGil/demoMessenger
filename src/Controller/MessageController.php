<?php

namespace App\Controller;

use App\Message\NotificationMessage;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
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
 *
 * @Route(
 *     path=MessageController::RUTA_API,
 *     name="api_messenger_"
 * )
 */
class MessageController extends AbstractController
{

    public const RUTA_API = '/api/v1/NotificationMessages';

    /**
     * @Route(
     *     path="",
     *     name="producer",
     *     methods={ Request::METHOD_POST }
     * )
     *
     * @param MessageBusInterface $bus
     * @param Request $request
     *
     * @return Response
     */
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
            Response::HTTP_CREATED
        );
    }

    /**
     * @Route(
     *     path="",
     *     name="consumer",
     *     methods={ Request::METHOD_GET }
     * )
     *
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
            '-vvv' => null
        ]);

        $output = new StreamOutput(fopen('php://stdout', 'wb'));
        $application->run($input, $output);
        $content = stream_get_contents($output->getStream());

        return new Response(
            $content,
            Response::HTTP_OK
        );
    }

    /**
     * @Route(
     *     path="",
     *     methods={ Request::METHOD_OPTIONS },
     *     name="options"
     * )
     *
     * @return Response
     */
    public function optionsAction(): Response
    {

        return new JsonResponse(
            null,
            Response::HTTP_NO_CONTENT,
            [
                'Access-Control-Allow-Methods' => 'OPTIONS',
                'Allow' => 'OPTIONS, GET, POST',
            ]
        );
    }
}
