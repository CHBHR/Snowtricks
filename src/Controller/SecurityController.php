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

class SecurityController extends AbstractController
{
    #[Route('/inscription', name: 'app_security_inscription')]
    public function inscription(Request $request, ManagerRegistry $manager, UserPasswordHasherInterface $encoder)
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

            return $this->redirectToRoute('app_security_connexion');
        }

        return $this->render('security/inscription.html.twig', [
            'form' => $form->createView()
        ]);
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
}
