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
use App\Entity\Tickets;
use App\Service\QwenService;
use App\Entity\Categories;
use App\Repository\TicketsRepository;

final class OrganisateurController extends AbstractController


{
    #[Route('/organisateur', name: 'app_organisateur')]
    public function index(EvenementsRepository $evenementsRepository, Request $request, EntityManagerInterface $entityManager, QwenService $qwenService): Response
    {
        $evenement = new Evenements();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $evenement->setUserId($this->getUser());
            $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $evenement->getNom()));
            $evenement->setSlug($slug . '-' . uniqid());

            $categorieText = trim($form->get('categorie')->getData() ?? '');
            if ($categorieText !== '') {
                $categorieEntity = $entityManager->getRepository(Categories::class)->findOneBy(['nom_categorie' => $categorieText]);
                if (!$categorieEntity) {
                    $categorieEntity = new Categories();
                    $categorieEntity->setNomCategorie($categorieText);
                    $entityManager->persist($categorieEntity);
                }
                $evenement->setCategorie($categorieEntity);
            } else {
                $evenement->setCategorie(null);
            }

            $entityManager->persist($evenement);
            $entityManager->flush();
            for($i= 0; $i < $evenement->getNombresPlaces(); $i++) {
                $ticket = new Tickets();
                $ticket->setEvenementId($evenement);
                $ticket->setStatut('disponible');
                $ticket->setCodeUnique(strtoupper(uniqid('TK-')));
                $entityManager->persist($ticket);
            }
            $entityManager->flush();
            return $this->redirectToRoute('app_organisateur_evenements');
        }

        return $this->render('organisateur/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/organisateur/evenements/{id}', name: 'app_organisateur_evenements_details')]
    public function mesEvenementsDetails(EvenementsRepository $evenementsRepository, int $id):Response
    {
        $evenement = $evenementsRepository->find($id);
        if(!$evenement){
            throw $this->createNotFoundException('Evenement non trouvé');
        } else {
            return $this->render('organisateur/mes_evenements_details.html.twig', [
                'evenement' => $evenement,
            ]);
        }
    }
    #[Route('/organisateur/tickets/{id}', name: 'app_organisateur_tickets')]
    public function ticketsRestant(EvenementsRepository $evenementsRepository, int $id):Response
    {
        $ticketsRestants = $evenementsRepository->findTicketsRestants($id);
        $ticketsVendus = $evenementsRepository->findTicketsVendus($id);
        return $this->render('organisateur/tickets_restants.html.twig', [
            'ticketsRestants' => $ticketsRestants,
            'ticketsVendus' => $ticketsVendus,
            'id' => $id,
        ]);
    }
    #[Route('/organisateur/evenements', name: 'app_organisateur_evenements')]
    public function evenements(EvenementsRepository $evenementsRepository,TicketsRepository $ticketsRepository): Response
    {
        $evenements = $evenementsRepository->findBy([
        'user_id' => $this->getUser()
        ]);

        $totalTicketsVendu = 0;
        $totalRevenu = 0;
        $prochainEvenement = null;
        $now = new \DateTime();
        foreach($evenements as $evenement){
            $vendu = $ticketsRepository->count([
                'evenement_id' => $evenement,
                'statut' => 'vendu'
            ]);
            $totalTicketsVendu += $vendu;
            $totalRevenu += $vendu * $evenement->getPrixTicket();

            if($evenement->getDate() > $now){
                if($prochainEvenement === null || $evenement->getDate() < $prochainEvenement->getDate()){
                    $prochainEvenement = $evenement;
                }
            }
        }

        return $this->render('organisateur/mes_evenements.html.twig', [
            'evenements' => $evenements,
            'total_evenements' => count($evenements),
            'total_tickets_vendus' => $totalTicketsVendu,
            'total_revenus' => $totalRevenu,
            'prochain_evenement' => $prochainEvenement,
        ]);
    }
    #[Route('/organisateur/evenements/{id}/modifier', name: 'app_organisateur_modifier')]
    public function modifierEvenement(EvenementsRepository $evenementsRepository, Request $request, EntityManagerInterface $em, int $id): Response
    {
        $evenement = $evenementsRepository->find($id);
        if(!$evenement){
            throw $this->createNotFoundException('Evenement non trouvé');
        }
        $form = $this->createForm(EvenementType::class, $evenement, ['is_edit' => true]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $categorieText = trim($form->get('categorie')->getData() ?? '');
            if ($categorieText !== '') {
                $categorieEntity = $em->getRepository(Categories::class)->findOneBy(['nom_categorie' => $categorieText]);
                if (!$categorieEntity) {
                    $categorieEntity = new Categories();
                    $categorieEntity->setNomCategorie($categorieText);
                    $em->persist($categorieEntity);
                }
                $evenement->setCategorie($categorieEntity);
            } else {
                $evenement->setCategorie(null);
            }
            $em->flush();
            return $this->redirectToRoute('app_organisateur_evenements');
        }
        return $this->render('organisateur/modifier.html.twig', [
            'form' => $form->createView(),
            'evenement' => $evenement,
        ]);
    }
    #[Route('/organisateur/evenements/{id}/supprimer', name: 'app_organisateur_supprimer')]
    public function supprimerEvenement(EvenementsRepository $evenementsRepository, EntityManagerInterface $em, int $id): Response
    {
        $evenement = $evenementsRepository->find($id);
        if(!$evenement){
            throw $this->createNotFoundException('Evenement non trouvé');
        }
        $em->remove($evenement);
        $em->flush();
        return $this->redirectToRoute('app_organisateur_evenements');
    }
    #[Route('/organisateur/evenements/{id}/{slug}', name: 'app_organisateur_evenement_page')]
    public function evenementPage(Evenementsrepository $evenementsRepository, int $id, string $slug, Request $request): Response
    {
        $evenement = $evenementsRepository->find($id);
        if(!$evenement){
            throw $this->createNotFoundException('Evenement non trouvé');
        } else {
            return $this->render('organisateur/evenement_page.html.twig', [
                'evenement' => $evenement,
            ]);
        }
    }
    #[Route('/organisateur/evenements/{id}/publier', name: 'app_organisateur_publier')]
    public function publierEvenement(EvenementsRepository $evenementsRepository, EntityManagerInterface $em, int $id):Response
    {
        $evenement = $evenementsRepository->find($id);
        if(!$evenement){
            throw $this->createNotFoundException('Evenement non trouvé');
        }
        $evenement->setIsPublished(true);
        $em->flush();
        return $this->redirectToRoute('app_organisateur_evenements');
    }
    #[Route('/organisateur/generer-description', name: 'app_generer_description')]
    public function genererDescription(Request $request, QwenService $qwenService): Response
    {
        $data = json_decode($request->getContent(), true);
        try {
            $description = $qwenService->generateDescription(
                $data['nom'] ?? '',
                $data['lieu'] ?? '',
                $data['date'] ?? '',
                $data['categorie'] ?? '',
                $data['description'] ?? ''
            );
            return $this->json(['description' => $description]);
        } catch (\Throwable $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}
