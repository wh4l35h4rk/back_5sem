<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\Project;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class TaskFixture extends DummyFixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            ProjectGroupFixture::class,
        ];
    }

    public function setData(ObjectManager $manager): void
    {
        $projects = [];
        for ($i = 0; $i < 7; $i++) {
            $projects[] = $this->getReference("project_$i", Project::class);
        }

        $this->createMany(Task::class, 15, 
            function(Task $task, $count) use ($projects) {
                $task->setName($this->faker->word);
                $task->setDescription($this->faker->sentence);
                $task->setCreatedAt($this->faker->dateTime());
                $task->setUpdatedAt($task->getCreatedAt());
                $task->setProject($this->faker->randomElement($projects));
            }
        );
        
        $manager->flush(); 
    }
}
