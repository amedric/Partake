<?php

namespace App\DataFixtures;

use App\Entity\Idea;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class IdeaFixtures extends \Doctrine\Bundle\FixturesBundle\Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $idea = new Idea();
            $idea->setContent($faker->paragraphs(2, true));
            $idea->setCreatedAt($faker->dateTimeBetween('-6 month'));
            $idea->setUser($this->getReference('user_' . $faker->numberBetween(0, 19)));
            $idea->setProject($this->getReference('project_' . $faker->numberBetween(0, 4)));
            $idea->setIdeaViews(1);
            $this->addReference('idea_' . $i, $idea);
            $manager->persist($idea);
        }
        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ProjectFixtures::class,
        ];
    }
}
