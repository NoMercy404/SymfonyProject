<?php
declare(strict_types=1);
namespace App\Services;

use App\Entity\WorkTime;
use App\Entity\Worker;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
class workTimeService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function addWorkTime(string $uuid, string $startDate, string $endDate): array
    {
        $start = DateTimeImmutable::createFromFormat('d.m.Y H:i', $startDate);
        $end = DateTimeImmutable::createFromFormat('d.m.Y H:i', $endDate);

        if (!$start || !$end || $start >= $end) {
            throw new \InvalidArgumentException('Nieprawidłowy przedział czasowy');
        }

        /** @var Worker|null $worker */
        $worker = $this->em->getRepository(Worker::class)->find($uuid);
        if (!$worker) {
            throw new \RuntimeException('Nie znaleziono pracownika');
        }

        $beginDate = DateTimeImmutable::createFromFormat('Y-m-d', $start->format('Y-m-d'));

        $exist = $this->em->getRepository(WorkTime::class)->findOneBy([
            'worker' => $worker,
            'beginDate' => $beginDate
        ]);

        if ($exist) {
            throw new \RuntimeException('Już istnieje zapis czasu pracy dla tego dnia');
        }

        $timediff = $end->getTimestamp() - $start->getTimestamp();
        if (($timediff / 3600) > 12) {
            throw new \RuntimeException('Nie można rejestrować więcej niż 12 godzin');
        }

        $time = new WorkTime();
        $time->setWorker($worker);
        $time->setStartDate($start);
        $time->setEndDate($end);
        $time->setBeginDate($beginDate);

        $this->em->persist($time);
        $this->em->flush();

        return ['Czas pracy został dodany!'];
    }
}