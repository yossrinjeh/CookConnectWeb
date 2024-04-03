<?php

namespace App\Controller;

use App\Entity\InformationPersonnele;
use App\Form\InformationPersonneleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/information/personnele')]
class InformationPersonneleController extends AbstractController
{
    #[Route('/', name: 'app_information_personnele_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $informationPersonneles = $entityManager
            ->getRepository(InformationPersonnele::class)
            ->findAll();

        return $this->render('information_personnele/index.html.twig', [
            'information_personneles' => $informationPersonneles,
        ]);
    }

    #[Route('/new', name: 'app_information_personnele_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $informationPersonnele = new InformationPersonnele();
        $form = $this->createForm(InformationPersonneleType::class, $informationPersonnele);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($informationPersonnele);
            $entityManager->flush();

            return $this->redirectToRoute('app_information_personnele_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('information_personnele/new.html.twig', [
            'information_personnele' => $informationPersonnele,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_information_personnele_show', methods: ['GET'])]
    public function show(int $id,EntityManagerInterface $entityManager): Response
    {
        $informationPersonnele = $entityManager->getRepository(InformationPersonnele::class)->find($id);

        return $this->render('information_personnele/show.html.twig', [
            'information_personnele' => $informationPersonnele,
        ]);
    }
    /*
    #[Route('/{id}/edit', name: 'app_information_personnele_edit', methods: ['GET', 'POST'])]
    public function edit(int $id,Request $request, EntityManagerInterface $entityManager): Response
    {   
        $informationPersonnele = $entityManager->getRepository(InformationPersonnele::class)->find($id);

        $form = $this->createForm(InformationPersonneleType::class, $informationPersonnele);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_information_personnele_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('information_personnele/edit.html.twig', [
            'information_personnele' => $informationPersonnele,
            'form' => $form,
        ]);
    }
*/


#[Route('/{id}/edit', name: 'app_information_personnele_edit', methods: ['GET', 'POST'])]
public function edit(int $id, Request $request, EntityManagerInterface $entityManager , UserRepository $userRepository): Response
{   
    $informationPersonnele = $entityManager->getRepository(InformationPersonnele::class)->find($id);

    // Get the associated user entity
   
        if($this->getUser()){
            $user = $informationPersonnele->getUser();
            if($this->getUser()->getUsername() != $user->getUsername()){
              //  throw new AccessDeniedHttpException('Access denied.');
                return $this->renderForm('frontOffice/403.html.twig', [
                   
                ]);
            }
        }else{
            return $this->redirectToRoute('app_login');
        }
  
   

    // Create a form for both entities
    $form = $this->createForm(InformationPersonneleType::class, $informationPersonnele);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Update user information
        $userFormData = $form->get('user')->getData();
        $user->setEmail($userFormData->getEmail()); // Assuming email is editable
        // You can add more setters for other user fields here
        
        // Update personal information
        $entityManager->persist($informationPersonnele);
        $entityManager->flush();

        return $this->redirectToRoute('app_information_personnele_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('information_personnele/edit.html.twig', [
        'information_personnele' => $informationPersonnele,
        'form' => $form,
    ]);
}

    #[Route('/{id}', name: 'app_information_personnele_delete', methods: ['POST'])]
    public function delete(Request $request,int $id, EntityManagerInterface $entityManager): Response
    {
        $informationPersonnele = $entityManager->getRepository(InformationPersonnele::class)->find($id);

        if ($this->isCsrfTokenValid('delete'.$informationPersonnele->getId(), $request->request->get('_token'))) {
            $entityManager->remove($informationPersonnele);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_information_personnele_index', [], Response::HTTP_SEE_OTHER);
    }
}
