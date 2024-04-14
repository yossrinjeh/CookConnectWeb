<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\ResetPassType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{
    #[Route('/reset_password', name: 'app_reset_password')]
    public function index( MailerController $mailer ,UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository, MailerInterface $test , Request $request,): Response
    {
        //$user = new User();
        //$mailer->sendEmail($test,'yossrinjeh46@gmail.com','1234');
        $form = $this->createForm(ResetPassType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user=$userRepository->findOneByEmail( $form->get('email')->getData());
        //dd($user->getVerificationCode());
        if($user->getVerificationCode()== $form->get('verificationCode')->getData()){
            
            return $this->redirectToRoute('app_chnage_password');
    }
        }
        return $this->render('reset_password/index.html.twig', [
            'controller_name' => 'ResetPasswordController',
            'form'=>$form->createView(),
        ]);
    }
    #[Route('/change', name: 'app_chnage_password')]
    public function chnage( ): Response
    {
        $form = $this->createForm(ChangePasswordType::class);
        return $this->render('reset_password/change.html.twig', [
            'controller_name' => 'changePassword',
            'form'=>$form->createView(),
        ]);
    }

}
