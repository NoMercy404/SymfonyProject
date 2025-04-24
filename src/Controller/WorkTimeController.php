<?php
declare(strict_types=1);
namespace App\Controller;

use App\Services\workTimeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WorkTimeController extends AbstractController
{
    #[Route('/workTime', methods: ['POST'])]
    public function addWorkTime(Request $request, workTimeService $workTimeService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['unikalny identyfikator pracownika'], $data['data i godzina rozpoczęcia'], $data['data i godzina zakończenia'])) {
            return $this->json(['error' => 'Brakuje danych'], 400);
        }

        try {
            $response = $workTimeService->addWorkTime(
                $data['unikalny identyfikator pracownika'],
                $data['data i godzina rozpoczęcia'],
                $data['data i godzina zakończenia']
            );

            return $this->json(['response' => $response]);

        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);

        } catch (\RuntimeException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}