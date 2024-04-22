<?php

namespace App\Form;

use App\Entity\Recette;
use App\Entity\Ingredient; 
use App\Entity\Nutrition; 
use App\Repository\NutritionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RecetteNutritionType extends AbstractType
{

    private $nutritionRepository;

    public function __construct(NutritionRepository $nutritionRepository)
    {
        $this->nutritionRepository= $nutritionRepository;
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idIngredients',  EntityType::class, [
                'class'=> Ingredient::class,
                'choice_label' => 'id',
                'multiple' => true, 
                'expanded' => true, 
            ])
            ->add('idIngredients',  TextType::class)
            ->add('quantiteIngredients',TextType::class)
            ->add('idNutrition',IntegerType::class,[
                'constraints' =>[
                    new Callback([$this, 'nutritionExists'])
                ]
            ])
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
}
