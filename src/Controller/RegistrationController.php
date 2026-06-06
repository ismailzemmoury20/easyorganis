<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                // Affiche toutes les erreurs de validation en flash pour débugger
                $errors = [];
                foreach ($form->getErrors(true) as $error) {
                    $errors[] = $error->getMessage();
                }
                $this->addFlash('error', 'Erreurs : ' . implode(' | ', $errors));
            } else {
                /** @var string $plainPassword */
                $plainPassword = $form->get('plainPassword')->getData();

                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

                $typeCompte = $request->request->all()['registration_form']['typeCompte'] ?? 'acheteur';
                if ($typeCompte === 'organisateur') {
                    $user->setRoles(['ROLE_ORGANISATEUR']);
                } else {
                    $user->setRoles(['ROLE_ACHETEUR']);
                }

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Compte créé avec succès ! Connectez-vous pour continuer.');
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
