<?php

namespace App\Controller;

use App\Entity\Nutrition;
use App\Form\NutritionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NutritionController extends AbstractController{

    #[Route('/Nutrition/new', name:'new_Nutrition')]
    public function new(Request $request):Response
    {
        $nutrition = new Nutrition();
        $form = $this->createForm(NutritionType::class);
        return $this->render('Nutrition/new.html.twig',[
            "form" => $form->createView()
        ]);

    }

}