<?php

namespace App\Controller\Api;

use App\Entity\EntityMerger;
use App\Entity\User;
use App\Repository\UserRepository;
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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UsersController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/users", name="api_get_users")
     * @Rest\QueryParam(
     *     name="page",
     *     requirements="\d+",
     *     default="1",
     *     description="Page of pagination"
     * )
     * @Rest\View(serializerGroups={"List"})
     *
     * @param UserRepository $repository
     * @param ParamFetcher $fetcher
     * @param $resourcesPerPage
     *
     * @return Paginator|RedirectResponse
     * @throws Exception
     */
    public function apiGetUsers(UserRepository $repository, ParamFetcher $fetcher, $resourcesPerPage)
    {
        $page = $fetcher->get('page');
        ($page == 0) ? $page = (string)$page : null;

        $users = $repository->findAllPaginated($page, $resourcesPerPage);
        if ($users->getIterator()->count() < 1) {
            return $this->redirectToRoute('api_get_users');
        }

        return $users;
    }

    /**
     * @Rest\Get("/users/{identifier<^[\d|\w]+$>}", name="api_get_user")
     * @Rest\View(serializerGroups={"User"})
     * @Entity("user", expr="repository.findUserByIdentifier(identifier)")
     *
     * @param User $user
     *
     * @return User
     */
    public function apiGetUser(User $user)
    {
        return $user;
    }

    /**
     * @Rest\Post("/users", name="api_create_user")
     * @ParamConverter(
     *     "user",
     *     converter="fos_rest.request_body",
     *     options={ "validator"={ "groups"={"Create"} } }
     * )
     * @Rest\View(statusCode=201)
     *
     * @param User $user
     * @param ConstraintViolationList $violations
     * @param EntityManagerInterface $em
     *
     * @return User|View
     */
    public function apiCreateUser(User $user, ConstraintViolationList $violations, EntityManagerInterface $em)
    {
        if (count($violations)) {
            return $this->view($violations, Response::HTTP_BAD_REQUEST);
        }

        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * TODO:: Remove identifier for simple id
     * @Rest\Put("/users/{identifier<^[\d|\w]+$>}", name="api_update_put_user")
     * @Rest\Patch("/users/{identifier<^[\d|\w]+$>}", name="api_update_patch_user")
     * @Entity("user", expr="repository.findUserByIdentifier(identifier)")
     * @ParamConverter(
     *     "newUser",
     *     class="App\Entity\User",
     *     converter="fos_rest.request_body"
     * )
     * @Rest\View(statusCode=202)
     *
     * @param User $user
     * @param $newUser User
     * @param ValidatorInterface $validator
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $em
     * @param EntityMerger $merger
     * @param Request $request
     *
     * @return View|void
     */
    public function apiUpdateUser(
        User $user,
        $newUser,
        ValidatorInterface $validator,
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $em,
        EntityMerger $merger,
        Request $request
    ) {
        if ($request->isMethod('PATCH')) {
            $errors = $validator->validate($newUser, null, ['Update']);
            if (count($errors)) {
                return $this->view($errors);
            }

            if ($merger->hasChanged($user, $newUser)) {
                $merger->merge($user, $newUser);
            }
        }

        if ($request->isMethod('PUT')) {
            $errors = $validator->validate($newUser, null, ['Create']);
            if (count($errors)) {
                return $this->view($errors);
            }

            $merger->merge($user, $newUser);
        }

        $em->flush();
    }

    /**
     * @Rest\Delete("/users/{identifier<^[\d|\w]+$>}", name="api_delete_user")
     * @Entity("user", expr="repository.findUserByIdentifier(identifier)")
     * @Rest\View(204)
     *
     * @param User $user
     * @param EntityManagerInterface $em
     */
    public function apiDeleteUSer(User $user, EntityManagerInterface $em)
    {
        $em->remove($user);
        $em->flush();
    }
}
