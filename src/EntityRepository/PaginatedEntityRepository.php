<?php


namespace App\EntityRepository;


use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class PaginatedEntityRepository extends EntityRepository implements ServiceEnti
{
    public function __construct(ManagerRegistry $registry, $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    public function findAllPaginated($page, $resourcesPerPage)
    {
        return $this->findBy(['id'], ['id' => 'ASC'], $resourcesPerPage, ($page - 1) * $resourcesPerPage);
    }
}
