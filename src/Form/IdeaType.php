<?php

namespace App\Form;

use App\Entity\Idea;
use App\Entity\User;
use App\Entity\Project;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IdeaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
            ->add('ideaColor', ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'Pink' => 'rgb(255, 180, 180)',
                    'Yellow' => 'rgb(255, 238, 83)',
                    'Green' => 'rgb(116, 255, 84)',
                    'Blue' => 'rgb(83, 141, 255)',
                    'Purple' => 'rgb(251, 71, 255)',
                    'Orange' => 'rgb(255, 146, 46)',
                    'Red' => 'rgb(255, 46, 46)',
                ]
            ])
            ->add('user', IntegerType::class)
            ->add('project', IntegerType::class)
//            ->add('user', EntityType::class, [
//                'class' => User::class,
//                'choice_label' => 'id',
//            ])
//            ->add('project', EntityType::class, [
//                'class' => Project::class,
//                'choice_label' => 'id',
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Idea::class,
        ]);
    }
}
