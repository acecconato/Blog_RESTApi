<?php

namespace App\Controller\Api;

use App\Entity\EntityDuplicationSorter;
use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;

class PostsController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/posts", name="api_get_posts")
     * @Rest\QueryParam(
     *     name="page",
     *     requirements="\d+",
     *     default="1",
     *     description="Page of pagination"
     * )
     * @Rest\View(serializerGroups={"Posts"})
     * @param PostRepository $repository
     * @param ParamFetcher $fetcher
     * @param $resourcesPerPage
     *
     * @return Paginator|RedirectResponse
     * @throws Exception
     */
    public function apiGetPosts(PostRepository $repository, ParamFetcher $fetcher, $resourcesPerPage)
    {
        $page = $fetcher->get('page');
        ($page == 0) ? $page = (string)$page : null;

        $posts = $repository->findAllPaginated($page, $resourcesPerPage);
        if ($posts->getIterator()->count() < 1) {
            return $this->redirectToRoute('api_get_posts');
        }

        return $posts;
    }

    /**
     * @Rest\Get("/posts/{id}", name="api_get_post")
     * @Entity("post", expr="repository.find(id)")
     * @Rest\View(serializerGroups={"Post"})
     *
     * @param Post $post
     *
     * @return Post
     */
    public function apiGetPost(Post $post)
    {
        return $post;
    }

    /**
     * @Rest\Post("/posts", name="api_create_post")
     * @ParamConverter(
     *     "post",
     *     converter="fos_rest.request_body",
     *     options={ "validator"={ "groups"={"Create"} } }
     * )
     * @Rest\View(statusCode=201, serializerGroups={"Post"})
     *
     * @param Post $post
     * @param ConstraintViolationList $violations
     * @param EntityManagerInterface $em
     * @param EntityDuplicationSorter $sorter
     *
     * @return Post|View
     */
    public function apiCreatePost(
        Post $post,
        ConstraintViolationList $violations,
        EntityManagerInterface $em,
        EntityDuplicationSorter $sorter
    ) {
        if (count($violations)) {
            return $this->view($violations, Response::HTTP_BAD_REQUEST);
        }

        // Prevents the creation of a new entity if it already exists.
        $sorter->handleDuplicatedContent($post, 'categories');

        $em->persist($post);
        $em->flush();

        return $post;
    }

    /**
     * @Rest\Put("/posts/{id}", name="api_update_post")
     */
    public function apiUpdatePost()
    {

    }

    /**
     * @Rest\Delete("/posts/{id}", name="api_delete_post")
     * @Rest\View(statusCode=204)
     *
     * @param Post $post
     * @param EntityManagerInterface $em
     */
    public function apiDeletePost(Post $post, EntityManagerInterface $em)
    {
        $em->remove($post);
        $em->flush();
    }
}
