<?php

namespace App\Form;

use App\Entity\Nutrition;
use App\Entity\Ingredient;
use App\Entity\Recette;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class NutritionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Remove 'userId' field from the form
            // ->add('userId', IntegerType::class, [
            //     'mapped' => false,
            //     'data' => 2,
            //     'label' => 'User ID',
            // ])

            ->add('idIngredient', IntegerType::class, [
                'label' => 'Ingredient ID',
                'required' => false, // Make it optional
                'constraints' => [
                    new Callback([$this, 'IngredientsAndRecetteInput']),
                    new Callback([$this, 'IngredientOrRecetteExists']),
                ],
            ])
            ->add('idRecette', IntegerType::class, [
                'label' => 'Recipe ID',
                'required' => false, // Make it optional
                'constraints' => [
                    new Callback([$this, 'IngredientsAndRecetteInput']),
                    new Callback([$this, 'IngredientOrRecetteExists']),
                ],
            ])
            ->add('calories', NumberType::class, [
                'label' => 'Calories',
            ])
            ->add('carbs', NumberType::class, [
                'label' => 'Carbohydrates',
            ])
            ->add('fat', NumberType::class, [
                'label' => 'Fat',
            ])
            ->add('fiber', NumberType::class, [
                'label' => 'Fiber',
            ])
            ->add('protein', NumberType::class, [
                'label' => 'Protein',
            ])
            ->add('vitamines', ChoiceType::class, [
                'label' => 'Vitamins',
                'choices' => [
                    'A' => 'A',
                    'B' => 'B',
                    'C' => 'C',
                    'D' => 'D',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Nutrition::class,
        ]);
    }



    public function IngredientsAndRecetteInput($data, ExecutionContextInterface $context): void
{
    $ingredientId = $context->getObject()->getIdIngredient();
    $recipeId = $context->getObject()->getIdRecette();

    if (($ingredientId && $recipeId) || (!$ingredientId && !$recipeId)) {
        $context->buildViolation('Choose either Ingredient ID or Recipe ID, not both.')
            ->atPath('idIngredient')
            ->addViolation();
    }
}

    public function IngredientOrRecetteExists($data, ExecutionContextInterface $context): void
{
    $entityManager = $context->getObject()->getEntityManager();
    $ingredientId = $context->getObject()->getIdIngredient();
    $recipeId = $context->getObject()->getIdRecette();

    if ($ingredientId) {
        $ingredient = $entityManager->getRepository(Ingredient::class)->find($ingredientId);
        if (!$ingredient) {
            $context->buildViolation('Ingredient with ID {{ id }} does not exist.')
                ->setParameter('{{ id }}', $ingredientId)
                ->atPath('idIngredient')
                ->addViolation();
        }
    }

    if ($recipeId) {
        $recipe = $entityManager->getRepository(Recette::class)->find($recipeId);
        if (!$recipe) {
            $context->buildViolation('Recipe with ID {{ id }} does not exist.')
                ->setParameter('{{ id }}', $recipeId)
                ->atPath('idRecette')
                ->addViolation();
        }
    }
}
}
