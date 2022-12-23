<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Utilisateur;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ConnexionController extends AbstractController
{
    #[Route('/snowtricks/connexion', name: 'app_security_connexion')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/connexion.html.twig', [
            'controller_name' => 'LoginController',
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/snowtricks/inscription', name: 'app_security_inscription')]
    public function inscription(Request $request, ManagerRegistry $manager, UserPasswordHasherInterface $encoder, SendMailService $sendMail, JWTService $jwt)
    {
        $utilisateur = new Utilisateur();
        $defaultAvatar = 'defaultAvatar.jpg';

        $entityManager = $manager->getManager();

        $form = $this->createForm(InscriptionType::class, $utilisateur);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->hashPassword(
                $utilisateur,
                $utilisateur->getMotDePasse()
            );
            $utilisateur->setMotDePasse($hash);
            $utilisateur->setDateInscription(new \DateTime());
            $utilisateur->setResetToken('');

            if (!$form->get('avatar')->getData()) {
                $default = new Images();
                $default->setNom($defaultAvatar);
                $entityManager->persist($utilisateur);

                // Création de l'image en db

                $entityManager->persist($default);
                $utilisateur->setAvatar($default);
            } else {
                /**
                 * Gestion de l'upload des images.
                 */
                $avatar = $form->get('avatar')->getData();

                // Gestion du nom du fichier
                $fichier = md5(uniqid()).'.'.$avatar->guessExtension();

                // Copie du fichier dans le dossier uploads
                $avatar->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                $avat = new Images();
                $avat->setNom($fichier);

                $entityManager->persist($utilisateur);

                $entityManager->persist($avat);
                $utilisateur->setAvatar($avat);
            }

            $entityManager->flush();

            // Génération du JWT utilisateur
            // Création du header
            $header = [
                'alg' => 'HS256',
                'typ' => 'JWT',
            ];

            // Création du payload
            $payload = [
                'user_id' => $utilisateur->getId(),
            ];

            // Génération du token
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            // Envoie de l'email
            $sendMail->send(
                'no-reply@monsite.net',
                $utilisateur->getEmail(),
                'Valider votre compte sur SnowTricks',
                'emailInscription',
                // on pourrai ici aussi écrire compact($utilisateur)
                [
                    'utilisateur' => $utilisateur,
                    'token' => $token,
                ]
            );

            $this->addFlash('success', 'Votre inscription a bien été validée');

            return $this->redirectToRoute('app_security_connexion');
        }

        return $this->render('security/inscription.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/snowtricks/deconnexion', name: 'app_security_deconnexion')]
    public function deconnexion()
    {
    }
}
