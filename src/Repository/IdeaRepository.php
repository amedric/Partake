<?php

namespace App\Repository;

use App\Entity\Idea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Idea>
 *
 * @method Idea|null find($id, $lockMode = null, $lockVersion = null)
 * @method Idea|null findOneBy(array $criteria, array $orderBy = null)
 * @method Idea[]    findAll()
 * @method Idea[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IdeaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Idea::class);
    }

    public function save(Idea $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Idea $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findLikeIdea(string $name): array
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->where('i.title LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->orderBy('i.title', 'ASC')
            ->getQuery();
        return $queryBuilder->getResult();
    }

    public function findIdeaByUser(string $id): array
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->where('i.project_id LIKE :id')
            ->getQuery();
        return $queryBuilder->getResult();
    }

    public function findIdeasCount(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            select project.id,
                   project.title,
                   project.category_id,
                   project.content,
                   project.project_views,
                   project.project_color,
                   count(idea.id) as ideaCount
            from project
            left join idea on project.id = idea.project_id
            group by project.id
            order by ideaCount desc
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function countIdeasByProject(): array
    {
        // automatically knows to select Products
        // the "p" is an alias you'll use in the rest of the query
        $qb = $this->createQueryBuilder('i')
            ->select('p.id as projectId', 'COUNT(i.id) as ideaCount')
            ->join('i.project', 'p')
            ->groupBy('p.id')
            ->getQuery();


        return $qb->getResult();
    }

//    /**
//     * @return Idea[] Returns an array of Idea objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Idea
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
