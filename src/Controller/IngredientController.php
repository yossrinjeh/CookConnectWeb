<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController{

    #[Route('/Ingredient/new', name:'new_Ingredient')]
    public function new(Request $request):Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class);
        return $this-> render('Ingredient/new.html.twig',[
            "form" => $form->createView()
        ]);
    }


}