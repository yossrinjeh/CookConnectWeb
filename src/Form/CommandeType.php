<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prix')
            ->add('etat')
            ->add('repas', EntityType::class, [
                'class' => 'App\Entity\Repas', // Assuming the Repas entity class path
                'choice_label' => 'nom', // Assuming 'nom' is the property to display in the select options
                'placeholder' => 'Select a Repas', // Optional placeholder text
                // You can add more options as needed
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
