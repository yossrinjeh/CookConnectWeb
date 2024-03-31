<?php


namespace App\Controller;

use App\Entity\Recette;
use App\Form\RecetteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecetteController extends AbstractController{

    #[Route('Recette/new',name:'new_Recette')]
    public function new(Request $request):Response
    {
        $recette = new Recette();
        $form = $this->createForm(RecetteType::class);
        return $this->render('Recette/new.html.twig',[
            "form" => $form->createView()
        ]);
    }


}