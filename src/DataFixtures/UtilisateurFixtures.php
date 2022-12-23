<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager as PersistenceObjectManager;

class UtilisateurFixtures extends Fixture
{
    public function load(PersistenceObjectManager $manager)
    {
        $user1 = new Utilisateur();
        $user1->setNomUtilisateur('John Doe');
        $user1->setEmail('john.doe@gmail.com');
        $user1->setPassword(password_hash('johndoe', PASSWORD_BCRYPT));
        $user1->setIsVerified(true);
        $user1->setDateInscription(new \DateTime());
        $user1->setResetToken('');
        $manager->persist($user1);

        $user2 = new Utilisateur();
        $user2->setNomUtilisateur('GandalfLeBlanc');
        $user2->setEmail('Gandalf@gmail.com');
        $user2->setPassword(password_hash('gandalf', PASSWORD_BCRYPT));
        $user2->setIsVerified(true);
        $user2->setDateInscription(new \DateTime());
        $user2->setResetToken('');
        $manager->persist($user2);

        $user3 = new Utilisateur();
        $user3->setNomUtilisateur('toto');
        $user3->setEmail('toto@gmail.com');
        $user3->setPassword(password_hash('tototest', PASSWORD_BCRYPT));
        $user3->setIsVerified(true);
        $user3->setDateInscription(new \DateTime());
        $user3->setResetToken('');
        $manager->persist($user3);

        $manager->flush();

        $this->addReference('user1', $user1);
        $this->addReference('user2', $user2);
        $this->addReference('user3', $user3);
    }
}
