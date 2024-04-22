<?php

namespace App\Form;

use App\Entity\Recette;
use App\Entity\Ingredient; 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class RecetteNutritionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idIngredients',  EntityType::class, [
                'class'=> Ingredient::class,
                'choice_label' => 'id',
                'multiple' => true, 
                'expanded' => true, 
            ])
            ->add('quantiteIngredients',IntegerType::class)
            ->add('idNutrition',IntegerType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recette::class,
        ]);
    }
}
