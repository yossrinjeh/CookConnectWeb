<?php

namespace App\Form;

use App\Entity\Ingredient;
use App\Repository\NutritionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class IngredientNutritionType extends AbstractType
{

    private $nutritionRpository;

    public function __construct(NutritionRepository $nutritionRepository)
    {
        $this->nutritionRpository = $nutritionRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('idNutrition', IntegerType::class, [
                'label' => 'Ingredient ID',
                'required' => false, 
                'constraints' => [
                    new Callback([$this, 'nutritionExist']),
                ],
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ingredient::class,
        ]);
    }

    public function nutritionExist($data, ExecutionContextInterface $context): void
    {
        $nutritionId = $data;
        $nutrition = $this->nutritionRpository->find($nutritionId);

        if(!$nutrition){
            $context->buildViolation('Ingredient with ID {{ id }} does not exist.')
                ->setParameter('{{ id }}', (string)$nutritionId)
                ->atPath('idIngredient')
                ->addViolation();
        }
    }
}
