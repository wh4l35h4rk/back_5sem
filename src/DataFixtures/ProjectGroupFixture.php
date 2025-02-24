<?php

namespace App\DataFixtures;

use App\Entity\ProjectGroup;
use Doctrine\Persistence\ObjectManager;

class ProjectGroupFixture extends DummyFixture
{
    public function setData(ObjectManager $manager): void
    {
        $this->createMany(ProjectGroup::class, 3, 
            function(ProjectGroup $projectGroup, $count) {
                $projectGroup->setName($this->faker->word);
                $projectGroup->setCreatedAt($this->faker->dateTime());
                $projectGroup->setUpdatedAt($this->faker->dateTime());
            }
        );
    }
}
