<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\TicketsVariation;
use App\Repository\TicketsVariationRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EvenementsRepository;
use App\Form\TicketVariationFormType;

final class TicketsVariationController extends AbstractController
{
    #[Route('/tickets/variation/{id}', name: 'app_tickets_variation')]
    public function index(Request $request, EvenementsRepository $evenementsRepository, int $id, EntityManagerInterface $em): Response
    {
        $evenement = $evenementsRepository->find($id);
        if (!$evenement) {
            throw $this->createNotFoundException('Evenement non trouvé');
        }
        $ticketVariation = new TicketsVariation();
        $ticketVariation->setEvenement($evenement);

        $form = $this->createForm(TicketVariationFormType::class, $ticketVariation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ticketVariation);
            $em->flush();
            return $this->redirectToRoute('app_tickets_variation', ['id' => $id]);
        }

        return $this->render('tickets_variation/index.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }
}
