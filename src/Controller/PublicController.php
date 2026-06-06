<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Evenements;
use App\Form\EvenementType;
use App\Repository\EvenementsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

final class PublicController extends AbstractController
{
    #[Route('/public', name: 'app_public')]
    public function index(): Response
    {
        return $this->render('public/index.html.twig', [
            'controller_name' => 'PublicController',
        ]);
    }
    #[Route('/event/{slug}', name: 'app_public_evenement')]
    public function show(EvenementsRepository $evenementsRepository, string $slug): Response
    {
        $evenement = $evenementsRepository->findOneBy(['slug' => $slug, 'isPublished' => true]);
        if (!$evenement) {
            throw $this->createNotFoundException('Evenement non trouvé ou non publié');
        }
        return $this->render('public/landing_page.html.twig', [
            'evenement' => $evenement,
        ]);
    }
}
