<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

abstract class DummyFixture extends Fixture
{
    
    private ObjectManager $manager;
    protected Faker\Generator $faker;

    abstract protected function setData(ObjectManager $manager);

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker = Faker\Factory::create();

        $this->setData($manager);
    }

    public function createMany(string $entityName, int $k, callable $fakeData): void
    {
        for ($i = 0; $i < $k; $i++) 
        {
            $entity = new $entityName();
            $fakeData($entity, $i);

            $this->manager->persist($entity);
            $this->manager->flush();
        }
    }
}