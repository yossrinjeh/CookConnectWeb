<?php

namespace App\Controller;
use App\Repository\RepasRepository;
use App\Entity\Repas;
use App\Form\RepasType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
#[Route('/repas')]
class RepasController extends AbstractController
{
    #[Route('/', name: 'app_repas_index', methods: ['GET'])]
    public function index(RepasRepository $repasRepository): Response
    {
        // Fetch all Repas entities using selectAllRepas method
        $repas = $repasRepository->selectAllRepas();

        // Render the template and pass the repas data to it
        return $this->render('frontOffice/base.html.twig', [
            'repas' => $repas,
        ]);
    }

    #[Route('/new', name: 'app_repas_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $repas = new Repas();
        $form = $this->createForm(RepasType::class, $repas);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($repas);
            $entityManager->flush();

            return $this->redirectToRoute('app_repas_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('repas/new.html.twig', [
            'repas' => $repas,
            'form' => $form,
        ]);
    }
    #[Route('/backrechercheAjax', name: 'backrechercheAjax')]
    public function searchAjax(Request $request, RepasRepository $repo)
    {
    // Récupérez le paramètre de recherche depuis la requête
    $query = $request->query->get('q');
    // Récupérez les plats correspondants depuis la base de données
    $repas = $repo->findrepasByNom($query);
    $html = $this->renderView("repas/index.html.twig", [
    "repas" => $repas,
    ]);
    return new Response($html);
}
#[Route('/pdf', name: 'PDF_Seance',methods: ['GET'])]
    public function pdf(repasRepository $RepasRepository)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
    
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('repas/PDF.html.twig', [
            'repas' => $RepasRepository->findAll(),
        ]);
    
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        // Setup the paper size and orientation
        $dompdf->setPaper('A3', 'portrait');
    
        // Render the HTML as PDF
        $dompdf->render();
    
        // Generate PDF file content
        $output = $dompdf->output();
    
        // Write file to the temporary directory
        $pdfFilepath = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($pdfFilepath, $output);
    
        // Return the PDF as a response
        return new BinaryFileResponse($pdfFilepath);
    }

    #[Route('/{id}', name: 'app_repas_show', methods: ['GET'])]
    public function show($id, EntityManagerInterface $entityManager): Response
    {
        $repas = $entityManager->getRepository(Repas::class)->find($id);
    
        if (!$repas) {
            throw $this->createNotFoundException('Repas not found');
        }
    
        // Add code to fetch related Recette entity
        $recette = $repas->getRecette();
    
        // Check if Recette entity exists
        if (!$recette) {
            
            throw $this->createNotFoundException('Recette not found');
        }
    
        return $this->render('repas/show.html.twig', [
            'repas' => $repas,
            'recette' => $recette, // Pass the Recette entity to the template
        ]);
    }
    
    #[Route('/{id}/edit', name: 'app_repas_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, $id, EntityManagerInterface $entityManager): Response
{
    $repas = $entityManager->getRepository(Repas::class)->find($id);
    if (!$repas) {
        throw $this->createNotFoundException('The repas does not exist');
    }

    $form = $this->createForm(RepasType::class, $repas);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        return $this->redirectToRoute('app_repas_index', [], Response::HTTP_SEE_OTHER);
    }
   
    return $this->renderForm('repas/edit.html.twig', [
        'repas' => $repas,
        'form' => $form,
    ]);
}
#[Route('/read', name: 'app_repas_i', methods: ['GET'])]
    public function read(): Response
    {
    
        $repository= $this->getDoctrine()->getRepository(Repas::class)->findAll();
     
        return $this->render('repas/read.html.twig',['Repas'=>$repository,
    ]);
    }

#[Route('/{id}', name: 'app_repas_delete', methods: ['POST'])]
public function delete(Request $request, $id, EntityManagerInterface $entityManager): Response
{
    $repas = $entityManager->getRepository(Repas::class)->find($id);

    if (!$repas) {
        throw $this->createNotFoundException('Repas not found');
    }

    if ($this->isCsrfTokenValid('delete'.$repas->getId(), $request->request->get('_token'))) {
        $entityManager->remove($repas);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_repas_index');
}
}
