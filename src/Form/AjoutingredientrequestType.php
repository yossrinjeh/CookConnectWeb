<?php

namespace App\Form;

use App\Entity\Ajoutingredientrequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AjoutingredientrequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /*->add('idChef', TextType::class, [
                'label' => 'Chef ID',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter Chef ID',
                    ]),
                ],
            ])*/
            ->add('nomIngredient', TextType::class, [
                'label' => 'Ingredient Name',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter Ingredient Name',
                    ]),
                ],
            ])
            ->add('nomRecette', TextType::class, [
                'label' => 'Recipe Name',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter Recipe Name',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter Description',
                    ]),
                ],
            ])
            ->add('etat', ChoiceType::class, [
                'label' => 'State',
                'choices' => [
                    'Denied' => 'Denied',
                    'Pending' => 'Pending',
                    'Approved' => 'Approved',
                ],
                'expanded' => true, // Show as checkboxes
                'multiple' => false, // Allow only one selection
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please choose a State',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ajoutingredientrequest::class,
        ]);
    }
}
