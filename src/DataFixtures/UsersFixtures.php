<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class UsersFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        $roles = [
            'ROLE_ADMIN',
            'ROLE_MODERATOR',
            'ROLE_AUTHOR',
            'ROLE_SUBSCRIBER',
            'ROLE_USER',
        ];

        $allUsers = [];
        for ($i = 0; $i < 40; $i++) {
            $user = new User();
            $user
                ->setUsername($faker->userName)
                ->setPassword($faker->password)
                ->setRoles([$roles[rand(0, count($roles) - 1)]]);

            $manager->persist($user);
            $allUsers[] = $user;
        }

        foreach ($allUsers as $user) {
            $hasPosts = rand(0, 1);
            if (rand(0, 1)) {
                for ($i = 0; $i < rand(1, 6); $i++) {
                    $post = new Post();
                    $post
                        ->setTitle($faker->title)
                        ->setContent($faker->text)
                        ->setSlug($faker->slug)
                        ->setCreatedAt(new \DateTime())
                        ->setUser($user);

                    $manager->persist($post);
                }
            }
        }

        $manager->flush();
    }
}
