<?php

namespace App\Entity\Task;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\Task;
use Doctrine\ORM\Mapping\Table;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_task")
 */
class UserTask
{
    const ASSIGN_ROLE = 2;
    const AUTHOR_ROLE = 1;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userTask")
     * @ORM\JoinColumn(name="user", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Task::class, inversedBy="userTask")
     * @ORM\JoinColumn(name="task", referencedColumnName="id", nullable=false)
     */
    private $task;

    /**
     * @ORM\Column(type="integer")
     */
    private $userRole;

    public function getId()
    {
        return $this->id;
    }

    public function getUser(): ?int
    {
        return $this->user;
    }

    public function setUser(int $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTask(): ?int
    {
        return $this->task;
    }

    public function setTask(int $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function getUserRole(): ?string
    {
        return $this->userRole;
    }

    public function setUserRole(string $userRole): self
    {
        $this->userRole = $userRole;

        return $this;
    }
}
