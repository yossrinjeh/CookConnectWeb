<?php

namespace App\Form;

use App\Entity\Nutrition;
use App\Entity\Ingredient;
use App\Entity\Recette;
use App\Repository\IngredientRepository;
use App\Repository\RecetteRepository;
use App\Repository\NutritionRepository;
use SebastianBergmann\Environment\Console;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class NutritionType extends AbstractType
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
            // Remove 'userId' field from the form
            // ->add('userId', IntegerType::class, [
            //     'mapped' => false,
            //     'data' => 2,
            //     'label' => 'User ID',
            // ])

            ->add('idIngredient', IntegerType::class, [
                'label' => 'Ingredient ID',
                'required' => false, 
                'constraints' => [
                    new Callback([$this, 'IngredientOrRecetteExists']),
                ],
            ])
            ->add('idRecette', IntegerType::class, [
                'label' => 'Recipe ID',
                'required' => false, 
                'constraints' => [
                    new Callback([$this, 'IngredientOrRecetteExists']),
                ],
            ])
            ->add('calories', NumberType::class, [
                'label' => 'Calories',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please put Calories',
                    ]),
                ],
            ])
            ->add('carbs', NumberType::class, [
                'label' => 'Carbohydrates',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please put Carbohydrates',
                    ]),
                ],
            ])
            ->add('fat', NumberType::class, [
                'label' => 'Fat',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please put Fat',
                    ]),
                ],
            ])
            ->add('fiber', NumberType::class, [
                'label' => 'Fiber',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please put Fiber',
                    ]),
                ],
            ])
            ->add('protein', NumberType::class, [
                'label' => 'Protein',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please put Protein',
                    ]),
                ],
            ])
            ->add('vitamines', TextType::class, [
                'label' => 'Vitamins',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please choose vitamines',
                    ]),
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


    public function IngredientOrRecetteExists($data, ExecutionContextInterface $context): void
{
    $nutritionId = $data;
    dump($data);
    $recetteId = $this->recetteRepository->findOneBy(['idNutrition' => $nutritionId]);
    
    $ingredientId = $this->ingredientRepository->findOneBy(['idNutrition' => $nutritionId]);
    


    if ($ingredientId && $recetteId) {
        $context->buildViolation('Choose either Ingredient ID or Recipe ID, not both.')
            ->atPath('idIngredient')
            ->addViolation();
    }

    if ($ingredientId) {
        $ingredient = $this->ingredientRepository->find($ingredientId);
        if (!$ingredient) {
            $context->buildViolation('Ingredient with ID {{ id }} does not exist.')
                ->setParameter('{{ id }}', (string)$ingredientId)
                ->atPath('idIngredient')
                ->addViolation();
        }
    }

    if ($recetteId) {
        $recette = $this->recetteRepository->find($recetteId);
        if (!$recette) {
            $context->buildViolation('Recipe with ID {{ id }} does not exist.')
                ->setParameter('{{ id }}', (string)$recetteId)
                ->atPath('idRecette')
                ->addViolation();
        }
    }
}
}
