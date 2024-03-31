<?php

namespace App\Form;

use App\Entity\Ajoutingredientrequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjoutingredientrequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idChef')
            ->add('nomIngredient')
            ->add('nomRecette')
            ->add('description')
            ->add('etat')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ajoutingredientrequest::class,
        ]);
    }
}
