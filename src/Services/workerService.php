<?php

namespace App\Services;
use App\Entity\Worker;
use Doctrine\ORM\EntityManagerInterface;

class workerService
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    public function createWorker(string $name, string $surname): Worker
    {
        $worker = new Worker();
        $worker->setName($name);
        $worker->setSurname($surname);

        $this->em->persist($worker);
        $this->em->flush();

        return $worker;
    }

}