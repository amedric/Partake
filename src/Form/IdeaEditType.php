<?php

namespace App\Form;

use App\Entity\Idea;
use App\Entity\Project;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IdeaEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
            ->add('ideaColor', ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'placeholder' => false,
                'choices' => [
                    'Pink' => 'rgb(255, 180, 180)',
                    'Yellow' => 'rgb(255, 238, 83)',
                    'Green' => 'rgb(116, 255, 84)',
                    'Blue' => 'rgb(83, 141, 255)',
                    'Purple' => 'rgb(251, 71, 255)',
                    'Orange' => 'rgb(255, 146, 46)',
                    'Red' => 'rgb(255, 46, 46)',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Idea::class,
        ]);
    }
}
