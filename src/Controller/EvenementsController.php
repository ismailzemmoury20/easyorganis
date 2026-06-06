<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\EvenementsRepository;

final class EvenementsController extends AbstractController
{
    #[Route('/evenements', name: 'app_evenements')]
    public function index(EvenementsRepository $evenementsRepository): Response
    {
        $evenements = $evenementsRepository->findAll();

        return $this->render('evenements/index.html.twig', [
            'evenements' => $evenements,
        ]);
    }
}
