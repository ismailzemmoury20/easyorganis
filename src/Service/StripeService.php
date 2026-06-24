<?php
namespace App\Service;

use Stripe\Stripe;
use Stripe\PaymentLink;
use Stripe\Exception\ApiErrorException;

class StripeService 
{
    private string $secretKey;
    
    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
        Stripe::setApiKey($this->secretKey);
    }

   
    public function createPaymentLink(array $data): string
    {
        try {
            $paymentLink = PaymentLink::create([
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => [
                                'name' => $data['product_name'],
                                'description' => $data['description'] ?? '',
                            ],
                            'unit_amount' => $data['amount'] * 100, // Stripe utilise les centimes
                        ],
                        'quantity' => $data['quantity'] ?? 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => $data['success_url'],
                'cancel_url' => $data['cancel_url'],
                'metadata' => [
                    'order_id' => $data['order_id'] ?? '',
                    'customer_email' => $data['customer_email'] ?? '',
                ],
            ]);
            
            return $paymentLink->url;
        } catch (ApiErrorException $e) {
            throw new \Exception('Erreur lors de la création du lien de paiement: ' . $e->getMessage());
        }
    }

    public function retrievePaymentLink(string $linkId): PaymentLink
    {
        return PaymentLink::retrieve($linkId);
    }
}