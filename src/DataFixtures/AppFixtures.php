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
        // $product = new Product();
        // $manager->persist($product);
        //$this->createArticles($manager);
        $this->createAdmin($manager);

        $manager->flush();
    }

    public function createArticles(ObjectManager $manager): void
    {
        $faker = Factory::create('fr/FR');

        for ($i = 0; $i < 20; $i++) {
            $article = (new Article)
                ->setTitle($faker->sentence(random_int(4, 7)))
                ->setContent($faker->paragraph(random_int(3, 6)))
                ->setCreatedAt(new \DateTimeImmutable(sprintf('-%d days', 20 - $i)));
            $manager->persist($article);
        }
    }

    public function createAdmin(ObjectManager $manager): void
    {
        $user = (new User)
            ->setEmail('admin@test.test')
            ->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->hasher->hashPassword($user, '1234'));

        $manager->persist($user);
    }
}
