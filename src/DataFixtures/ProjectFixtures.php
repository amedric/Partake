<?php

namespace App\DataFixtures;

use App\Entity\Project;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProjectFixtures extends \Doctrine\Bundle\FixturesBundle\Fixture implements DependentFixtureInterface
{
    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $project = new Project();
            $project->setTitle($faker->sentence(6));
            $project->setContent($faker->paragraphs(2, true));
            $project->setCreatedAt($faker->dateTimeBetween('-6 month'));
            $project->setUser($this->getReference('user_' . $faker->numberBetween(0, 19)));
            $project->setCategory($this->getReference('category_' . $faker->numberBetween(0, 4)));
            $this->addReference('project_' . $i, $project);
            $manager->persist($project);
        }
        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
        ];
    }
}
