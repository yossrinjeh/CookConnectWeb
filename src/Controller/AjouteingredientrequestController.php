<?php

namespace App\Controller;

use App\Entity\Ajoutingredientrequest;
use App\Form\AjoutingredientrequestType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ajoutingredientrequest')]
class AjoutingredientrequestController extends AbstractController
{
    #[Route('/', name: 'app_ajoutingredientrequest_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $ajoutingredientrequests = $entityManager
            ->getRepository(Ajoutingredientrequest::class)
            ->findAll();

        return $this->render('ajoutingredientrequest/index.html.twig', [
            'ajoutingredientrequests' => $ajoutingredientrequests,
        ]);
    }

    #[Route('/new', name: 'app_ajoutingredientrequest_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ajoutingredientrequest = new Ajoutingredientrequest();
        $form = $this->createForm(AjoutingredientrequestType::class, $ajoutingredientrequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ajoutingredientrequest);
            $entityManager->flush();

            return $this->redirectToRoute('app_ajoutingredientrequest_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ajoutingredientrequest/new.html.twig', [
            'ajoutingredientrequest' => $ajoutingredientrequest,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ajoutingredientrequest_show', methods: ['GET'])]
    public function show(Ajoutingredientrequest $ajoutingredientrequest): Response
    {
        return $this->render('ajoutingredientrequest/show.html.twig', [
            'ajoutingredientrequest' => $ajoutingredientrequest,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ajoutingredientrequest_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ajoutingredientrequest $ajoutingredientrequest, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AjoutingredientrequestType::class, $ajoutingredientrequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ajoutingredientrequest_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ajoutingredientrequest/edit.html.twig', [
            'ajoutingredientrequest' => $ajoutingredientrequest,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ajoutingredientrequest_delete', methods: ['POST'])]
    public function delete(Request $request, Ajoutingredientrequest $ajoutingredientrequest, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ajoutingredientrequest->getId(), $request->request->get('_token'))) {
            $entityManager->remove($ajoutingredientrequest);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ajoutingredientrequest_index', [], Response::HTTP_SEE_OTHER);
    }
}
