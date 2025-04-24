<?php
declare(strict_types=1);

namespace App\Controller;

use App\Services\workerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class WorkerController extends AbstractController
{
    #[Route('/worker', methods: ['POST'])]
    public function createWorker(Request $request,workerService $workerService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $name = $data['imię'] ?? '';
        $surname = $data['nazwisko'] ?? '';

        $worker = $workerService->createWorker($name, $surname);

        return new JsonResponse([
            'response' => [
                'id' => $worker->getId()
            ]
        ]);
    }
}
