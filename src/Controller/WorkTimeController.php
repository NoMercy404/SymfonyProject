<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WorkTimeController extends AbstractController
{
    #[Route('/work/time', name: 'app_work_time')]
    public function index(): Response
    {
        return $this->render('work_time/index.html.twig', [
            'controller_name' => 'WorkTimeController',
        ]);
    }
}
