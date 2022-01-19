<?php

namespace App\DataFixtures\Task;

use App\Entity\Task\TaskPriority;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TaskPriorityFixtures extends Fixture
{

    private array $data = [
        [
            'id' => 1,
            'name' => 'high',
            'label' => 'High',
        ],
        [
            'id' => 2,
            'name' => 'medium',
            'label' => 'Medium',
        ],
        [
            'id' => 3,
            'name' => 'low',
            'label' => 'Low',
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach ($this->data as $data){
            $taskPriority = new TaskPriority();
            foreach ($data as $key => $value) {
                $methodName = 'set' . ucfirst($key);
                $taskPriority->{$methodName}($value);
            }
            $manager->persist($taskPriority);
        }

        $manager->flush();
    }

}