<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(private readonly UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $this->createArticles($manager);
        $this->createAdmin($manager);

        $manager->flush();
    }

    public function createArticles(ObjectManager $manager): void
    {
        $faker = Factory::create('fr/FR');

        for ($i = 0; $i < 20; $i++) {

            $content = [
                "time" => 1748812168824,
                "blocks" => [
                    "id" => $this->randomId(),
                    "type" => "paragraph",
                    "data" => [
                        "text" => $faker->sentence(random_int(4, 7))
                    ],
                ],
                [
                    "id" => $this->randomId(),
                    "type" => "paragraph",
                    "data" => [
                        "text" => $faker->sentence(random_int(4, 7))
                    ]
                ],
                [
                    "id" => $this->randomId(),
                    "type" => "paragraph",
                    "data" => [
                        "text" => "Publié depuis Postman"
                    ]
                ],
                "version" => "2.31.0-rc.7"
            ];


            $article = (new Article)
                ->setTitle($faker->sentence(random_int(4, 7)))
                ->setContent($content)
                ->setCreatedAt(new \DateTimeImmutable(sprintf('-%d days', 20 - $i)));
            $manager->persist($article);
        }
    }

    public function createAdmin(ObjectManager $manager): void
    {
        $user = (new User)
            ->setEmail('admin@test.test')
            ->setName('admin')
            ->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->hasher->hashPassword($user, '1234'));

        $manager->persist($user);
    }

    private function randomId($length = 10): string
    {
        return substr(str_replace(['/', '+', '='], '', base64_encode(random_bytes($length))), 0, $length);
    }
}
