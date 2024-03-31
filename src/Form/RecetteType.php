<?php

namespace App\Form;

use App\Entity\Recette;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RecetteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //should be passed through other controllers
            ->add('idUser', IntegerType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('nom', TextType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('prix', IntegerType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('description', TextareaType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('idIngredients', IntegerType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('idNutrition', IntegerType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('etat', TextType::class, [
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
            'data_class' => Recette::class,
        ]);
    }
}
