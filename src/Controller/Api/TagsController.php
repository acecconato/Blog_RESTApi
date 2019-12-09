<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TagsController extends AbstractController
{
    /**
     * @Route("/tags", name="tags")
     */
    public function index()
    {
        return $this->render('tags/index.html.twig', [
            'controller_name' => 'TagsController',
        ]);
    }
}
