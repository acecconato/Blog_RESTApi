<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    /**
     * @Rest\Get("/categories", name="api_get_categories")
     * @Rest\View(serializerGroups={"Categories"})
     * @param CategoryRepository $repository
     *
     * @return Category[]
     */
    public function apiGetCategories(CategoryRepository $repository)
    {
        return $repository->findAll();
    }

    /**
     * @Rest\Get("/categories/{id}", name="api_get_category", requirements={"\d+"})
     * @Rest\View(serializerGroups={"Category"})
     *
     * @param Category $category
     *
     * @return Category
     */
    public function apiGetCategory(Category $category)
    {
        return $category;
    }

    public function apiCreateCategory()
    {

    }

    public function apiUpdateCategory()
    {

    }

    public function apiDeleteCategory()
    {

    }
}
