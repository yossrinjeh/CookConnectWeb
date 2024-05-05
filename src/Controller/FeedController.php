<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Poste;
use App\Repository\PosteRepository;
use App\Repository\CommentaireRepository;
use App\Repository\LikesRepository;

class FeedController extends AbstractController
{

    private $posteRepository;

    public function __construct(PosteRepository $posteRepository)
    {
        $this->posteRepository = $posteRepository;
    }

    #[Route('/feed', name: 'app_feed')]
    public function index(PosteRepository $postRepository, CommentaireRepository $commentRepository, LikesRepository $likeRepository): Response
    {
        $posts = $postRepository->findAll();

        $postsWithDetails = [];

        foreach ($posts as $post) {
            $comments = $commentRepository->selectCommentsByPostId($post->getId());

            $likes = $likeRepository->selectLikesByPostId($post->getId());

            $postsWithDetails[] = [
                'post' => $post,
                'comments' => $comments,
                'likes' => $likes,
            ];
        }

        return $this->render('feed/index.html.twig', [
            'postsWithDetails' => $postsWithDetails,
        ]);
    }
}
