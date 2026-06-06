<?php

namespace App\Service;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class QwenService
{
    public function __construct(private HttpClientInterface $httpClient, private string $qwenApiKey)
    {}
    public function generateDescription(string $nom, string $lieu, string $date, string $categorie, string $descriptionBrute = null): ?string
    {
        $prompt = $descriptionBrute
            ? "Voici la description brute d'un événement écrite par l'organisateur : \"$descriptionBrute\". 
               Améliore-la pour la rendre plus attrayante, professionnelle et engageante en français. 
               Garde le sens original mais enrichis le style. Maximum 150 mots."
            : "Génère une description pour un événement nommé $nom qui sera tenu à $lieu le $date 
               et qui appartient à la catégorie $categorie. La description doit être concise, 
               attrayante et donner envie aux gens de participer à l'événement. Maximum 150 mots.";

        $reponse = $this->httpClient->request('POST', 'https://ws-cb6ztwpbeet4xwkw.ap-southeast-1.maas.aliyuncs.com/compatible-mode/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->qwenApiKey,
                'Content-Type'  => 'application/json'
                ],
                'json' => [
                    'model' => 'qwen-plus',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ]
                ]
            ]);
        $data = $reponse->toArray();
        return $data['choices'][0]['message']['content'] ?? null;
    }
}