<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\WorkTime;
use App\Entity\Worker;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class SummaryController extends AbstractController
{
    #[Route('/summary', methods: ['POST'])]
    public function makeSummary(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $uuid = $data['unikalny identyfikator pracownika'] ?? null;
        $entryDate = $data['data'] ?? null;

        if (!$uuid || !$entryDate) {
            return $this->json(['error' => 'Brakuje danych'], 400);
        }

        /** @var Worker|null $worker */
        $worker = $em->getRepository(Worker::class)->find($uuid);
        if (!$worker) {
            return $this->json(['error' => 'Nie znaleziono pracownika'], 404);
        }


        $monthlyNorm = $this->getParameter('monthlyNormOfHours');
        $hourRate = $this->getParameter('hourRate');
        $overtimeRate = $this->getParameter('overtimeRate');

        $repo = $em->getRepository(WorkTime::class);

        try {
            if ($dataObj = \DateTimeImmutable::createFromFormat('d.m.Y', $entryDate)) {
                $workTime = $repo->findOneBy([
                    'worker' => $worker,
                    'beginDate' => $dataObj
                ]);

                if (!$workTime) {
                    return $this->json(['error' => 'Brak danych dla podanego dnia'], 404);
                }

                $hours = $this->roundHour($workTime);
                $salary = $hours * $hourRate;

                return $this->json([
                    'response' => [
                        'suma po przeliczeniu' => "{$salary} PLN",
                        'ilość godzin z danego dnia' => $hours,
                        'stawka' => "{$hourRate} PLN"
                    ]
                ]);
            }

            if ($dataObj = \DateTimeImmutable::createFromFormat('m.Y', $entryDate)) {
                $firstDay = $dataObj->modify('first day of this month');
                $lastDay = $dataObj->modify('last day of this month');

                $workTime = $repo->createQueryBuilder('c')
                    ->where('c.worker = :p')
                    ->andWhere('c.beginDate BETWEEN :start AND :end')
                    ->setParameter('p', $worker)
                    ->setParameter('start', $firstDay)
                    ->setParameter('end', $lastDay)
                    ->getQuery()->getResult();

                $summary = 0;
                foreach ($workTime as $record) {
                    $summary += $this->roundHour($record);
                }

                $overtime = max(0, $summary - $monthlyNorm);
                $regular = min($summary, $monthlyNorm);
                $summarySalary= $regular * $hourRate + $overtime * ($hourRate * $overtimeRate);

                return $this->json([
                    'response' => [
                        'ilość normalnych godzin z danego miesiąca' => $regular,
                        'stawka' => "{$hourRate} PLN",
                        'ilość nadgodzin z danego miesiąca' => $overtime,
                        'stawka nadgodzinowa' => ($hourRate * $overtimeRate) . " PLN",
                        'suma po przeliczeniu' => "{$summarySalary} PLN"
                    ]
                ]);
            }

            return $this->json(['error' => 'Nieprawidłowy format daty. Użyj formatu dd.mm.yyyy lub mm.yyyy'], 400);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Błąd przetwarzania daty: ' . $e->getMessage()], 400);
        }
    }

    private function roundHour(WorkTime $workTime): float
    {
        $start = $workTime->getStartDate();
        $end = $workTime->getEndDate();
        $time = ($end->getTimestamp() - $start->getTimestamp()) / 60; // calculation of minutes

        if ($time % 60 < 15) {
            $rounded = floor($time / 60);
        } elseif ($time % 60 < 45) {
            $rounded = floor($time / 60) + 0.5;
        } else {
            $rounded = ceil($time / 60);
        }

        return $rounded;
    }
}
