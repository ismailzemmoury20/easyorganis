<?php

namespace App\Controller;

use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\TicketsRepository;
use App\Entity\Commandes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class StripeController extends AbstractController
{
    public function __construct(private StripeService $stripeService) 
    {}
    
    #[Route('/create-payment-link', name: 'create_payment_link', methods: ['POST'])]
    public function createPaymentLink(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        
        try {
            $paymentLink = $this->stripeService->createPaymentLink([
                'product_name' => $data['product_name'],
                'description' => $data['description'] ?? '',
                'amount' => $data['amount'],
                'quantity' => $data['quantity'] ?? 1,
                'success_url' => $this->generateUrl('payment_success', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('payment_cancel', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL),
                'order_id' => $data['order_id'] ?? uniqid(),
                'customer_email' => $data['customer_email'] ?? '',
            ]);
            
            return $this->json([
                'success' => true,
                'payment_link' => $paymentLink
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
    
    #[Route('/payment/success', name: 'payment_success')]
    public function paymentSuccess(Request $request,TicketsRepository $ticketsRepository,EntityManagerInterface $em,MailerInterface $mailer): Response 
    {
        $user = $this->getUser();
        $ticketId = $request->query->get('ticket_id');
        $ticket = $ticketsRepository->find($ticketId);

        if (!$ticket) {
            $this->addFlash('error', 'Ticket introuvable.');
            return $this->redirectToRoute('app_acheteur');
        }

        // Mettre à jour le ticket
        $ticket->setStatut('vendu');
        $ticket->setUserId($user);
        $ticket->setDateAchat(new \DateTime());

        // Créer la commande
        $commande = new Commandes();
        $commande->setEvenementId($ticket->getEvenementId());
        $commande->setUserId($user);
        $commande->setMontantTotal($ticket->getEvenementId()->getPrixTicket());
        $commande->setDateCommande(new \DateTime());
        $commande->setAchat(1);
        $commande->setStatut('payé');
        $em->persist($commande);
        $em->flush();

        // Envoyer l'email
        $email = (new TemplatedEmail())
            ->from(new Address('ismail.zamouri3@gmail.com', 'EasyOrganis'))
            ->to($user->getUserIdentifier())
            ->subject('Votre ticket — ' . $ticket->getEvenementId()->getNom())
            ->htmlTemplate('acheteur/email_ticket.html.twig')
            ->context([
                'ticket' => $ticket,
                'evenement' => $ticket->getEvenementId(),
            ]);
        $mailer->send($email);

        $this->addFlash('success', 'Ticket acheté avec succès !');

        // Rediriger vers le PDF
        return $this->redirectToRoute('app_acheteur_ticket_pdf', ['id' => $ticket->getId()]);
    }
    
    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function paymentCancel(): Response
    {
        return $this->render('stripe/cancel.html.twig');
    }
}
