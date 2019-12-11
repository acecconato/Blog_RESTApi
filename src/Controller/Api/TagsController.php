<?php

namespace App\Controller\Api;

use App\Entity\Tag;
use App\Repository\TagRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TagsController extends AbstractController
{
    /**
     * @Rest\Get("/tags", name="api_get_tags")
     * @Rest\View(serializerGroups={"Tags"})
     * @param TagRepository $repository
     *
     * @return Tag[]
     */
    public function apiGetCategories(TagRepository $repository)
    {
        return $repository->findAll();
    }

    /**
     * @Rest\Get("/tags/{id}", name="api_get_tag", requirements={"\d+"})
     * @Rest\View(serializerGroups={"Tag"})
     *
     * @param Tag $tag
     *
     * @return Tag
     */
    public function apiGetCategory(Tag $tag)
    {
        return $tag;
    }
}
