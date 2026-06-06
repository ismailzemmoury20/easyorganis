<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\EvenementsRepository;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EvenementsRepository $evenementsRepository): Response
    {
        // Redirect already-logged-in users to their dashboard
        if ($this->getUser()) {
            $roles = $this->getUser()->getRoles();
            if (in_array('ROLE_ADMIN', $roles)) {
                return $this->redirectToRoute('admin');
            }
            if (in_array('ROLE_ORGANISATEUR', $roles)) {
                return $this->redirectToRoute('app_organisateur');
            }
            if (in_array('ROLE_ACHETEUR', $roles)) {
                return $this->redirectToRoute('app_acheteur');
            }
        }

        $prochains = $evenementsRepository->findProchains(6);

        return $this->render('home/index.html.twig', [
            'prochains' => $prochains,
        ]);
    }
}
