<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function save(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findLikeProject(string $name): array
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->where('p.title LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->orderBy('p.title', 'ASC')
            ->getQuery();
        return $queryBuilder->getResult();
    }

    public function findIdeasCountLikes(int $id): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            select project.id,
                   idea.id,
                   idea.title,
                   idea.content,
                   idea.idea_color as "ideaColor",
                   idea.idea_views as "ideaViews",
                   count(`like`.idea_id) as ideaLikes
            from project
            left join idea on project.id = idea.project_id
            left join `like` on idea.id = `like`.idea_id
            where project.id = :id
            group by `like`.idea_id
            order by ideaLikes desc
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['id' => $id]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function findIdeasCountComments(int $id): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            select project.id,
                   idea.id,
                   idea.title,
                   idea.content,
                   idea.idea_color as "ideaColor",
                   idea.idea_views as "ideaViews",
                   count(comment.idea_id) as ideaComments
            from project
            left join idea on project.id = idea.project_id
            left join comment on idea.id = comment.idea_id
            where project.id = :id
            group by comment.idea_id
            order by ideaComments desc
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['id' => $id]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

//    /**
//     * @return Project[] Returns an array of Project objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Project
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
