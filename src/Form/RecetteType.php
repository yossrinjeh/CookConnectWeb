<?php

namespace App\Form;

use App\Entity\Recette;
use Symfony\Component\Form\AbstractType;
use App\Repository\NutritionRepository;
use App\Repository\IngredientRepository;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RecetteType extends AbstractType
{


    private $nutritionRepository;
    private $ingredientRepository;

    public function __construct(NutritionRepository $nutritionRepository,IngredientRepository $ingredientRepository)
    {
        $this->ingredientRepository = $ingredientRepository;
        $this->nutritionRepository = $nutritionRepository;
    }


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
            //ingredients, quantites et nutrition dans un autre form 
            
            
            
            /*->add('idNutrition', IntegerType::class, [
                'label' => 'Nutrition ID',
            ])*/
            //etat auto assigned to unaactive, will be activated when ingredieents qte et nutrition are assigned.
            /*->add('etat', TextType::class, [
                'label' => 'State',
            ])*/
            ->add('idIngredients', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter Ingredients\' IDs',
                    ]),
                    new Callback([$this, 'validateIngredients']),
                    new Callback([$this, 'validateSameNumberElements']),
                ],
            ])
            ->add('quantiteIngredients', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter Quantities',
                    ]),
                    new Callback([$this, 'validateQuantities']),
                    new Callback([$this, 'validateSameNumberElements']),
                ],
            ])
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


    public function validateIngredients($value, ExecutionContextInterface $context): void
    {
        $ids = explode(',', $value);
        foreach ($ids as $id) {
            if (!is_numeric($id)) {
                $context->buildViolation('Invalid Ingredient ID: {{ id }}')
                    ->setParameter('{{ id }}', $id)
                    ->addViolation();
            }else{
                $ingredient = $this->ingredientRepository->find($id);
                if (!$ingredient) {
                    $context->buildViolation('The ingredient {{ id }} does not exist in the database.')
                        ->setParameter('{{ id }}',$id)
                        ->atPath('idIngredients')
                        ->addViolation();
                }
            }
        }

    }

    public function validateQuantities($value, ExecutionContextInterface $context): void
    {
        $quantities = explode(',', $value);
        foreach ($quantities as $quantity) {
            if (!is_numeric($quantity)) {
                $context->buildViolation('Invalid Quantity: {{ quantity }}')
                    ->setParameter('{{ quantity }}', $quantity)
                    ->addViolation();
            }
        }
    }

    public function validateSameNumberElements($value, ExecutionContextInterface $context): void
    {
        // This method checks if the number of elements in idIngredients matches quantiteIngredients
        $formData = $context->getRoot()->getData();
        $idIngredients = $formData->getIdIngredients();
        $quantiteIngredients = $formData->getQuantiteIngredients();

        $idCount = count(explode(',', $idIngredients));
        $quantiteCount = count(explode(',', $quantiteIngredients));

        if ($idCount !== $quantiteCount) {
            $context->buildViolation('The number of Ingredients must match the number of Quantities.')
                ->atPath('idIngredients')
                ->addViolation();
        }
    }

}
