<?php

namespace App\Entity\Task;

use App\Repository\TaskPriorityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskPriorityRepository::class)
 */
class TaskPriority
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $ins;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIns(): ?int
    {
        return $this->ins;
    }

    public function setIns(int $ins): self
    {
        $this->ins = $ins;

        return $this;
    }
}
