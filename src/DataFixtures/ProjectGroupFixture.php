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
                $projectGroup->setUpdatedAt($projectGroup->getCreatedAt());
                $this->addReference("project_group_$count", $projectGroup);
            }
        );
        
        $manager->flush(); 
    }
}
