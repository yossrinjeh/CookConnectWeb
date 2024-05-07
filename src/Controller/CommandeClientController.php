<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\Commande1Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use DateTime;
use App\Entity\Repas;
use App\Repository\RepasRepository;
use App\Repository\CommandeRepository;

use Symfony\Component\Mailer\Mailer ;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

use App\Entity\Recette;
use App\Entity\Livraison;
 

#[Route('/commande/client')]
class CommandeClientController extends AbstractController
{
    #[Route('/', name: 'app_commande_client_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $commandes = $entityManager
            ->getRepository(Commande::class)
            ->findAll();
    
            $repas = [];
            foreach ($commandes as $commande) {
                $repas[] = $commande->getRepas();
            }
    
        return $this->render('commande_client/index.html.twig', [
            'commandes' => $commandes,
            'repas' => $repas, // Pass the fetched Repas entities to the template
        ]);
    }

    #[Route('/new', name: 'app_commande_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commande = new Commande();
        $form = $this->createForm(Commande1Type::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commande);
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande_client/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/repas', name: 'app_commande_client_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager): Response
    {
        $repas = $entityManager
        ->getRepository(Repas::class)
        ->findAll();
        // Return the repas and commandes to the template for rendering
       



        return $this->render('commande_client/show_all_repas.html.twig', [
            'repas' => $repas,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Commande1Type::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande_client/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_client_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commande_client_index', [], Response::HTTP_SEE_OTHER);
    }






//---------------------------------------------send mail
    #[Route('/{id}/send-confirmation-email', name: 'app_send_confirmation_email', methods: ['POST'])]
public function sendConfirmationEmail(Commande $commande): Response
{
    // Check if the order status is "En cours"
    if ($commande->getEtat() === 'En cours') {
        // Generate a random 6-digit verification code
        $verificationCode = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);


        //get the user who is logged in
        $user = $this->getUser();
        //gets the mail
        $userEmail = $user->getEmail();



        
    
        // Email content with the verification code
        $htmlContent = "
            <html>
            <head>
                <style>
                    /* Your CSS styles */
                </style>
            </head>
            <body>
                <div class='container'>
                    <h2>Dear {$commande->getUser()->getEmail()},</h2>
                    <p>Your order confirmation:</p>
                    <ul>
                        <li><strong>Order Ticket:</strong> {$commande->getId()}</li>
                        <li><strong>PRIX  :</strong> {$commande->getPrix()}</li>
                        <!-- Include other order details as needed -->
                    </ul>
                   
                    <p>Your verification code is: <strong>$verificationCode</strong></p>
                    <p>Best regards,<br> Make sure to type the code correctly!</p>
                    <p class='thank-you'>Thank you for choosing us.</p>
                </div>
            </body>
            </html>
        ";

        $transport = Transport::fromDsn('smtp://tester44.tester2@gmail.com:hpevdqbvclzebhxa@smtp.gmail.com:587');
        $mailer = new Mailer($transport);

        // Create the email message
        $email = (new Email())
            ->from('tester44.tester2@gmail.com')
            ->to($userEmail)
            ->subject('reservation Confirmation')
            ->html($htmlContent);

        // Send the email
        $mailer->send($email);

        // You might want to save the verification code in the database or session for later verification by the user

        // Redirect to the confirmation page with the verification code
        return $this->redirectToRoute('app_confirm_order_verification', [
            'id' => $commande->getId(),
            'verificationCode' => $verificationCode,
        ]);
    } else {
        // Add flash message for orders with a status other than "En cours"
        $this->addFlash('error', 'Confirmation email cannot be sent for orders with a status other than "En cours".');

        // Redirect back to the order index page
        return $this->redirectToRoute('app_commande_client_index');
    }
}

#[Route('/{id}/confirm-order-verification/{verificationCode}', name: 'app_confirm_order_verification', methods: ['GET'])]
public function confirmOrderVerification(Commande $commande, $verificationCode): Response
{
    return $this->render('commande_client/new.html.twig', [
        'commande' => $commande,
        'verificationCode' => $verificationCode,
    ]);
}
/*
#[Route('/{id}/confirmorder', name: 'app_confirm_order', methods: ['POST'])]
public function confirmOrder(Request $request, Commande $commande): Response
{
    $verificationCode = $request->request->get('verificationCode');
    $storedVerificationCode = $request->request->get('storedVerificationCode');

    // Check if the verification code matches the one sent to the user's email
    if ($verificationCode === $storedVerificationCode) {
        // Update the order status to "confirmed"
        $commande->setEtat('confirmer');
        $this->getDoctrine()->getManager()->flush();

        // Add flash message for successful confirmation
        $this->addFlash('success', 'Order confirmed successfully.');
    } else {
        // Add flash message for incorrect verification code
        $this->addFlash('error', 'Incorrect verification code. Please try again.');
        return $this->redirectToRoute('app_confirm_order_verification', [
            'id' => $commande->getId(),
        ]);
    }

    // Redirect back to the order index page
    return $this->redirectToRoute('app_commande_client_index');
}


*/






#[Route('/commandeset/{id}', name: 'commandeset', methods: ['POST'])]
public function createCommandeAndLivraison(int $id, Request $request, EntityManagerInterface $entityManager): Response
{        
    // Find the repas based on the provided ID
    $repas = $entityManager->getRepository(Repas::class)->find($id);

    
    // Check if the repas exists
    if (!$repas) {
        throw $this->createNotFoundException('Repas not found');
    }
    
    // Find the associated recipe
    $recette = $entityManager
        ->getRepository(Recette::class)
        ->findOneById($repas->getIdRecette());
    
    // Check if the recipe exists
    if (!$recette) {
        throw $this->createNotFoundException('Recipe not found');
    }

    // Get the authenticated user
    $user = $this->getUser();
    
 // Create a new commande
$commande = new Commande();
$commande->setUser($user);
$commande->setPrix($recette->getPrix()); // Set prix from the associated recette
$commande->setEtat("En cours"); // Set default etat
$commande->setRepas($repas); // Set the repas object

    $entityManager->persist($commande);
    $entityManager->flush(); // Flush before accessing ID
    
    // Ensure the Commande ID is not null before creating the Livraison
    $commandeId = $commande->getId();
    if ($commandeId === null) {
        throw new \RuntimeException('Failed to get the ID of the created Commande entity.');
    }
    
    // Create a new livraison
    $livraison = new Livraison();
    $livraison->setUser($user);
    $livraison->setCommande($commande); // Set the Commande object
    $livraison->setAdresse($request->request->get('adresse')); // Assuming the adresse is provided via POST request
    $livraison->setDate(new DateTime());
    $livraison->setNumTelLivreur(4856); // Example value, you may want to change this
    $entityManager->persist($livraison);
    
    // Commit changes to the database
    $entityManager->flush();

    // Redirect the user to the appropriate page
    return $this->redirectToRoute('app_commande_client_show');
}

#[Route('/{id}/confirmorder', name: 'app_confirm_order', methods: ['POST'])]
public function confirmOrder(Request $request, Commande $commande): Response
{
    $verificationCode = $request->request->get('verificationCode');
    $storedVerificationCode = $request->request->get('storedVerificationCode');
    $adresse = $request->request->get('adresse');
    $numTel = $request->request->get('num_tel');

    // Check if the verification code matches the one sent to the user's email
    if ($verificationCode === $storedVerificationCode && !empty($adresse) && !empty($numTel)) {
        // Update the order status to "confirmed"
        $commande->setEtat('confirmer');
        
                // Set Livraison details
            // Set Livraison details
        $livraison = new Livraison();
        $livraison->setUser($this->getUser()); // Set the user who is logged in
        $livraison->setCommande($commande);
        $livraison->setAdresse($adresse);
        $livraison->setNumTelLivreur($numTel);
        $livraison->setDate(new \DateTime()); // Set the current date

        // Persist Livraison entity
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($livraison);
        $entityManager->flush();


        // Add flash message for successful confirmation
        $this->addFlash('success', 'Order confirmed successfully.');

        // Redirect back to the order index page
        return $this->redirectToRoute('app_commande_client_index');
    } else {
        // Add flash message for incorrect verification code or empty fields
        if ($verificationCode !== $storedVerificationCode) {
            $this->addFlash('error', 'Incorrect verification code. Please try again.');
        }
        if (empty($adresse)) {
            $this->addFlash('error', 'Delivery address cannot be empty.');
        }
        if (empty($numTel)) {
            $this->addFlash('error', 'Delivery phone number cannot be empty.');
        }
        
        // Redirect back to the verification page
        return $this->redirectToRoute('app_confirm_order_verification', [
            'id' => $commande->getId(),
            'verificationCode' => $verificationCode,
        ]);
    }
}



 } 




















 
