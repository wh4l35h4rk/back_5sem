<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\ProjectGroup;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProjectFixture extends DummyFixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            ProjectGroupFixture::class,
        ];
    }

    public function setData(ObjectManager $manager): void
    {
        $projectGroups = [];
        for ($i = 0; $i < 3; $i++) {
            $projectGroups[] = $this->getReference("project_group_$i", ProjectGroup::class);
        }

        $this->createMany(Project::class, 7, 
            function(Project $project, $count) use ($projectGroups) {
                $project->setName($this->faker->company);
                $project->setCreatedAt($this->faker->dateTime());
                $project->setUpdatedAt($this->faker->dateTime());
                $project->setProjectGroup($this->faker->randomElement($projectGroups));
                $this->addReference("project_$count", $project);
            }
        );
        
        $manager->flush(); 
    }
}
