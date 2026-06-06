<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Repository\EvenementsRepository;

final class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(): Response
    {
        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
        ]);
    }
    #[Route('/order/checkout/{id}', name: 'app_create_checkout')]
    public function createStripeCheckoutSession(EvenementsRepository $evenementsRepository, int $id):Response
    {
        Stripe::setApiKey($this->getParameter('stripe_secret_key'));
        $evenement = $evenementsRepository->find($id);
        if (!$evenement) {
            throw $this->createNotFoundException('Evenement non trouvé');
        }
        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $evenement->getNom(),
                        ],
                        'unit_amount' => $evenement->getPrixTicket() * 100,
                    ],
                    'quantity' => 1,
            ]],
            'mode' => 'payment', // ou 'payment' pour une commande classique
            'success_url' => $this->generateUrl('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($checkout_session->url);
    }
    
    #[Route('/order/success', name: 'payment_success')]
    public function success(): Response
    {
        return $this->render('order/success.html.twig');
    }

    #[Route('/order/cancel', name: 'payment_cancel')]
    public function cancel(): Response
    {
        return $this->render('order/cancel.html.twig');
    }
}
