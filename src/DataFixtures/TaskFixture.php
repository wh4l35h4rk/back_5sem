<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Persistence\ObjectManager;

class TaskFixture extends DummyFixture
{
    public function setData(ObjectManager $manager): void
    {
        $this->createMany(Task::class, 15, 
            function(Task $task, $count) {
                $task->setName($this->faker->word);
                $task->setDescription($this->faker->sentence);
                $task->setCreatedAt($this->faker->dateTime());
                $task->setUpdatedAt($this->faker->dateTime());
            }
        );
    }
}
