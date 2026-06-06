<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\EvenementsRepository;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\TicketsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Commandes;
use App\Repository\CommandesRepository;
use App\Repository\CategoriesRepository;

final class AcheteurController extends AbstractController
{
    #[Route('/acheteur', name: 'app_acheteur')]
    public function index(EvenementsRepository $evenementsRepository, CategoriesRepository $categoriesRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $query = $evenementsRepository->findEvenements();
        $evenements = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );
        return $this->render('acheteur/index.html.twig', [
            'evenements'  => $evenements,
            'categories'  => $categoriesRepository->findAll(),
        ]);
    }
    #[Route('/acheteur/evenements', name: 'app_acheteur_evenements_filtre')]
    public function evenementByFilter(EvenementsRepository $evenementsRepository, Request $request): Response
    {
        $categorie = $request->query->get('categorie');
        $lieu = $request->query->get('lieu');
        $evenement = $evenementsRepository->findEvenementsFilter($categorie, $lieu);
        return $this->render('acheteur/evenements_details.html.twig', [
            'evenement' => $evenement,
        ]);
    }
    #[Route('/acheteur/evenements/{id}', name: 'app_acheteur_evenements_details')]
    public function evenementDetails(EvenementsRepository $evenementsRepository, int $id): Response
    {
        $evenement = $evenementsRepository->find($id);
        if (!$evenement) {
            throw $this->createNotFoundException('Evenement non trouvé');
        }
        return $this->render('acheteur/evenements_details.html.twig', [
            'evenement' => $evenement,
        ]);
    }
    #[Route('/acheteur/evenements/{id}/acheter', name: 'app_acheteur_acheter')]
    public function acheterTicker(TicketsRepository $ticketsRepository, Request $request, int $id, EntityManagerInterface $em): Response
    {
        
        $ticket = $ticketsRepository->findTicketsByEvenement($id);
        if(empty($ticket)){
            $this->addFlash('error', 'Aucun ticket disponible pour cet événement.');
            return $this->redirectToRoute('app_acheteur_evenements_details', ['id' => $id]);
        } else {
            $ticket->setStatut('vendu');
            $commande = new Commandes();
            $commande->setEvenementId($ticket->getEvenementId());
            $commande->setUserId($this->getUser());
            $commande->setMontantTotal($ticket->getEvenementId()->getPrixTicket());
            $commande->setDateCommande(new \DateTime());
            $commande->setAchat(1);
            $commande->setStatut('payé');
            $em->persist($commande);
            $ticket->setUserId($this->getUser());
            $ticket->setDateAchat(new \DateTime());
            $em->flush();
            $this->addFlash('success', 'Ticket acheté avec succès !');

            return $this->redirectToRoute('app_acheteur_evenements_details', ['id' => $id]);
        }
    }
    #[Route('/acheteur/commandes', name: 'app_acheteur_commandes')]
    public function commandes(CommandesRepository $commandesRepository, TicketsRepository $ticketsRepository): Response
    {
        $commandes = $commandesRepository->findAllTicketsPurchasedByUser($this->getUser());
        $tickets   = $ticketsRepository->findAllTicketsPurchasedByUser($this->getUser());

        if (empty($commandes)) {
            $this->addFlash('info', 'Vous n\'avez aucune commande pour le moment.');
        }

        return $this->render('acheteur/commandes.html.twig', [
            'commandes' => $commandes,
            'tickets'   => $tickets,
        ]);
    }
}
