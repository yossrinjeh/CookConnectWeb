<?php

namespace App\Form;

use App\Entity\Recette;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecetteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Assuming idUser is set dynamically in the controller
            ->add('nom', TextType::class, [
                'label' => 'Name',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please put Name',
                    ]),
                ],
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Price',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please put Price',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please put Description',
                    ]),
                ],
            ])
            // ingredients, quantites et nutrition dans un autre form 
            //->add('idNutrition', IntegerType::class, [
            //    'label' => 'Nutrition ID',
            //])
            //etat auto assigned to unaactive, will be activated when ingredieents qte et nutrition are assigned.
            //->add('etat', TextType::class, [
            //    'label' => 'State',
            //])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'mapped' => false, // Set to false if you're handling file upload separately
                'required' => false, // Set to true if the image is required
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select an Image',
                    ]),
                    new Image([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image au format JPEG, PNG ou JPG valide.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recette::class,
        ]);
    }
}
