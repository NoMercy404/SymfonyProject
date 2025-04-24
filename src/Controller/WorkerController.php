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

        if (!isset($data['imię'], $data['nazwisko'])) {
            return $this->json(['error' => 'Brakuje danych'], 400);
        }

        $name = $data['imię'] ?? '';
        $surname = $data['nazwisko'] ?? '';
        try {
            $worker = $workerService->createWorker($name, $surname);

            return new JsonResponse([
                'response' => [
                    'id' => $worker->getId()
                ]
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);

        } catch (\RuntimeException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
