<?php

namespace App\Form;

use App\Entity\Nutrition;
use App\Repository\IngredientRepository;
use App\Repository\RecetteRepository;
use App\Repository\NutritionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\IsNull;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class NutritionRecetteIngredientType extends AbstractType
{
    private $recetteRepository;
    private $ingredientRepository;
    private $nutritionRepository;

    public function __construct(RecetteRepository $recetteRepository, IngredientRepository $ingredientRepository, NutritionRepository $nutritionRepository)
    {
        $this->recetteRepository = $recetteRepository;
        $this->ingredientRepository = $ingredientRepository;
        $this->nutritionRepository= $nutritionRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('idIngredient', IntegerType::class, [
            'label' => 'Ingredient ID',
            'required' => false, 
            'constraints' => [
                new Callback([$this, 'IngredientOrRecetteExists']),
                new Callback([$this, 'IngredientExists']),
            ],
        ])
        ->add('idRecette', IntegerType::class, [
            'label' => 'Recipe ID',
            'required' => false, 
            'constraints' => [
                new Callback([$this, 'IngredientOrRecetteExists']),
                new Callback([$this, 'RecetteExists']),
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Nutrition::class,
        ]);
    }

    public function IngredientOrRecetteExists($value, ExecutionContextInterface $context): void
{
    $form = $context->getRoot();
    /** @var App\Entity\Nutrition $formData */
    $formData = $form->getData();
    
    $recetteId = $formData->getIdRecette();
    $ingredientId = $formData->getIdIngredient();
    


    if (($ingredientId!=null || $ingredientId!=0) && ($recetteId!=null || $recetteId!=0)) {
        $context->buildViolation('Choose either Ingredient ID or Recipe ID, not both.')
            ->atPath('idIngredient')
            ->addViolation();
    }

    /*if ($ingredientId!=null or $ingredientId!=0) {
        $ingredient = $this->ingredientRepository->find($ingredientId);
        if (!$ingredient) {
            $context->buildViolation('Ingredient with ID {{ id }} does not exist.')
                ->setParameter('{{ id }}', (string)$ingredientId)
                ->atPath('idIngredient')
                ->addViolation();
            
        }
    }

    if ($recetteId!=null or $recetteId!=0) {
        $recette = $this->recetteRepository->find($recetteId);
        if (!$recette) {
            $context->buildViolation('Recipe with ID {{ id }} does not exist.')
                ->setParameter('{{ id }}', (string)$recetteId)
                ->atPath('idRecette')
                ->addViolation();
        }
    }*/
    }
    public function RecetteExists($value, ExecutionContextInterface $context): void
    {
        $form = $context->getRoot();
        /** @var App\Entity\Nutrition $formData */
        $formData = $form->getData();
    
        $recetteId = $formData->getIdRecette();

        $recette = $this->recetteRepository->find($recetteId);
        if (!$recette) {
            $context->buildViolation('Recipe with ID {{ id }} does not exist.')
                ->setParameter('{{ id }}', (string)$recetteId)
                ->atPath('idRecette')
                ->addViolation();
        }
    }
    public function IngredientExists($value, ExecutionContextInterface $context): void
    {
        $form = $context->getRoot();
        /** @var App\Entity\Nutrition $formData */
        $formData = $form->getData();
    
        $ingredientId = $formData->getIdIngredient();

        $ingredient = $this->ingredientRepository->find($ingredientId);
        if (!$ingredient) {
            $context->buildViolation('Ingredient with ID {{ id }} does not exist.')
                ->setParameter('{{ id }}', (string)$ingredientId)
                ->atPath('idIngredient')
                ->addViolation();
            
        }
    }
}
