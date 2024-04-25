<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('frontOffice/base.html.twig', [
            'controller_name' => 'HomeController',
        ]); 
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('frontOffice/about.html.twig');
    }
    #[Route('/contact', name: 'app_contact')]
    public function contactUs(): Response
    {
        return $this->render('frontOffice/contact.html.twig');
    }
    #[Route('/backoffice', name: 'app_back')]
    public function back(): Response
    {
        if ($this->getUser()  && in_array('ADMIN', $this->getUser()->getRoles())) {

            return $this->render('BackOffice/base.html.twig');
        } else {
            return $this->render('frontOffice/403.html.twig');
        }
    }
}
