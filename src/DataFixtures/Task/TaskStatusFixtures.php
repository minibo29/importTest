<?php

namespace App\DataFixtures\Task;

use App\Entity\Task\TaskStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TaskStatusFixtures extends Fixture
{

    private array $data = [
        [
            'id' => 1,
            'name' => 'progresses',
            'label' => 'In progress',
        ],
        [
            'id' => 2,
            'name' => 'done',
            'label' => 'Done',
        ],
        [
            'id' => 3,
            'name' => 'refused',
            'label' => 'Refused',
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach ($this->data as $data){
            $taskStatus = new TaskStatus();
            foreach ($data as $key => $value) {
                $methodName = 'set' . ucfirst($key);
                $taskStatus->{$methodName}($value);
            }
            $manager->persist($taskStatus);
        }

        $manager->flush();
    }

}