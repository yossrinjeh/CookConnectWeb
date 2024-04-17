<?php

namespace App\Controller;

use App\Entity\Regime;
use App\Form\RegimeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/regime')]
class RegimeController extends AbstractController
{
    #[Route('/', name: 'app_regime_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $regimes = $entityManager
            ->getRepository(Regime::class)
            ->findAll();

        return $this->render('regime/index.html.twig', [
            'regimes' => $regimes,
        ]);
    }

    #[Route('/new', name: 'app_regime_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $regime = new Regime();
        $form = $this->createForm(RegimeType::class, $regime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($regime);
            $entityManager->flush();

            return $this->redirectToRoute('app_regime_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('regime/new.html.twig', [
            'regime' => $regime,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_regime_show', methods: ['GET'])]
    public function show(Regime $regime): Response
    {
        return $this->render('regime/show.html.twig', [
            'regime' => $regime,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_regime_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, $id, EntityManagerInterface $entityManager): Response
    {
        $regime = $entityManager->getRepository(regime::class)->find($id);
        if (!$regime) {
            throw $this->createNotFoundException('The regime does not exist');
        }
    
        $form = $this->createForm(regimeType::class, $regime);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
    
            return $this->redirectToRoute('app_regime_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('regime/edit.html.twig', [
            'regime' => $regime,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_regime_delete', methods: ['POST'])]
    public function delete(Request $request, $id, EntityManagerInterface $entityManager): Response
    {
        $regime = $entityManager->getRepository(regime::class)->find($id);
    
        if (!$regime) {
            throw $this->createNotFoundException('regime not found');
        }
    
        if ($this->isCsrfTokenValid('delete'.$regime->getId(), $request->request->get('_token'))) {
            $entityManager->remove($regime);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_regime_index');
    }
}
