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

//    public function findProjectDesc(): array
//    {
//        $conn = $this->getEntityManager()->getConnection();
//
//        $sql = '
//            select project.id,
//                   project.title,
//                   project.category_id,
//                   project.content,
//                   project.project_views,
//                   project.project_color,
//                   project.created_at,
//                   project.is_archived,
//                   count(idea.id) as ideaCount
//            from project
//            left join idea on project.id = idea.project_id
//            group by project.id
//            order by project.created_at desc
//            ';
//        $stmt = $conn->prepare($sql);
//        $resultSet = $stmt->executeQuery();
//
//        // returns an array of arrays (i.e. a raw data set)
//        return $resultSet->fetchAllAssociative();
//    }

//    public function findIdeasCountLikes(int $id): array
//    {
//      $conn = $this->getEntityManager()->getConnection();
//
//        $sql = '
//            select project.id,
//                   idea.id,
//                   idea.title,
//                   idea.content,
//                   idea.idea_color as "ideaColor",
//                   idea.idea_views as "ideaViews",
//                   count(`like`.idea_id) as ideaLikes
//            from project
//            left join idea on project.id = idea.project_id
//            left join `like` on idea.id = `like`.idea_id
//            where project.id = :id
//            group by `like`.idea_id
//            order by ideaLikes desc
//            ';
//        $stmt = $conn->prepare($sql);
//        $resultSet = $stmt->executeQuery(['id' => $id]);
//
//        // returns an array of arrays (i.e. a raw data set)
//        return $resultSet->fetchAllAssociative();
//    }

//    public function findProjectAsc(): array
//    {
//        $conn = $this->getEntityManager()->getConnection();
//
//        $sql = '
//            select project.id,
//                   project.title,
//                   project.category_id,
//                   project.content,
//                   project.project_views,
//                   project.project_color,
//                   project.created_at,
//                   project.is_archived,
//                   count(idea.id) as ideaCount
//            from project
//            left join idea on project.id = idea.project_id
//            group by project.id
//            order by project.created_at asc
//            ';
//        $stmt = $conn->prepare($sql);
//        $resultSet = $stmt->executeQuery();
//
//        // returns an array of arrays (i.e. a raw data set)
//        return $resultSet->fetchAllAssociative();
//    }

//    public function findProjectViewsDesc(): array
//    {
//        $conn = $this->getEntityManager()->getConnection();
//
//        $sql = '
//            select project.id,
//                   project.title,
//                   project.category_id,
//                   project.content,
//                   project.project_views,
//                   project.project_color,
//                   project.created_at,
//                   project.is_archived,
//                   count(idea.id) as ideaCount
//            from project
//            left join idea on project.id = idea.project_id
//            group by project.id
//            order by project.project_views desc
//            ';
//        $stmt = $conn->prepare($sql);
//        $resultSet = $stmt->executeQuery();
//
//        // returns an array of arrays (i.e. a raw data set)
//        return $resultSet->fetchAllAssociative();
//    }

//    public function findIdeasCountComments(int $id): array
//    {
//        $conn = $this->getEntityManager()->getConnection();
//
//        $sql = '
//            select project.id,
//                   idea.id,
//                   idea.title,
//                   idea.content,
//                   idea.idea_color as "ideaColor",
//                   idea.idea_views as "ideaViews",
//                   count(comment.idea_id) as ideaComments
//            from project
//            left join idea on project.id = idea.project_id
//            left join comment on idea.id = comment.idea_id
//            where project.id = :id
//            group by comment.idea_id
//            order by ideaComments desc
//            ';
//        $stmt = $conn->prepare($sql);
//        $resultSet = $stmt->executeQuery(['id' => $id]);
//
//        // returns an array of arrays (i.e. a raw data set)
//        return $resultSet->fetchAllAssociative();
//    }

    public function countNumberProjects(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            select count(*) as nbProjects from project;
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function countTotalProjectViews(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            select SUM(project_views) as nbProjectViews from project;
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function findProjectAuthorizedForUser($id): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT project_id FROM project_user WHERE user_id = :id
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['id' => $id]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    // ----------------- find all projects with idea count ---------------------------------
    public function findAllProjects($column, $orderBy): array
    {
        $orderByPara = $column . " " . $orderBy;
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            select
                p.id,
                p.user_id as userId,
                p.category_id as categoryId,
                p.title,
                p.content,
                p.created_at as createdAt,
                p.project_views as views,
                    (select count(i.id)
                        FROM idea as i
                        where i.project_id = p.id
                        group by p.id) as ideaCount,
                    p.is_archived as isArchived
                from project as p
                order by '. $orderByPara
        ;
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

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
