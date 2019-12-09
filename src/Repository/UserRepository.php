<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
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

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     *
     * @param UserInterface $user
     * @param string $newEncodedPassword
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if ( ! $user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param $identifier
     *
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findUserByIdentifier($identifier)
    {
        $qb = $this
            ->createQueryBuilder('u')
            ->where('u.id = :identifier')
            ->orWhere('u.username = :identifier')
            ->setParameter('identifier', $identifier);

        return $qb->getQuery()->getSingleResult();
    }

    public function findAllPaginated($page, $resourcesPerPage)
    {
        $qb = $this
            ->createQueryBuilder('u')
            ->orderBy('u.id', 'DESC')
            ->getQuery()
            ->setFirstResult(($page - 1) * $resourcesPerPage)
            ->setMaxResults($resourcesPerPage);

        return new Paginator($qb);
    }
}
