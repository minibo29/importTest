<?php

namespace App\Entity;

use App\Entity\Task\TaskPriority;
use App\Entity\User;
use App\Entity\Task\TaskStatus;
use App\Entity\Task\TaskType;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{

    /**
     * @ORM\Column(name="strTaskName", type="string", length=50)
     * @Assert\NotBlank
     */
    private $title;

    /**
     * @ORM\Column(name="strTaskDesc", type="string", length=255)
     * @Assert\NotBlank
     */
    private $desc;

    /**
     * @ORM\Column(name="dtmScheduleTime", type="datetime", nullable=true)
     */
    private $scheduleTime;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=TaskPriority::class, cascade={"persist", "remove"})
     */
    private $priority;

    /**
     * @ORM\OneToOne(targetEntity=TaskType::class, cascade={"persist", "remove"})
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=TaskStatus::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $status;

    /**
     * @ORM\ManyToMany(targetEntity=User::class)
     * @JoinTable(name="users_task",
     *      joinColumns={@JoinColumn(name="task_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="user_id", referencedColumnName="id", unique=true)}
     * )
     */
    private $user;

    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPriority(): ?TaskPriority
    {
        return $this->priority;
    }

    public function setPriority(?TaskPriority $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getType(): ?TaskType
    {
        return $this->type;
    }

    public function setType(?TaskType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus(): ?TaskStatus
    {
        return $this->status;
    }

    public function setStatus(?TaskStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

}
