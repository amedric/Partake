<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

    public function findLikeUser(string $name): array
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->where('u.lastname LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->orderBy('u.lastname', 'ASC')
            ->getQuery();
        return $queryBuilder->getResult();
    }

    /**
     * @throws Exception
     */
    public function findProjectsIdeasForUser(int $id): array
    {

        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT "project" AS projectOrIdea,
                   p.id AS id,
                   p.title AS title,
                   p.project_views AS views,
                   count(i.id) AS counts,
                    c1.category_color AS color,
                   p.created_at AS createdAt,
                    "likes",
                    "liked"
                    FROM project AS p
                LEFT JOIN category AS c1 ON p.category_id = c1.id
            LEFT JOIN idea AS i ON p.id = i.project_id
            WHERE p.user_id = :id
            GROUP BY p.id
            UNION
            SELECT "idea",
                   i1.id,
                   i1.title,
                   i1.idea_views,
                   (SELECT COUNT(idea_id) FROM comment AS c2
                       WHERE c2.idea_id = i1.id),
                   i1.idea_color,
                   i1.created_at,
                   COUNT(l.idea_id) AS likes,
                   (SELECT COUNT(l2.user_id)
	                    FROM idea AS i2
                        LEFT JOIN `like` AS l2 ON i2.id = l2.idea_id
                        WHERE l2.user_id = :id AND l2.idea_id = i1.id
	                    GROUP BY i1.id)
            FROM idea AS i1
                LEFT JOIN `like` AS l ON i1.id = l.idea_id
            WHERE i1.user_id = :id
            GROUP BY i1.id
            ORDER BY createdAt ASC
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['id' => $id]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
