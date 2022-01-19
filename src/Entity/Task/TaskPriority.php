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
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param self $id
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
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

    /**
     * @return string
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param self $label
     */
    public function setLabel($label): self
    {
        $this->label = $label;
        return $this;
    }

    /** @see \Serializable::serialize() */
    public function serialize(): string
    {
        return serialize(array(
            $this->id,
            $this->name,
            $this->label,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->name,
            $this->label,
            ) = unserialize($serialized, array('allowed_classes' => false));
    }

}
