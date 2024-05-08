<?php

namespace App\Form;

use App\Entity\Recette;
use App\Repository\NutritionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;

class RecetteAccordNutritionType extends AbstractType
{


    private $nutritionRpository;

    public function __construct(NutritionRepository $nutritionRepository)
    {
        $this->nutritionRpository = $nutritionRepository;
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idNutrition', IntegerType::class,[
                'label' => 'Nutrition ID', 
                'constraints' => [
                    new NotBlank(['message'=>'this field is required',]),
                    new Callback([$this, 'nutritionExist']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recette::class,
        ]);
    }

    public function nutritionExist($data, ExecutionContextInterface $context): void
    {
        $formData = $context->getRoot()->getData();
        $nutritionId = $formData->getIdNutrition();

        $nutrition = $this->nutritionRpository->find($nutritionId);

        if($nutrition){
            if(!$nutrition){
                $context->buildViolation('Nutrition with ID {{ id }} does not exist.')
                    ->setParameter('{{ id }}', (string)$nutritionId)
                    ->atPath('idIngredient')
                    ->addViolation();   
            }
        }else{
            $context->buildViolation('This field is required.')
                    ->atPath('idIngredient')
                    ->addViolation();
        }
    }
}
