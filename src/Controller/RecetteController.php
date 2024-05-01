<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Form\RecetteNutritionType;
use App\Form\RecetteType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NutritionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/recette')]
class RecetteController extends AbstractController
{
    #[Route('/', name: 'app_recette_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {

        //block acess
        if ($this->getUser()  && in_array('CHEFMASTER', $this->getUser()->getRoles())){
            
            $recettes = $entityManager
                ->getRepository(Recette::class)
                ->findAll();

            return $this->render('recette/index.html.twig', [
                'recettes' => $recettes,
            ]);
            }elseif($this->getUser()  && in_array('CHEF', $this->getUser()->getRoles())){

                $email = $this->getUser()->getUserIdentifier();
                $user = $userRepository->findOneByEmail($email);
                $id = $user->getId();
                $recettes = $entityManager
                ->getRepository(Recette::class)
                ->findBy(['idUser' => $id]);

                return $this->render('recette/index.html.twig', [
                    'recettes' => $recettes,
            ]);
            // else access denied message
        }else{
        return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
    }
    }

    #[Route('/new', name: 'app_recette_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,UserRepository $userRepository): Response
    {
        $email = $this->getUser()->getUserIdentifier();
        $user = $userRepository->findOneByEmail($email);
        $id = $user->getId();   
        $recette = new Recette();
        $recette->setEtat("desactivé");
        $recette->setIdUser($id);
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($recette);
            $entityManager->flush();

            return $this->redirectToRoute('app_recette_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recette/new.html.twig', [
            'recette' => $recette,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recette_show', methods: ['GET'])]
    public function show(Recette $recette): Response
    {
        return $this->render('recette/show.html.twig', [
            'recette' => $recette,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_recette_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recette $recette, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {

        if ($this->getUser()  && in_array('CHEFMASTER', $this->getUser()->getRoles())){
            $form = $this->createForm(RecetteType::class, $recette);
            $form->handleRequest($request);
        }elseif($this->getUser()  && in_array('CHEF', $this->getUser()->getRoles())){
            $email = $this->getUser()->getUserIdentifier();
            $user = $userRepository->findOneByEmail($email);
            $id = $user->getId(); 
            if($id == $recette->getIdUser()){
                $form = $this->createForm(RecetteType::class, $recette);
                $form->handleRequest($request);
            }else{
                $recetteData =$entityManager
                ->getRepository(Recette::class)
                ->findBy(['idUser' => $id]);
                $form = $this->createForm(RecetteType::class, $recette);
                $form->handleRequest($request);
                $error_message = "You are not authorized to edit this recipe.";
                return $this->render('recette/index.html.twig', [
                'recette' => $recetteData,
                'form' => $form->createView(),
                'error_message' => $error_message,
        ]);
            }
        }else{
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_recette_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recette/edit.html.twig', [
            'recette' => $recette,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recette_delete', methods: ['POST'])]
    public function delete(Request $request, Recette $recette, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {


        if($this->getUser()  && in_array('CHEFMASTER', $this->getUser()->getRoles())){
            if ($this->isCsrfTokenValid('delete'.$recette->getId(), $request->request->get('_token'))) {
                $entityManager->remove($recette);
                $entityManager->flush();
            }
        }elseif($this->getUser()  && in_array('CHEF', $this->getUser()->getRoles())){
            $email = $this->getUser()->getUserIdentifier();
            $user = $userRepository->findOneByEmail($email);
            $id = $user->getId();
            if($id==$recette->getIdUser()){
                if ($this->isCsrfTokenValid('delete'.$recette->getId(), $request->request->get('_token'))) {
                    $entityManager->remove($recette);
                    $entityManager->flush();
                }
            }else{
                $recetteData =$entityManager
                ->getRepository(Recette::class)
                ->findBy(['idUser' => $id]);
                $form = $this->createForm(RecetteType::class, $recette);
                $form->handleRequest($request);
                $error_message = "You are not authorized to delette this recipe.";
                return $this->render('recette/index.html.twig', [
                'recette' => $recetteData,
                'form' => $form->createView(),
                'error_message' => $error_message,
                ]);
            }
        }else{
            return $this->redirectToRoute('app_recette_index', [], Response::HTTP_SEE_OTHER);
        }

        
    }

    #[Route('/{id}/accordNutrition', name: 'app_recette_accordNutrition', methods: ['GET', 'POST'])]
    public function accordNutrition(Request $request, Recette $recette, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecetteNutritionType::class, $recette);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_recette_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recette/accordNutrition.html.twig', [
            'recette' => $recette,
            'form' => $form,
        ]);
    }
}
