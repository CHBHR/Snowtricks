<?php

namespace App\Controller;

use App\Entity\Images;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\InscriptionType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use App\Entity\Utilisateur;
use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UtilisateurRepository;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    #[Route('/inscription', name: 'app_security_inscription')]
    public function inscription(Request $request, ManagerRegistry $manager, UserPasswordHasherInterface $encoder, SendMailService $sendMail, JWTService $jwt)
    {
        $utilisateur = new Utilisateur();

        $entityManager = $manager->getManager();

        $form = $this->createForm(InscriptionType::class, $utilisateur);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->hashPassword(
                $utilisateur, 
                $utilisateur->getMotDePasse()
            );
            $utilisateur->setMotDePasse($hash);
            $utilisateur->setDateInscription(new \DateTime());
            $utilisateur->setResetToken('');

            $entityManager->persist($utilisateur);

             /**
             * Gestion de l'upload des images
             */
            $avatars = $form->get('avatar')->getData();
            foreach($avatars as $avatar){
                //Gestion du nom du fichier
                $fichier = md5(uniqid()).'.'.$avatar->guessExtension();

                //Copie du fichier dans le dossier uploads
                $avatar->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                //Création de l'image en db
                
                $avat = new Images();
                $avat->setNom($fichier);

                $entityManager->persist($avat);
                $utilisateur->setAvatar($avat);

            }
            $entityManager->flush();

            //Génération du JWT utilisateur
            //Création du header
            $header = [
                'alg' => 'HS256',
                'typ' => 'JWT'
            ];

            //Création du payload
            $payload = [
                'user_id' => $utilisateur->getId()
            ];

            //Génération du token
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            //Envoie de l'email
            $sendMail->send(
                'no-reply@monsite.net',
                $utilisateur->getEmail(),
                'Valider votre compte sur SnowTricks',
                'emailInscription',
                //on pourrai ici aussi écrire compact($utilisateur)
                [
                    'utilisateur' => $utilisateur,
                    'token' => $token
                ]
            );

            return $this->redirectToRoute('app_security_connexion');
        }

        return $this->render('security/inscription.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/verification/{token}', name:'app_verify_user')]
    public function verifyUser($token, JWTService $jwt, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $em): Response
    {
        //Vérif si le token est valide, n'a pas exp et n'a pas été modif.
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->checkSignature($token, $this->getParameter('app.jwtsecret'))){
            //le token est valide

            //récup payload
            $payload = $jwt->getPayload($token);

            //récup utilisateur du token
            $utilisateur = $utilisateurRepository->find($payload['user_id']);

            //vérif que l'utilisateur existe et n'a pas encore activer son compte
            if($utilisateur && !$utilisateur->isIsVerified()){
                $utilisateur->setIsVerified(true);
                $em->flush($utilisateur);
                $this->addFlash('success', 'Votre adresse mail a bien été vérifiée');
                return $this->redirectToRoute('app_home');
            }
        }
        //gestion des problèmes du token invalide
        $this->addFlash('danger', 'Le token est invalide ou a expiré');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/resendverif', name:'app_resend_verif')]
    public function resendVerification(JWTService $jwt, SendMailService $sendMail, UtilisateurRepository $utilisateur): Response
    {
        $utilisateur = $this->getUser();

        if(!$utilisateur){
            $this->addFlash('danger', 'Vous devez être connecter pour accéder à cette page');
            return $this->redirectToRoute('app_security_connexion');
        }

        if($utilisateur->isIsVerified()){
            $this->addFlash('warning', 'Le mail a déjà été validé');
            return $this->redirectToRoute('app_home');
        }

        //Voir bloc plus haut
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];

        $payload = [
            'user_id' => $utilisateur->getId()
        ];

        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        $sendMail->send(
            'no-reply@monsite.net',
            $utilisateur->getEmail(),
            'Valider votre compte sur SnowTricks',
            'emailInscription',
            [
                'utilisateur' => $utilisateur,
                'token' => $token
            ]
        );
        $this->addFlash('success', 'Email de vérification envoyé');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/connexion', name: 'app_security_connexion')]
    public function connexion()
    {
        return $this->render('security/connexion.html.twig');
    }

    /**
     * Le composant de sécurité se charge de la déconnexion
     */
    #[Route('/deconnexion', name:"app_security_deconnexion")]
    public function deconnexion() {}

    /**
     * Réinitialisation du mot de passe
     */
    #[Route('/oublie-pass', name:"app_security_oublie_pass")]
    public function forgottenPassword( Request $request, UtilisateurRepository $utilisateurRepository, TokenGeneratorInterface $tokenGenerator, EntityManagerInterface $entityManager, SendMailService $mail): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //On cherche l'utilisateur par son email
            $utilisateur = $utilisateurRepository->findOneByEmail($form->get('email')->getData());

            //On vérifie si il existe un utilisateur
            if($utilisateur){
                //on génère un token pour réinitialiser le mot de passe
                $token = $tokenGenerator->generateToken();
                $utilisateur->setResetToken($token);
                $entityManager->persist($utilisateur);
                $entityManager->flush();

                //On génère le lien de réinit.
                $url = $this->generateUrl('app_security_reset_pass', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                //on crée les données du mail
                //ici la contion compact permet une autre écriture
                $context = compact('url', 'utilisateur');

                //Envoi du mail
                $mail->send(
                    'no-reply@snowtricks.fr',
                    $utilisateur->getEmail(),
                    'Réinitialisation de mot de passe',
                    'emailNewPassRequest',
                    $context
                );

                $this->addFlash('success', 'Un email de réinitialisation à été envoyé');
                return $this->redirectToRoute('app_security_connexion');
            }

            //Utilisateur est null
            $this->addFlash('danger', 'Un problème est survenu');
            return $this->redirectToRoute('app_security_connexion');
        }

        return $this->render('security/reset_password_request.html.twig', [
            'requestPassForm' => $form->createView()
        ]);
    }

    #[Route('/oubli-pass/{token}', name:'app_security_reset_pass')]
    public function resetPass(string $token, Request $request, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passworkHasher): Response
    {
        //Vérif. si token dans la db
        $utilisateur = $utilisateurRepository->findOneByResetToken($token);

        //Verif. si utilisateur existe
        if($utilisateur){
            $form = $this->createForm(ResetPasswordFormType::class);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                //Delete du token
                $utilisateur->setResetToken('');

                //On enregistre le nouveau pass en le hashant
                $utilisateur->setMotDePasse(
                    $passworkHasher->hashPassword(
                        $utilisateur,
                        $form->get('password')->getData()
                        )
                    );

                    $entityManager->persist($utilisateur);
                    $entityManager->flush();

                    $this->addFlash('success', 'Le mot de passe à été changé avec succès');
                    return $this->redirectToRoute('app_security_connexion');
            }

            return $this->render('security/reset_password.html.twig', [
                'passForm' => $form->createView()
            ]);
        }

        //Si le token est invalide
        $this->addFlash('danger', 'Token invalide');
        return $this->redirectToRoute('app_security_connexion');
    }
}
