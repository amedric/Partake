<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('email', TextType::class)
            ->add('password', PasswordType::class)
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'placeholder' => 'Choose department',
                'required' => true,
                'choice_label' => 'title',
            ])
            ->add('birthday', DateType::Class, array(
                'widget' => 'choice',
                'years' => range(date('Y'), date('Y') - 100),
                'months' => range(date('m'), 12),
                'days' => range(date('d'), 31),
            ));
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
