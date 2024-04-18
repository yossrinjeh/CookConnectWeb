<?php

namespace App\Form;

use App\Entity\Poste;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\File;

class PosteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', null, [
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a user']),
                ],
            ])
            ->add('titre', null, [
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a title']),
                    new Length(['min' => 5, 'max' => 255, 'minMessage' => 'Title must be at least {{ limit }} characters long']),
                ],
            ])
            ->add('description', null, [
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a description']),
                    new Length(['min' => 10, 'minMessage' => 'Description must be at least {{ limit }} characters long']),
                ],
            ])
            ->add('image', null, [
                'constraints' => [
                    new Url(['message' => 'Please enter a valid URL for the image']),
                ],
            ])
            ->add('video', null, [
                'constraints' => [
                    new Url(['message' => 'Please enter a valid URL for the video']),
                ],
            ])
            ->add('date', null, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a date']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Poste::class,
        ]);
    }
}
