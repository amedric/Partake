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
            $idea->setTitle($faker->sentence(6));
            $idea->setContent($faker->paragraphs(2, true));
            $idea->setIdeaColor($faker->randomElement([
                'rgb(255, 180, 180)',
                'rgb(255, 238, 83)',
                'rgb(116, 255, 84)',
                'rgb(83, 141, 255)',
                'rgb(251, 71, 255)',
                'rgb(255, 146, 46)',
                'rgb(255, 46, 46)']));
            $idea->setCreatedAt($faker->dateTimeBetween('-6 month'));
            $idea->setUser($this->getReference('user_' . $faker->numberBetween(0, 19)));
            $idea->setProject($this->getReference('project_' . $faker->numberBetween(0, 19)));
            $idea->setIdeaViews(20);
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
