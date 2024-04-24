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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Knp\Component\Pager\PaginatorInterface;
#[Route('/repas')]
class RepasController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/p', name: 'app_repas_in', methods: ['GET'])]
    public function in(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        // Récupérer tous les repas depuis le repository
        $repasRepository = $entityManager->getRepository(Repas::class);
        $repasQuery = $repasRepository->findAll();

        // Paginer les résultats
        $pagination = $paginator->paginate(
            $repasQuery, // La query à paginer
            $request->query->getInt('page', 1), // Récupérer le numéro de page à partir de la requête, 1 par défaut
            3 // Nombre d'éléments par page
        );

        return $this->render('repas/repasFront.html.twig', [
            'pagination' => $pagination,
        ]);
    }
  
    #[Route('/', name: 'app_repas_index', methods: ['GET'])]
    public function index(RepasRepository $repasRepository): Response
    {
        // Fetch all Repas entities using selectAllRepas method
        $repas = $repasRepository->selectAllRepas();

        // Render the template and pass the repas data to it
        return $this->render('repas/index.html.twig', [
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
           

            $this->addFlash(
                'success',
                'Add successfully!'
            );

            return $this->redirectToRoute('app_repas_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('repas/new.html.twig', [
            'repas' => $repas,
            'form' => $form,
        ]);
    }
    #[Route('/backrechercheAjax', name: 'backrechercheAjax')]
    public function searchAjax(Request $request, RepasRepository $repo): Response
    {
        // Retrieve the search query parameter from the request
        $query = $request->query->get('q');

        // If the query is empty, return all results
        if (empty($query)) {
            $repas = $repo->findAll();
        } else {
            // Otherwise, search for meals by name
            $repas = $repo->findrepasByNom($query);
        }

        // Render the template with the results
        $html = $this->renderView("repas/index.html.twig", [
            "repas" => $repas,
        ]);

        // Return the HTML response
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

    #[Route('/repas/{id}', name: 'app_repas_show', methods: ['GET'])]
    public function show(Repas $repas): Response
    {
        if (!$repas) {
            throw new NotFoundHttpException('Repas not found');
        }

        return $this->render('repas/show.html.twig', [
            'repas' => $repas,
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
        $this->addFlash(
            'info',
            'update successfully!'
        );

        return $this->redirectToRoute('app_repas_index', [], Response::HTTP_SEE_OTHER);
    }
   
    return $this->renderForm('repas/edit.html.twig', [
        'repas' => $repas,
        'form' => $form,
    ]);
}
// #[Route('/read', name: 'app_repas_i', methods: ['GET'])]
//     public function read(): Response
//     {
    
//         $repository= $this->getDoctrine()->getRepository(Repas::class)->findAll();
     
//         return $this->render('repas/read.html.twig',['Repas'=>$repository,
//     ]);
//     }

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
        $this->addFlash(
            'del',
            'delet successfully!'
        );
    }

    return $this->redirectToRoute('app_repas_index');
}
// #[Route('/statistics', name: 'app_statistics', methods: ['GET'])]
// public function statistics(RepasRepository $repasRepository): Response
// {
//     $stats = $repasRepository->getRepasStatistics(); // Call the method on the repository

//     return $this->render('repas/statistics.html.twig', [
//         'client_repas' => $stats['client'],
//         'admin_repas' => $stats['admin'],
//     ]);
// }

#[Route('/statistics', name: 'statistics', methods: ['GET'])]
    public function statistics(): Response
    {
        $clientCount = $this->countGenderForEvents('client');
        $adminCount = $this->countGenderForEvents('admin');
      
    
        $data = [
            'client' => $clientCount,
            'admin' => $adminCount,
            
        ];
    
        $jsonData = json_encode($data);
    
        return $this->render('repas/statistics.html.twig', [
            'client' => $clientCount,
            'admin' => $adminCount,
           
            'jsonData' => $jsonData
        ]);
    }
private function countGenderForEvents(string $type): int
{
    $entityManager = $this->entityManager;

    // Query the database to count events based on gender
    $query = $entityManager->createQuery(
        'SELECT COUNT(e.id) AS repasCount
        FROM App\Entity\Repas e
        WHERE e.type = :type'
    )->setParameter('type', $type);

    $result = $query->getSingleResult();
    
    return $result['repasCount'];
}
}
