<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Poste;
use App\Entity\Commentaire;
use App\Repository\PosteRepository;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Knp\Component\Pager\PaginatorInterface;


class SocialMediaAdminController extends AbstractController
{

    // #[Route('/backoffice/socialMedia', name: 'app_social_media_admin')]
    // public function index(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    // {
    //     $postsQuery = $entityManager
    //         ->getRepository(Poste::class)
    //         ->createQueryBuilder('p')
    //         ->getQuery();

    //     $posts = $paginator->paginate(
    //         $postsQuery,
    //         $request->query->getInt('page', 1), // Current page number, default to 1
    //         5 // Number of items per page
    //     );

    //     return $this->render('social_media_admin/index.html.twig', [
    //         'posts' => $posts,
    //     ]);
    // }

    #[Route('/backoffice/socialMedia', name: 'app_social_media_admin')]
    public function index(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        $searchQuery = $request->query->get('search');

        $postsQuery = $entityManager
            ->getRepository(Poste::class)
            ->createQueryBuilder('p');

        if ($searchQuery) {
            $postsQuery
                ->andWhere('p.id LIKE :search')
                ->orWhere('p.titre LIKE :search')
                ->orWhere('p.description LIKE :search')
                ->setParameter('search', '%' . $searchQuery . '%');
        }

        $postsQuery = $postsQuery->getQuery();

        $posts = $paginator->paginate(
            $postsQuery,
            $request->query->getInt('page', 1), // Current page number, default to 1
            5 // Number of items per page
        );

        return $this->render('social_media_admin/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/chart_data', name: 'chart_data', methods: ['GET'])]
    public function chartData(PosteRepository $posteRepository, CommentaireRepository $commentaireRepository): JsonResponse
    {
        $postCount = $posteRepository->getPostCount();
        $commentCount = $commentaireRepository->getCommentCount();

        $data = [
            'post_count' => $postCount,
            'comment_count' => $commentCount,
        ];

        return $this->json($data);
    }


    #[Route('/backoffice/delete-post/{id}', name: 'app_delete_post')]
    public function deletePost(EntityManagerInterface $entityManager, Poste $post): Response
    {
        try {
            $entityManager->remove($post);
            $entityManager->flush();
            $this->addFlash('success', 'Post deleted successfully.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'An error occurred while deleting the post.');

            // Log::error($e->getMessage());
        }
        return $this->redirectToRoute('app_social_media_admin');
    }

    #[Route('/backoffice/delete-comment/{id}', name: 'app_delete_comment')]
    public function deleteComment(EntityManagerInterface $entityManager, Commentaire $comment): Response
    {
        try {
            $entityManager->remove($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Comment deleted successfully.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'An error occurred while deleting the comment.');

            // Log::error($e->getMessage());
        }
        return $this->redirectToRoute('app_social_media_admin');
    }


    #[Route('/backoffice/socialMedia/{postId}/comments', name: 'app_social_media_comments')]
    public function getCommentsForPost(int $postId, CommentaireRepository $commentaireRepository): JsonResponse
    {
        $comments = $commentaireRepository->selectCommentsByPostId($postId);

        $serializedComments = [];
        foreach ($comments as $comment) {
            $serializedComments[] = [
                'id' => $comment->getId(),
                'commentaire' => $comment->getCommentaire(),
            ];
        }

        return new JsonResponse($serializedComments);
    }

    #[Route('/backoffice/socialMedia/get-comments-by-post-id', name: 'get_comments_by_post_id')]
    public function getCommentsByPostId(Request $request, CommentaireRepository $commentRepository): JsonResponse
    {
        $postId = $request->query->get('postId');
        $comments = $commentRepository->findBy(['postId' => $postId]);

        $formattedComments = [];
        foreach ($comments as $comment) {
            $formattedComments[] = ['text' => $comment->getCommentaire()];
        }

        return new JsonResponse(['comments' => $formattedComments]);
    }
}
