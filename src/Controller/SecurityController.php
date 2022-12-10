<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\InscriptionType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;

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

            $entityManager->persist($utilisateur);
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
     * Demande d'un nouveau mot de passe
     */
    #[Route('/oubliePass', name:"app_security_oubliePass")]
    public function oubliePass()
    {
        //Check du mail: présent en base de donnée? flash
        //envois d'un mail avec un token
        //redirection sur la pag d'accueil
        return $this->render('security/oubliePass.html.twig');
    }

    /**
     * Réinitialisation du mot de passe
     */
    #[Route('/nouveauPass', name:"app_security_nouveauPass")]
    public function newPass()
    {
        //lien vers une page 'nouveau mot de passe'
        //check validité du token
        //check si le mot de passe est le même que l'ancien
        //check si la confirmation est la même que le mdp
        //redirection page d'accueil logged in
        //flash 'le nouveau mot de passe a bien été enregistré'
    }
}
