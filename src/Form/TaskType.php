<?php

namespace App\Form;

use App\Entity\Task;
use App\Entity\Task\TaskPriority as Priority;
use App\Entity\Task\TaskStatus as Status;
use App\Entity\Task\TaskType as Type;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('desc')
            ->add('scheduleTime')
            ->add('priority', EntityType::class, [
                'class' => Priority::class,
                'choice_label' => 'label',
            ])
            ->add('type', EntityType::class, [
                'class' => Type::class,
                'choice_label' => 'label',
            ])
            ->add('status', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'label',
            ])
//            ->add('user', EntityType::class, [
//                'class' => User::class,
//                'choice_label' => 'email',
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
