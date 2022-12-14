<?php

namespace App\Repository;

use App\Entity\Commentaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commentaire>
 *
 * @method Commentaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commentaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commentaire[]    findAll()
 * @method Commentaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commentaire::class);
    }

    public function save(Commentaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Commentaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findCommentairesPaginated(int $figureId, int $limit): array
    {
        $result = [];

        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('c', 'f')
            ->from('App\Entity\Commentaire', 'c')
            ->join('c.figure', 'f')
            ->where("f.id = '$figureId'")
            ->orderBy('c.dateCreation', 'DESC')
            ->setMaxResults($limit);

        $paginator = new Paginator($query);
        $data = $paginator->getQuery()->getResult();

        // Verif qu'il y a des données
        if (empty($data)) {
            return $result;
        }

        // Calcul du nombre total de figures
        $nbCommentairesMax = $paginator->count();

        // On remplt le tableau
        $result['data'] = $data;
        $result['nbCommentairesMax'] = $nbCommentairesMax;
        $result['limit'] = $limit;

        return $result;
    }
}
