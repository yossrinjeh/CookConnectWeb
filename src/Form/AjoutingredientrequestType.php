<?php

namespace App\Form;

use App\Entity\Ajoutingredientrequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjoutingredientrequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idChef')
            ->add('nomIngredient',TextType::class)
            ->add('nomRecette',TextType::class)
            ->add('description',TextareaType::class)
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
