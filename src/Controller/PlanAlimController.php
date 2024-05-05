<?php

namespace App\Controller;

use App\Entity\PlanAlim;
use App\Form\PlanAlimType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/plan/alim')]
class PlanAlimController extends AbstractController
{
    #[Route('/', name: 'app_plan_alim_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $planAlims = $entityManager
            ->getRepository(PlanAlim::class)
            ->findAll();

        return $this->render('plan_alim/index.html.twig', [
            'plan_alims' => $planAlims,
        ]);
    }

    #[Route('/new', name: 'app_plan_alim_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $planAlim = new PlanAlim();
        $form = $this->createForm(PlanAlimType::class, $planAlim);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($planAlim);
            $entityManager->flush();

            return $this->redirectToRoute('app_plan_alim_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('plan_alim/new.html.twig', [
            'plan_alim' => $planAlim,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_plan_alim_show', methods: ['GET'])]
    public function show(PlanAlim $plan_alim): Response
    {
        return $this->render('plan_alim/show.html.twig', [
            'plan_alim' => $plan_alim,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_plan_alim_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, $id, EntityManagerInterface $entityManager): Response
    {
        $plan_alim = $entityManager->getRepository(PlanAlimType::class)->find($id);
        if (!$plan_alim) {
            throw $this->createNotFoundException('The plan_alim does not exist');
        }
    
        $form = $this->createForm(PlanAlimType::class, $plan_alim);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
    
            return $this->redirectToRoute('app_plan_alim_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('plan_alim/edit.html.twig', [
            'plan_alim' => $plan_alim,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_plan_alim_delete', methods: ['POST'])]
    public function delete(Request $request, $id, EntityManagerInterface $entityManager): Response
    {
        $plan_alim = $entityManager->getRepository(PlanAlimType::class)->find($id);
    
        if (!$plan_alim) {
            throw $this->createNotFoundException('plan_alim not found');
        }
    
        if ($this->isCsrfTokenValid('delete'.$plan_alim->getId(), $request->request->get('_token'))) {
            $entityManager->remove($plan_alim);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_plan_alim_index');
    }
}
