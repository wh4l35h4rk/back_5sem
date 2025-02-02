<?php

namespace App\DataFixtures;

use App\Entity\Project;
use Doctrine\Persistence\ObjectManager;

class ProjectFixture extends DummyFixture
{
    public function setData(ObjectManager $manager): void
    {
        $this->createMany(Project::class, 7, 
            function(Project $project, $count) {
                $project->setName($this->faker->company);
                $project->setCreatedAt($this->faker->dateTime());
                $project->setUpdatedAt($this->faker->dateTime());
            }
        );
    }
}
