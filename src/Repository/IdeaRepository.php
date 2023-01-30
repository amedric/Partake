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
                   project.user_id,
                   project.content,
                   project.project_views,
                   project.project_color,
                   project.is_archived,
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

    public function countNumberIdeas(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            select count(*) as nbIdeas from idea;
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function countTotalIdeaViews(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            select SUM(idea_views) as nbIdeaViews from idea;
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function findAllIdeasByProjectId($id, $column, $orderBy): array
    {
        $order = " " . $column . " " . $orderBy;
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            select
                i.id,
                i.title,
                i.content,
                i.idea_color as ideaColor,
                i.idea_views as ideaViews,
                i.project_id as projectId,
                (select count(l.id)
                    FROM `like` as l
                    where l.idea_id = i.id) as ideaLikes,
                (select count(c.id)
                    FROM comment as c
                    where c.idea_id = i.id) as ideaComments,
                i.created_at as createdAt
            from project as p
            left join idea as i on p.id = i.project_id
            where p.id = :id
            order by' . $order
            ;
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['id' => $id]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }
}
