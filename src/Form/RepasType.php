<?php

namespace App\Form;
use App\Entity\User;
use App\Entity\Recette;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Repas;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
class RepasType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('recette', EntityType::class, [
            'class' => Recette::class,
            'choice_label' => 'id', // Assuming 'id' is the property you want to display in the dropdown
            'placeholder' => 'Select Recette', // Optional placeholder
            'constraints' => [
                new NotBlank([
                    'message' => 'Please select a recette',
                ]),
            ],
        ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id', // Or any other property of User you want to display
                'placeholder' => 'Select User',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select a user',
                    ]),
                ],
            ])
            ->add('nom',null , ['constraints' => [
                new NotBlank([
                    'message' => 'Please enter an name',
                ]),new Assert\Type([
                    'type' => 'string',
                    'message' => 'The role field must be a string.'
                ])
            ]])
            ->add('type',null , ['constraints' => [
                new NotBlank([
                    'message' => 'Please enter an type',
                ]),
            ]])
            ->add('tags',null , ['constraints' => [
                new NotBlank([
                    'message' => 'Please enter an tags',
                ]),new Assert\Type([
                    'type' => 'string',
                    'message' => 'The role field must be a string.'
                ]),
                new Length([
                    'min' => 8,
                    'minMessage' => 'Your tags should be at least {{ limit }} characters',
                   
                    'max' => 4096,
                ]),
            ]])
            ->add('email',null , ['constraints' => [
                new NotBlank([
                    'message' => 'Please enter an email',
                ]),
            ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => [
                'novalidate' => 'novalidate', // comment me to reactivate the html5 validation!  🚥
            ],
            'data_class' => Repas::class,
        ]);
    }
}
