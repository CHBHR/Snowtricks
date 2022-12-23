<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Utilisateur>
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    public function save(Utilisateur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Utilisateur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findImageNameByUserId(int $userId)
    {
        $names = ['i.nom'];

        $query = $this->getEntityManager()->createQueryBuilder()
        ->select($names, 'u')
        ->from('App\Entity\Images', 'i')
        ->join('i.utilisateur', 'u')
        ->where("u.id = '$userId'")
        ;

        $data = $query->getQuery()->getResult();

        return $data;
    }
}
