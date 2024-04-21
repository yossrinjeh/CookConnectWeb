<?php

namespace App\Form;
use App\Entity\Ingredient;
use App\Entity\Nutrition;
use App\Entity\Recette;
use App\Repository\NutritionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\Image;  

class IngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Remove 'userId' field as it's passed automatically
            // ->add('userId')

            ->add('nom', TextType::class, [
                'label' => 'Name',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please put Name',
                    ]),
                ],
            ])
            ->add('qte', IntegerType::class, [
                'label' => 'Quantity',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please put Quantity',
                    ]),
                ],
            ])
            ->add('prixVente', NumberType::class, [
                'label' => 'Selling Price',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please put Selling Price',
                    ]),
                ],
            ])
            ->add('prixAchat', NumberType::class, [
                'label' => 'Purchase Price',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please put Purchaase Price',
                    ]),
                ],
            ])
            // this will be removed and will be assinged in a new form 
           // ->add('idNutrition', IntegerType::class, [
           //     'label' => 'Nutrition ID',
           //     'constraints' => [
           //         new Callback([$this, 'NutritionIdExists'])
           //     ]
           // ])
            // this is auto assigned at the new form(ingredient/Nutrition forum)
           // ->add('etat', TextType::class, [
           //     'label' => 'State',
           // ])
            ->add('quantiteThreshold', IntegerType::class, [
                'label' => 'Quantity Threshold',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please put Calories',
                    ]),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
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
            'data_class' => Ingredient::class,
        ]);
    }

    public function NutritionIdExists($value, ExecutionContextInterface $context): void
    {
        $nutritionId = $value;
        $entityManager = $context->getObject()->getEntityManager();

        // Check if the nutrition ID exists in the database
        $nutrition = $entityManager->getRepository(Nutrition::class)->find($nutritionId);

        if (!$nutrition) {
            $context->buildViolation('The nutrition {{ id }} does not exist in the database.')
                ->setParameter('{{ id }}',$nutritionId)
                ->atPath('idNutrition')
                ->addViolation();
        }
    }
}
