<?php

namespace App\Serializer;

use Doctrine\Common\DataFixtures\Exception\CircularReferenceException;

class CircularReferenceHandler
{
    public function __invoke($object)
    {
        if (method_exists($object, 'getId')) {
            return $object->getId();
        }

        throw new CircularReferenceException(__CLASS__." can't handle the circular reference.");
    }
}
