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
use phpDocumentor\Reflection\Types\Void_;
use Twilio\Rest\Client;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        if($this->getUser()  && in_array('CHEF', $this->getUser()->getRoles()) || in_array('ADMIN', $this->getUser()->getRoles()) || in_array('CHEFMASTER', $this->getUser()->getRoles())){
        // Fetch all Repas entities using selectAllRepas method
        $repas = $repasRepository->selectAllRepas();

        // Render the template and pass the repas data to it
        return $this->render('repas/index.html.twig', [
            'repas' => $repas,
        ]);
    }else{
        return $this->redirectToRoute('app_login');

    }
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
            
            $this->envoyerSms();


            return $this->redirectToRoute('app_repas_index', [], Response::HTTP_SEE_OTHER);

        }

        return $this->renderForm('repas/new.html.twig', [
            'repas' => $repas,
            'form' => $form,
        ]);
    }
        private function envoyerSms(): void
        {
            // Remplacer ces valeurs par vos identifiants Twilio
            $sid = 'AC23002d58f668196d97bd7b1fc9ab5799';
            $token = 'be933030f1bdf9a438760e7f4cc08c01';
            $from = '+13168545234 ';
    
    
            // Initialisez le client Twilio
            $client = new Client($sid, $token);
    
    
            // Remplacer `votre_numero_destinataire` par le numéro de téléphone du destinataire
            $numeroDestinataire = '+21698587612';
    
    
            // Message à envoyer
            $message = 'ajouter repas';
    
    
            // Envoyer le SMS
            $client->messages->create(
                $numeroDestinataire,
                [
                    'from' => $from,
                    'body' => $message
                ]
            );
    
    
           
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
    #[Route('/export-pdf', name: 'export_pdf')]
    public function exportPdf(RepasRepository $repasRepository): Response
    {
        // Fetch all bookings from the database
        $repas = $repasRepository->findAll();
    
        $logoPath = 'C:\Users\mohamed\Documents\GitHub\CookConnectWeb\public\mmmmmmm.png';
        $logoImage = $this->generateBase64Image($logoPath);
    
        // Generate the PDF content for the booking with the logo
    
        // Generate the PDF content
        $html = $this->renderView('repas/PDF.html.twig', [
            'repas' => $repas,
            'logoImage' => $logoImage, // Pass the logo image to the template

        ]);
    
        // Configure Dompdf
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
    
        // Instantiate Dompdf
        $dompdf = new Dompdf($pdfOptions);
    
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
    
        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
    
        // Render the HTML as PDF
        $dompdf->render();
    
        // Set the file name
        $fileName = 'NonPaid_bookings_' . date('YmdHis') . '.pdf';
    
        // Return the response containing the PDF content
        return new Response($dompdf->output(), Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }


private function generateBase64Image($imagePath) {
    $imageData = file_get_contents($imagePath);
    $base64Image = 'data:image/png;base64,' . base64_encode($imageData);
    return $base64Image;
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
    #[Route('/repass/{id}', name: 'app_repas_showf', methods: ['GET'])]
    public function showf(Repas $repas): Response
    {
        if (!$repas) {
            throw new NotFoundHttpException('Repas not found');
        }

         return $this->render('repas/showFront.html.twig', [
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
#[Route("/upload_pdf_to_dropbox", name: "upload_pdf_to_dropbox", methods: ["POST"])]    
public function uploadPdfToDropbox(Request $request): Response
{
  
        // Get the PDF content from the request
        $pdfContent = $request->getContent();
        
        // Generate a unique file path on Dropbox
        $filePath = "/booking_details_" . uniqid() . ".pdf";
        
    // Replace with your Dropbox access token
    $accessToken = 'sl.B0XzJi48ZXKHqcqelkinUdtH5DtMo9eXBEr8cocebkyCNJM_x5lrHdJiG-hPKvVfh9OWJYNxFyR42h53g2gjHvNnfNW5MVkMTxtnM2cLJpTrfnZJqGxMMJHP8l_AsTIYgp-Utmb3ubkq';

    // Create a cURL handle
    $ch = curl_init();

    // Set the URL for uploading to Dropbox
    curl_setopt($ch, CURLOPT_URL, 'https://content.dropboxapi.com/2/files/upload');

    // Set the request method to POST
    curl_setopt($ch, CURLOPT_POST, true);

    // Set the request headers
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/octet-stream',
        'Dropbox-API-Arg: {"path": "' . $filePath . '"}'
    ]);

    // Set the request body (PDF content)
    curl_setopt($ch, CURLOPT_POSTFIELDS, $pdfContent);

    // Disable SSL certificate verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // Execute the cURL request
    $response = curl_exec($ch);

    // Check for errors
    if ($response === false) {
        $error = curl_error($ch);
        // Handle the error
        return new JsonResponse(['error' => $error], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    // Close the cURL handle
    curl_close($ch);

    // Return a success response
    return new JsonResponse(['success' => true]);
}


}