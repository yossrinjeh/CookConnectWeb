<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Nutrition;
use App\Form\IngredientType;
use App\Form\IngredientNutritionType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ingredient')]
class IngredientController extends AbstractController
{
    #[Route('/', name: 'app_ingredient_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        if($this->getUser()  && (in_array('CHEFMASTER', $this->getUser()->getRoles()) || in_array('CHEF', $this->getUser()->getRoles()))){
                
            $ingredients = $entityManager
            ->getRepository(Ingredient::class)
            ->findAll();

            return $this->render('ingredient/index.html.twig', [
                'ingredients' => $ingredients,
        ]);
        }else{
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        
    }


    #[Route('/new', name: 'app_ingredient_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,UserRepository $userRepository): Response
    {
        $email = $this->getUser()->getUserIdentifier();
        $user = $userRepository->findOneByEmail($email);
        $id = $user->getId();
        $ingredient = new Ingredient();
        $ingredient->setUserId($id);
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $image */
            $image = $form['image']->getData();

            if ($image) {
                $fileName = uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move($this->getParameter('image_dir'), $fileName); // Move the uploaded file to the configured directory
                } catch (FileException $e) {
                    // Handle file exception
                    // You might want to log the error or show an error message to the user
                    return new Response('Failed to upload the image.', Response::HTTP_INTERNAL_SERVER_ERROR);
                }

                $ingredient->setImage($fileName);
            }

            
            if($ingredient->getQte()<$ingredient->getQuantiteThreshold()){
                $ingredient->setEtat("disabled");
            }else{
                $ingredient->setEtat("enabled");
            }
            $entityManager->persist($ingredient);
            $entityManager->flush();

            return $this->redirectToRoute('app_ingredient_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ingredient/new.html.twig', [
            'ingredient' => $ingredient,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ingredient_show', methods: ['GET'])]
    public function show(Ingredient $ingredient): Response
    {
        return $this->render('ingredient/show.html.twig', [
            'ingredient' => $ingredient,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ingredient_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ingredient $ingredient, EntityManagerInterface $entityManager,UserRepository $userRepository): Response
    {

        if($this->getUser()  && (in_array('CHEFMASTER', $this->getUser()->getRoles()))){
            $form = $this->createForm(IngredientType::class, $ingredient);
            $form->handleRequest($request);
        }elseif($this->getUser()  && (in_array('CHEF', $this->getUser()->getRoles()))){
            $email = $this->getUser()->getUserIdentifier();
            $user = $userRepository->findOneByEmail($email);
            $id = $user->getId();
            if($id == $ingredient->getUserId()){
                $form = $this->createForm(IngredientType::class, $ingredient);
                $form->handleRequest($request);
            }else{
                $ingredientData =$entityManager
                ->getRepository(Ingredient::class)
                ->findall();
                $form = $this->createForm(IngredientType::class, $ingredient);
                $form->handleRequest($request);
                $error_message = "You are not authorized to edit this ingredient.";
                return $this->render('ingredient/index.html.twig', [
                'ingredients' => $ingredientData,
                'form' => $form->createView(),
                'error_message' => $error_message,
        ]);
            }
        }else{
            return $this->redirectToRoute('app_login_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient->setImage((string)($ingredient->getImage()));
            if($ingredient->getQte()<$ingredient->getQuantiteThreshold()){
                $ingredient->setEtat("disabled");
            }else{
                $ingredient->setEtat("enabled");
            }
            $entityManager->flush();


            return $this->redirectToRoute('app_ingredient_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ingredient/edit.html.twig', [
            'ingredient' => $ingredient,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ingredient_delete', methods: ['POST'])]
    public function delete(Request $request, Ingredient $ingredient, EntityManagerInterface $entityManager,UserRepository $userRepository): Response
    {

        if($this->getUser()  && (in_array('CHEFMASTER', $this->getUser()->getRoles()))){
            if ($this->isCsrfTokenValid('delete'.$ingredient->getId(), $request->request->get('_token'))) {
                $entityManager->remove($ingredient);
                $entityManager->flush();
            }
    
            return $this->redirectToRoute('app_ingredient_index', [], Response::HTTP_SEE_OTHER);
        }elseif($this->getUser()  && (in_array('CHEF', $this->getUser()->getRoles()))){
            $email = $this->getUser()->getUserIdentifier();
            $user = $userRepository->findOneByEmail($email);
            $id = $user->getId();
            if($id == $ingredient->getUserId()){
                if ($this->isCsrfTokenValid('delete'.$ingredient->getId(), $request->request->get('_token'))) {
                    $entityManager->remove($ingredient);
                    $entityManager->flush();
                }
        
                return $this->redirectToRoute('app_ingredient_index', [], Response::HTTP_SEE_OTHER);
            }else{
                $ingredientData =$entityManager
                ->getRepository(Ingredient::class)
                ->findall();
                $form = $this->createForm(IngredientType::class, $ingredient);
                $form->handleRequest($request);
                $error_message = "You are not authorized to delete this ingredient.";
                return $this->render('ingredient/index.html.twig', [
                'ingredients' => $ingredientData,
                'form' => $form->createView(),
                'error_message' => $error_message,
                ]);
            }
        }else{
            return $this->redirectToRoute('app_login_index', [], Response::HTTP_SEE_OTHER);
        }
        
    }

    #[Route('/{id}/accordNutrition', name: 'app_ingredient_nutrition_accord', methods:['GET','POST'])]
    public function nutritionAccord(Request $request, Ingredient $ingredient, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(IngredientNutritionType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $idNutrition = $ingredient->getIdNutrition();
            $nutrition = $entityManager->getRepository(Nutrition::class)->find($idNutrition);
            $nutrition->setIdIngredient($ingredient->getId());
            $entityManager->persist($nutrition);
            $entityManager->flush();
            return $this->redirectToRoute('app_ingredient_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ingredient/ingredientNutrition.html.twig', [
            'ingredient' => $ingredient,
            'form' => $form,
        ]);
    }

}
