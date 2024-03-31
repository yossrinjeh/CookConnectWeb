<?php

namespace App\Form;

use App\Entity\Nutrition;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NutritionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        //should be passed through other controllers
            ->add('userId', IntegerType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('idIngredient', TextType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('idRecette', TextType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('calories', TextType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('carbs', TextType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('fat', TextType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('fiber', TextType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('protein', TextType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('vitamines', TextType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('image', FileType::class, [
                "attr" => [
                    "class" => "form-control-file"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Nutrition::class,
        ]);
    }
}
