<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class StatusController
{
    #[Route('/status', 'healthcheck')]
    public function heathcheck(): JsonResponse
    {
        return new JsonResponse([
            'app' => true
        ]);
    }
}
