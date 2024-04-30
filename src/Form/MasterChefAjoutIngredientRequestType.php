<?php

namespace App\Form;

use App\Entity\Ajoutingredientrequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;

class MasterChefAjoutIngredientRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idChef')
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ajoutingredientrequest::class,
        ]);
    }
}
