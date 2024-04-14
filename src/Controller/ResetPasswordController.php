<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\ResetPassType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{
    #[Route('/reset_password', name: 'app_reset_password')]
    public function index(MailerController $mailer, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository, MailerInterface $test, Request $request): Response
    {
         //$mailer->sendEmail($test,'yossrinjeh46@gmail.com','1234');
        $form = $this->createForm(ResetPassType::class);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneByEmail($form->get('email')->getData());
    
            if ($user && $user->getVerificationCode() == $form->get('verificationCode')->getData()) {
                return $this->redirectToRoute('app_change_password', ['email' => $user->getEmail()]);
            } else {
                // Handle case where user is not found or verification code doesn't match
                return new Response("Invalid email or verification code!", Response::HTTP_BAD_REQUEST);
            }
        }
    
        return $this->render('reset_password/index.html.twig', [
            'controller_name' => 'ResetPasswordController',
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/change/{email}', name: 'app_change_password')]
    public function change(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository, EntityManagerInterface $entityManager, string $email): Response
    {
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);
    
        $user = $userRepository->findOneByEmail($email);
    
        if (!$user) {
            // Handle case where user is not found
            return new Response("User not found!", Response::HTTP_NOT_FOUND);
        }
    
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
    
            $entityManager->persist($user);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_login');
        }
    
        return $this->render('reset_password/change.html.twig', [
            'controller_name' => 'ChangePasswordController',
            'form' => $form->createView(),
        ]);
    }
    

}
