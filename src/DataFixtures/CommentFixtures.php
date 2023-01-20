<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixtures extends \Doctrine\Bundle\FixturesBundle\Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $comment = new Comment();
            $comment->setContent($faker->paragraphs(2, true));
            $comment->setCreatedAt($faker->dateTimeBetween('-6 month'));
            $comment->setUser($this->getReference('user_' . $faker->numberBetween(0, 19)));
            $comment->setProject($this->getReference('project_' . $faker->numberBetween(0, 4)));
            $comment->setIdea($this->getReference('idea_' . $faker->numberBetween(0, 4)));
            $comment->setCommentViews(1);
            $this->addReference('comment_' . $i, $comment);
            $manager->persist($comment);
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
