<?php

namespace App\Repository;

use App\Entity\Figure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Figure>
 *
 * @method Figure|null find($id, $lockMode = null, $lockVersion = null)
 * @method Figure|null findOneBy(array $criteria, array $orderBy = null)
 * @method Figure[]    findAll()
 * @method Figure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FigureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Figure::class);
    }

    public function save(Figure $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Figure $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findFiguresPaginated($limit = 4): array
    {
        $limit = (int) $limit;
        $limit = abs($limit);

        $result = [];

        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('f')
            ->from('App\Entity\Figure', 'f')
            ->setMaxResults($limit);

        $paginator = new Paginator($query);
        $data = $paginator->getQuery()->getResult();

        //Verif qu'il y a des données
        if(empty($data)){
            return $result;
        }

        //Calcul du nombre total de figures
        $nbFiguresMax = $paginator->count();

        //On remplt le tableau
        $result['data'] = $data;
        $result['nbFiguresMax'] = $nbFiguresMax;
        $result['limit'] = $limit;

        return $result;
    }
}
