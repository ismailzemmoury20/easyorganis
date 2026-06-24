<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Repository\EvenementsRepository;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\TicketsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Commandes;
use App\Repository\CommandesRepository;
use App\Repository\CategoriesRepository;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCodeBundle\Response\QrCodeResponse;
use Nucleos\DompdfBundle\Wrapper\DompdfWrapperInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

final class AcheteurController extends AbstractController
{
    public function __construct(private BuilderInterface $customQrCodeBuilder)
    {
    }

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
    public function acheterTicker(MailerInterface $mailer, TicketsRepository $ticketsRepository, Request $request, int $id, EntityManagerInterface $em, \App\Service\StripeService $stripeService): Response
    {
        $user = $this->getUser();
        if (!$user instanceof \App\Entity\User) {
            throw $this->createAccessDeniedException();
        }

        $ticket = $ticketsRepository->findTicketsByEvenement($id);
        if(empty($ticket)){
            $this->addFlash('error', 'Aucun ticket disponible pour cet événement.');
            return $this->redirectToRoute('app_acheteur_evenements_details', ['id' => $id]);
        }
        $paymentLink = $stripeService->createPaymentLink([
            'product_name' => $ticket->getEvenementId()->getNom(),
            'amount' => $ticket->getEvenementId()->getPrixTicket(),
            'quantity' => 1,
            'success_url' => $this->generateUrl('payment_success', ['ticket_id' => $ticket->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($paymentLink); 

    }
    
    #[Route('/acheteur/evenements/{id}/qr', name: 'app_acheteur_code')]
    public function ticketCodeQr(BuilderInterface $customQrCodeBuilder ,TicketsRepository $ticketsRepository, int $id): Response
    {
        $ticket = $ticketsRepository->find($id);
        if (!$ticket || $ticket->getUserId() !== $this->getUser()) {
            throw $this->createNotFoundException('Ticket non trouvé');
        }

        $result = $customQrCodeBuilder->build(
            data: $ticket->getCodeUnique(),
            size: 300,
            margin: 20
        );

        return new QrCodeResponse($result);
    }

    #[Route('/acheteur/ticket/{id}/pdf', name:'app_acheteur_ticket_pdf')]
    public function generateTicketPdf(DompdfWrapperInterface $wrapper,TicketsRepository $ticketsRepository,int $id): Response
    {
        $ticket = $ticketsRepository->find($id);
        if (!$ticket || $ticket->getUserId() !== $this->getUser()) {
            throw $this->createNotFoundException('Ticket non trouvé');
        }
        $html = $this->renderView('/acheteur/ticket_pdf.html.twig', [
            'ticket' => $ticket,
            'evenement' => $ticket->getEvenementId(),
        ]);
        return $wrapper->getStreamResponse($html, 'ticket-' . $ticket->getCodeUnique() . '.pdf');
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
