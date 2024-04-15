<?php

namespace App\Form;

use App\Entity\Nutrition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NutritionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('userId',IntegerType::class,[
                'mapped' => false,
                'data' => 2
            ])
            ->add('idIngredient',IntegerType::class,[

            ])
            ->add('idRecette',IntegerType::class)
            ->add('calories',NumberType::class)
            ->add('carbs',NumberType::class)
            ->add('fat',NumberType::class)
            ->add('fiber',NumberType::class)
            ->add('protein',NumberType::class)
            ->add('vitamines')
            ->add('image')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Nutrition::class,
        ]);
    }
}
