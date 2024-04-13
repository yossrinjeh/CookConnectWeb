<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
       if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
       // throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    #[Route(path: '/backoffice/users', name: 'backoffice_users_list')]
    public function getusersList() : Response{
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $informationPersonnelles = [];
        foreach ($users as $user) {
            $informationPersonnelles[$user->getId()] = $user->getInformationPersonnelle();
        }
        return $this->render('security/usersList.html.twig', [
            'users' => $users,
            'informationPersonnelles' => $informationPersonnelles,
        ]);
    }
    #[Route(path: '/toggle-active/{id}', name: 'toggle_user_active')]
    public function toggleUserActive($id): RedirectResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Toggle isActive status
        $user->setIsActive(!$user->isIsactive());
        $entityManager->flush();

        return $this->redirectToRoute('backoffice_users_list');
    }
}
