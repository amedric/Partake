<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends \Doctrine\Bundle\FixturesBundle\Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setEmail($faker->email);
            $user->setPassword(password_hash('1234', PASSWORD_DEFAULT));
            $user->setTeam($faker->randomElement(['Students', 'Management', 'HR', 'Formateurs']));
            $user->setCategory($this->getReference('category_' . $faker->numberBetween(0, 4)));
            $this->addReference('user_' . $i, $user);
            $manager->persist($user);
        }
        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
