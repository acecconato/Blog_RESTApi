<?php

namespace App\Controller\Api;

use App\Repository\CategoryRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    /**
     * @Rest\Get("/categories", name="api_get_categories")
     * @Rest\QueryParam(
     *     name="page",
     *     requirements="\d+",
     *     default=1,
     *     description="Page of pagination"
     * )
     * @param CategoryRepository $repository
     * @param ParamFetcher $fetcher
     * @param $resourcesPerPage
     */
    public function apiGetCategories(CategoryRepository $repository, ParamFetcher $fetcher, $resourcesPerPage)
    {
        $page = $fetcher->get('page');
        $categories = $repository->findAllPaginated($page, $resourcesPerPage);
    }

    public function apiGetCategory()
    {

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
