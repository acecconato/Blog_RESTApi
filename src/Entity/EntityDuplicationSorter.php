<?php

namespace App\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class EntityDuplicationSorter
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function handleDuplicatedContent($entity, string $relations)
    {
        switch ($relations) {
            case ('categories'):
                $categoryRepo = $this->manager->getRepository("App:Category");
                foreach ($entity->getCategories() as $category) {
                    if ($tempCategory = $categoryRepo->findOneBy(['label' => $category->getLabel()])) {
                        $entity->removeCategory($category);
                        $entity->addCategory($tempCategory);
                        continue;
                    }
                }
                break;
            case ('tags'):
                break;
            default:
                return;
        }
    }
}
