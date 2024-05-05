<?php

namespace App\Controller;

use App\Entity\Nutrition;
use App\Entity\Ingredient;
use App\Entity\Recette;
use App\Form\NutritionRecetteIngredientType;
use App\Form\NutritionType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/nutrition')]
class NutritionController extends AbstractController
{
    #[Route('/', name: 'app_nutrition_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {


        if($this->getUser()  && (in_array('CHEFMASTER', $this->getUser()->getRoles()) || in_array('CHEF', $this->getUser()->getRoles()))){

                $nutrition = $entityManager
                ->getRepository(Nutrition::class)
                ->findAll();
                return $this->render('nutrition/index.html.twig', [
                    'nutrition' => $nutrition,
                ]);
        
                       
        }else{
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }
        
        
    }

    #[Route('/new', name: 'app_nutrition_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,UserRepository $userRepository): Response
    {
        $email = $this->getUser()->getUserIdentifier();
        $user = $userRepository->findOneByEmail($email);
        $id = $user->getId();
        $nutrition = new Nutrition();
        $nutrition->setUserId($id);
        $form = $this->createForm(NutritionType::class, $nutrition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($nutrition);
            $entityManager->flush();

            return $this->redirectToRoute('app_nutrition_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('nutrition/new.html.twig', [
            'nutrition' => $nutrition,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_nutrition_show', methods: ['GET'])]
    public function show(Nutrition $nutrition): Response
    {
        return $this->render('nutrition/show.html.twig', [
            'nutrition' => $nutrition,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_nutrition_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Nutrition $nutrition, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {

        if($this->getUser()  && in_array('CHEFMASTER', $this->getUser()->getRoles())){
            $form = $this->createForm(NutritionType::class, $nutrition);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();

                return $this->redirectToRoute('app_nutrition_index', [], Response::HTTP_SEE_OTHER);
            }
        }elseif($this->getUser()  && in_array('CHEF', $this->getUser()->getRoles())){
            $email = $this->getUser()->getUserIdentifier();
            $user = $userRepository->findOneByEmail($email);
            $id = $user->getId();
            if($nutrition->getUserId()==$id){
                $form = $this->createForm(NutritionType::class, $nutrition);
                $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();

                return $this->redirectToRoute('app_nutrition_index', [], Response::HTTP_SEE_OTHER);
            }
            }else{
                $nutritionData =$entityManager
                ->getRepository(Nutrition::class)
                ->findall();
                $form = $this->createForm(NutritionType::class, $nutrition);
                $form->handleRequest($request);
                $error_message = "You are not authorized to edit this nutrition.";
                return $this->render('nutrition/index.html.twig', [
                'nutrition' => $nutritionData,
                'form' => $form->createView(),
                'error_message' => $error_message,
        ]);
            }
        }else{
            $nutritionData =$entityManager
                ->getRepository(Nutrition::class)
                ->findAll();
                $form = $this->createForm(NutritionType::class, $nutrition);
                $form->handleRequest($request);
                $error_message = "You are not authorized to access this page.";
                return $this->render('nutrition/index.html.twig', [
                'nutrition' => $nutritionData,
                'form' => $form->createView(),
                'error_message' => $error_message,
        ]);
        }
        $form = $this->createForm(NutritionType::class, $nutrition);
        $form->handleRequest($request);
        return $this->renderForm('nutrition/edit.html.twig', [
            'nutrition' => $nutrition,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_nutrition_delete', methods: ['POST'])]
    public function delete(Request $request, Nutrition $nutrition, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        if($this->getUser()  && in_array('CHEFMASTER', $this->getUser()->getRoles())){
            if ($this->isCsrfTokenValid('delete'.$nutrition->getId(), $request->request->get('_token'))) {
                $entityManager->remove($nutrition);
                $entityManager->flush();
            }    
        }elseif($this->getUser()  && in_array('CHEF', $this->getUser()->getRoles())){
            $email = $this->getUser()->getUserIdentifier();
            $user = $userRepository->findOneByEmail($email);
            $id = $user->getId();
            if($nutrition->getId()==$id){
                if ($this->isCsrfTokenValid('delete'.$nutrition->getId(), $request->request->get('_token'))) {
                    $entityManager->remove($nutrition);
                    $entityManager->flush();
                } 
            }else{
                $nutritionData =$entityManager
                ->getRepository(Nutrition::class)
                ->findAll();
                $form = $this->createForm(NutritionType::class, $nutrition);
                $form->handleRequest($request);
                $error_message = "You are not authorized to delete this nutrition.";
                return $this->render('nutrition/index.html.twig', [
                'nutrition' => $nutritionData,
                'form' => $form->createView(),
                'error_message' => $error_message,
        ]);
            }
        }else{
            $nutritionData =$entityManager
                ->getRepository(Nutrition::class)
                ->findAll();
                $form = $this->createForm(NutritionType::class, $nutrition);
                $form->handleRequest($request);
                $error_message = "You are not authorized to access this page.";
                return $this->render('nutrition/index.html.twig', [
                'nutrition' => $nutritionData,
                'form' => $form->createView(),
                'error_message' => $error_message,
        ]);
        }

        if ($this->isCsrfTokenValid('delete'.$nutrition->getId(), $request->request->get('_token'))) {
            $entityManager->remove($nutrition);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_nutrition_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/accordNutrition', name: 'app_nutrition_accordNutrition', methods: ['GET', 'POST'])]
    public function accordNutrition(Request $request, Nutrition $nutrition, EntityManagerInterface $entityManager,UserRepository $userRepository): Response
    {
        if($this->getUser()  && in_array('CHEFMASTER', $this->getUser()->getRoles())){
            $form = $this->createForm(NutritionRecetteIngredientType::class, $nutrition);
            $form->handleRequest($request);
        }elseif($this->getUser()  && in_array('CHEF', $this->getUser()->getRoles())){
            $email = $this->getUser()->getUserIdentifier();
            $user = $userRepository->findOneByEmail($email);
            $id = $user->getId();
            if($nutrition->getId()==$id){
                $form = $this->createForm(NutritionRecetteIngredientType::class, $nutrition);
                $form->handleRequest($request);
            }else{
                $nutritionData =$entityManager
                ->getRepository(Nutrition::class)
                ->findAll();
                $form = $this->createForm(NutritionType::class, $nutrition);
                $form->handleRequest($request);
                $error_message = "You are not authorized to accord nutrition to this this nutrition.";
                return $this->render('nutrition/index.html.twig', [
                'nutrition' => $nutritionData,
                'form' => $form->createView(),
                'error_message' => $error_message,
                ]);
            }
        }else{
            $nutritionData =$entityManager
                ->getRepository(Nutrition::class)
                ->findAll();
                $form = $this->createForm(NutritionType::class, $nutrition);
                $form->handleRequest($request);
                $error_message = "You are not authorized to access  this page.";
                return $this->render('nutrition/index.html.twig', [
                'nutrition' => $nutritionData,
                'form' => $form->createView(),
                'error_message' => $error_message,
                ]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $idIngredient = $nutrition->getIdIngredient();
            $idRecette = $nutrition->getIdRecette();

            if($idIngredient!=null and $idIngredient!=0){
                $ingredient = $entityManager->getRepository(Ingredient::class)->find($idIngredient);
                $ingredient->setIdNutrition($nutrition->getId());
                $entityManager->persist($ingredient);
            }
            if($idRecette!=null and $idRecette!=0){
                $recette = $entityManager->getRepository(Recette::class)->find($idRecette);
                $recette->setIdNutrition($nutrition->getId());
                $entityManager->persist($recette);
            }    
            $entityManager->flush();


            return $this->redirectToRoute('app_nutrition_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('nutrition/nutritionAccord.html.twig', [
            'nutrition' => $nutrition,
            'form' => $form,
        ]);
    }
    
}
