<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AddUserByAdminType;
use App\Security\AppCustomAuthenticator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class AddUsersByAdminController extends AbstractController
{
    #[Route('/addusersbyadmin', name: 'app_add_users_by_admin')]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppCustomAuthenticator $authenticator, EntityManagerInterface $entityManager,MailerController $mailer,MailerInterface $test): Response
    {
        if($this->getUser()  && in_array('ADMIN', $this->getUser()->getRoles())){
        $user = new User();
        $form = $this->createForm(AddUserByAdminType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           $password= $this->generateRandomPassword();
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $password
                )
            );
            $user->setImage('avatar.png');
            $user->setDate(new DateTime());
            $activationCode = rand(100000, 999999);
            $user->setVerificationCode($activationCode);
            $entityManager->persist($user);
            $entityManager->flush();
            
            // do anything else you need here, like send an email
            $request->getSession()->getFlashBag()->add('success', 'User created successfully');
            $mailer->sendEmailPassword($test, $user->getEmail(), $password);
            return $this->redirectToRoute('backoffice_users_list');
          
        }

        return $this->render('add_users_by_admin/index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }else{
        return $this->redirectToRoute('app_login');
    }
    }
    function generateRandomPassword($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle($chars), 0, $length);
    }


    #[Route('/sendemailmobile/{email}/{name}', name: 'app_sendemail')]
    public function sendemailmobile(Request $request,String $email,String $name,MailerController $mailer,MailerInterface $test): Response
    {
        $mailer->sendEmailMobile($test, $email, $name);
        return $this->redirectToRoute('app_home');

    }
}
