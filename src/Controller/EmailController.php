<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
class EmailController extends AbstractController
{
    #[Route('/email', name: 'app_email')]
    public function index(): Response
    
    {
        // CREATE A TRANSPORT OBJECT 
        $transport =Transport::fromDsn('smtp://foodhealthy550@gmail.com:xffsxqzzggmywxra@smtp.gmail.com:587');
        $mailer = new Mailer($transport);
        $email = (new Email());
        $email
            ->from('foodhealthy550@gmail.com')
            ->to('dabbebiouday9@gmail.com')
            ->subject('verification paiement')
            ->text('Votre paiement a été effectué avec succès. Merci d\'avoir acheté notre produit. Nous vous remercions de votre confiance et espérons que vous apprécierez votre achat. Si vous avez des questions ou avez besoin d\'assistance, n\'hésitez pas à nous contacter. Bonne journée.')

            ->html('<p class="text-center bg-success">Votre paiement a été effectué avec succès. Merci d\'avoir acheté notre produit. Nous vous remercions de votre confiance et espérons que vous apprécierez votre achat. Si vous avez des questions ou avez besoin d\'assistance, n\'hésitez pas à nous contacter. Bonne journée.</p>');
        $mailer->send($email);
        return $this->render('email/index.html.twig', [
            'controller_name' => 'EmailController',
        ]);
    }
}
