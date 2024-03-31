<?php

namespace App\Controller;

use App\Entity\Ajoutingredientrequest;
use App\Form\AjoutingredientrequestType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AjoutingredientrequestController extends AbstractController{

    #[Route('/Ajoutingredientrequest/new', name:'new_Ajoutingredientrequest')]
    public function new(Request $request):Response
    {
        $ajout = new Ajoutingredientrequest();
        $form = $this->createForm(AjoutingredientrequestType::class);
        return $this->render('Ajoutingredientrequest/new.html.twig',[
            "form" => $form->createView()
        ]);
    }



}