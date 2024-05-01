<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddUserByAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email' ,null,[
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter an email',
                ]),
            ],
        ])
        ->add('role', ChoiceType::class, [
            'choices' => [
                'CHEFMASTER' => 'CHEFMASTER',
                'NUTRITIONIST' => 'NUTRITIONIST',
            ],
            'placeholder' => 'Select Type', // Optional placeholder text
            'attr' => [
                'class' => 'form-control' , // Add custom CSS class
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please select a Type',
                ]),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => [
                'novalidate' => 'novalidate', 
            ],
            'data_class' => User::class,
        ]);
    }
}
