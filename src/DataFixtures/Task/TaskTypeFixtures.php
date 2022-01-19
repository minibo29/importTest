<?php

namespace App\DataFixtures\Task;

use App\Entity\Task\TaskType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TaskTypeFixtures extends Fixture
{

    private array $data = [
        [
            'id' => 1,
            'name' => 'feature',
            'label' => 'Feature',
        ],
        [
            'id' => 2,
            'name' => 'bug',
            'label' => 'Bug',
        ],
        [
            'id' => 3,
            'name' => 'investigation',
            'label' => 'Investigation',
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach ($this->data as $data){
            $taskType = new TaskType();
            foreach ($data as $key => $value) {
                $methodName = 'set' . ucfirst($key);
                $taskType->{$methodName}($value);
            }
            $manager->persist($taskType);
        }

        $manager->flush();
    }

}