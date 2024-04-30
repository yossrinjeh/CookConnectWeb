<?php

namespace App\Form;

use App\Entity\Nutrition;
use App\Entity\PlanAlim;
use App\Entity\User;
use App\Entity\Regime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PlanAlimType extends AbstractType
{
   

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nutrition', EntityType::class, [
                'class' => Nutrition::class,
                'choice_label' => 'id', // Assuming 'id' is the property you want to display in the dropdown
                'placeholder' => 'Select Nutrition', // Optional placeholder
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select a nutrition',
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
            ->add('regime', EntityType::class, [
                'class' => Regime::class,
                'choice_label' => 'id', // Assuming 'id' is the property you want to display in the dropdown
                'placeholder' => 'Select regime', // Optional placeholder
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select a regime',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => [
                'novalidate' => 'novalidate', // comment me to reactivate the html5 validation!  🚥
            ],
            'data_class' => PlanAlim::class,
        ]);
    }
}
