<?php

namespace App\Controller;

use Swop\GitHubWebHook\Security\SignatureValidator;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WebhookController
{
    public function handle(Request $request)
    {

        $secret = $_ENV['APP_SECRET'];
        $validator = new SignatureValidator();
        try {
            $psrFactory = new DiactorosFactory();
            $psrRequest = $psrFactory->createRequest($request);
            $validator->validate($psrRequest, $secret);
            $load = @json_decode($request->getContent());
            $client = new \Redis();
            $client->connect('127.0.0.1', 6379);
            $client->set('received-' . $load->repository->name, time());
        }
        catch (\Exception $e) {
            return new Response($e->getMessage());
        }
        return new Response('OK');
    }
}
