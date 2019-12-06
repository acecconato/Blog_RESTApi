<?php

namespace App\Entity;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EntityMerger
{
    /**
     * @var Reader
     */
    private $reader;

    private $encoder;

    public function __construct(Reader $reader, UserPasswordEncoderInterface $encoder)
    {
        $this->reader  = $reader;
        $this->encoder = $encoder;
    }

    public function merge($entity, $changes)
    {
        $this->validate($entity, $changes);

        $entityRefl  = new \ReflectionObject($entity);
        $changesRefl = new \ReflectionObject($changes);

        foreach ($changesRefl->getProperties() as $changedProperty) {
            $changedProperty->setAccessible(true);
            $changedPropertyValue = $changedProperty->getValue($changes);

            if (null === $changedPropertyValue) {
                continue;
            }

            if ( ! $entityRefl->hasProperty($changedProperty->getName())) {
                continue;
            }

            $entityProperty = $entityRefl->getProperty($changedProperty->getName());
            $annotation     = $this->reader->getPropertyAnnotation($entityProperty, Id::class);

            if (null !== $annotation) {
                continue;
            }

            $entityProperty->setAccessible(true);
            $password = ($entityProperty->getName() === 'password') ? $entityProperty->getValue($entity) : null;
            if ($password !== null) {
                $entityProperty->setValue(
                    $entity,
                    $this->encoder->encodePassword($entity, $changedPropertyValue)
                );
                continue;
            }

            $entityProperty->setValue($entity, $changedPropertyValue);
        }
    }

    public function hasChanged($entity, $changes)
    {
        $reader = $this->reader;
        $this->validate($entity, $changes);

        $entityRefl  = new \ReflectionObject($entity);
        $changesRefl = new \ReflectionObject($changes);

        foreach ($changesRefl->getProperties() as $changedProperty) {
            $entityReflProperty = $entityRefl->getProperty($changedProperty->getName());
            $annotation         = $reader->getPropertyAnnotation($entityReflProperty, Id::class);

            if ($annotation !== null) {
                continue;
            }

            $entityReflProperty->setAccessible(true);
            $changedProperty->setAccessible(true);
            $defaultValue = $entityReflProperty->getValue($entity);
            $newValue     = $changedProperty->getValue($changes);

            if (null === $newValue) {
                continue;
            }

            if (gettype($newValue) === 'object') {
                continue;
            }

            if (is_array($newValue) && count($newValue) < 1) {
                continue;
            }


            if ($defaultValue !== $newValue) {
                return true;
            }
        }

        return false;
    }

    protected function validate($entity, $changes)
    {
        if ( ! get_class($entity) || ! get_class($changes)) {
            throw new \InvalidArgumentException("$entity or $changes is not an object");
        }

        $entityClassName = get_class($entity);

        if ( ! is_a($changes, $entityClassName)) {
            throw new \InvalidArgumentException("$changes is not an object of class $entityClassName");
        }
    }
}
