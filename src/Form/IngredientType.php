<?php

namespace App\Form;

use App\Entity\Ingredient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class IngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('userId', IntegerType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('nom', TextType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('qte', IntegerType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('prixVente', IntegerType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('prixAchat', IntegerType::class, [
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
            ->add('quantiteThreshold', IntegerType::class, [
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
            'data_class' => Ingredient::class,
        ]);
    }
}
