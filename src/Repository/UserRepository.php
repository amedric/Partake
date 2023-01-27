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
     * @param $id
     * @param $column
     * @param $orderBy
     * @param $wherePara
     * @return array
     * @throws Exception
     * retireves all projects and ideas into one table
     * can be filtered by projects and ideas
     */
    public function findProjectsIdeasForUser($id, $column, $orderBy, $wherePara): array
    {
        $orderByPara = $column . " " . $orderBy;
        $conn = $this->getEntityManager()->getConnection();
        // query to find data for projects and ideas
        $sql = "
                select * from
                (select
                    'project' as dataType,
                    p.id as id,
                    p.title as title,
                    p.project_views as views,
                    (select count(i2.project_id)
                        FROM idea as i2
                        where i2.project_id = p.id
                        group by p.id) as counts,
                    c1.category_color as color,
                    '0' as 'likes',
                    '0' as liked,
                    p.created_at as createdAt
                    from project as p
                    left join category as c1 on p.category_id = c1.id
                    left join idea as i on p.id = i.project_id
                    where p.user_id = :id
                union
                select 'idea' as dataType,
                        i3.id as id,
                        i3.title as title,
                        i3.idea_views as views,
                        (select count(c2.id)
                            FROM comment as c2
                            where c2.idea_id = i3.id) as counts,
                        i3.idea_color as color,
                        (select count(l.id)
                            FROM `like` as l
                            where l.idea_id = i3.id) as 'likes',
                        (select count(l2.user_id)
                            FROM `like` as l2
                            where l2.idea_id = i3.id and l2.user_id = :id) as liked,
                        i3.created_at as createdAt
                from idea as i3
                where i3.user_id = :id
                order by " . $orderByPara . ") as allData
                where allData.dataType = " . $wherePara
            ;

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['id' => $id]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }
}
