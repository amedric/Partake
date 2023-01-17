<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', textType::class)
            ->add('categoryColor', ChoiceType::class, [
                'placeholder' => 'Choose Team color',
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
