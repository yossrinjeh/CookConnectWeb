<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Poste;
use App\Repository\PosteRepository;

class FeedController extends AbstractController
{

    private $posteRepository;

    public function __construct(PosteRepository $posteRepository)
    {
        $this->posteRepository = $posteRepository;
    }

    #[Route('/feed', name: 'app_feed')]
    public function index(): Response
    {
        $posts = $this->posteRepository->SelectAllPosts();

        return $this->render('feed/index.html.twig', [
            'posts' => $posts,
            'controller_name' => "Feed",
        ]);
    }
}
