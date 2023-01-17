<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Project1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
            ->add('createdAt')
            ->add('projectViews')
            ->add('projectColor')
            ->add('category', null, ['choice_label' => 'title'])
            ->add('usersSelectOnProject', EntityType::class, [
                'class' => User::class,
                'required' => true,
                'multiple' => true,
                'choice_label' => function (User $user) {
                    return $user->getFullName();
                },
                'group_by' => function (User $user) {
                    return $user->getTeam();
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
