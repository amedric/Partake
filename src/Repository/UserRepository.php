<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function findProjectsIdeasForUser(int $id): array
    {

        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            select "project" as projectOrIdea,
                   p.id as id,
                   p.title as title,
                   p.project_views as views,
                   count(i.id) as counts,
                    c1.category_color as color,
                   p.created_at as createdAt,
                    "likes"
            from project as p
                left join category as c1 on p.category_id = c1.id
            left join idea as i on p.id = i.project_id
            where p.user_id = :id
            group by p.id
            UNION
            SELECT "idea",
                   i1.id,
                   i1.title,
                   i1.idea_views,
                   count(c2.idea_id),
                   i1.idea_color,
                   i1.created_at,
                   count(l.idea_ID) as likes
            FROM idea as i1
                left join `like` as l on i1.id = l.idea_id
            left join comment as c2 on i1.id = c2.idea_id
            where i1.user_id = :id
            group by i1.id
            order by createdAt ASC
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
