<?php

namespace App\Form;

use App\Entity\Recette;
use App\Entity\Ingredient; 
use App\Entity\Nutrition; 
use App\Repository\NutritionRepository;
use App\Repository\IngredientRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


class RecetteNutritionType extends AbstractType
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
        ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recette::class,
        ]);
    }

    public function nutritionExists($data ,ExecutionContextInterface $context) : void
    {
        $form = $context->getRoot();
        /** @var App\Entity\Nutrition $formData */
        $formData = $form->getData();

        $nutritionId = $formData->getIdNutrition();

        $nutrition = $this->nutritionRepository->find($nutritionId);
        if(!$nutrition){
            $context->buildViolation('Nutrition with ID {{ id }} does not exist.')
                ->setParameter('{{ id }}', (string)$nutritionId)
                ->atPath('idNutrition')
                ->addViolation();
        }

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
                }else{
                    $idNutrition = $ingredient->getIdNutrition();
                    if($idNutrition){
                        $nutrition = $this->nutritionRepository->find($idNutrition);
                        if(!$nutrition){
                            $context->buildViolation('The nutrition for ingredint {{ id }} does not exist in the database.')
                            ->setParameter('{{ id }}',$id)
                            ->atPath('idIngredients')
                            ->addViolation();
                        }
                    }else{
                        $context->buildViolation('The ingredient {{ id }} does not have a nutrition.')
                        ->setParameter('{{ id }}',$id)
                        ->atPath('idIngredients')
                        ->addViolation();
                    }
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
