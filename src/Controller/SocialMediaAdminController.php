<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Poste;
use App\Entity\Commentaire;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class SocialMediaAdminController extends AbstractController
{


    // #[Route('/backoffice/socialMedia', name: 'app_social_media_admin')]
    // public function index(EntityManagerInterface $entityManager): Response
    // {
    //     return $this->render('social_media_admin/index.html.twig');
    // }

    // #[Route('/backoffice/posts/data', name: 'app_posts_data')]
    // public function getPostsData(Request $request, EntityManagerInterface $entityManager): JsonResponse
    // {
    //     // Retrieve the necessary parameters from the request
    //     $draw = $request->get('draw');
    //     $start = $request->get('start');
    //     $length = $request->get('length');
    //     $searchValue = $request->get('search')['value'];

    //     // Fetch the posts from the database based on the search value and pagination
    //     $repository = $entityManager->getRepository(Poste::class);
    //     $queryBuilder = $repository->createQueryBuilder('p');

    //     // Apply the search filter if a search value is provided
    //     if (!empty($searchValue)) {
    //         $queryBuilder->andWhere('p.titre LIKE :searchValue OR p.description LIKE :searchValue')
    //             ->setParameter('searchValue', '%' . $searchValue . '%');
    //     }

    //     // Get the total count of posts (without pagination)
    //     $totalCount = $queryBuilder->select('COUNT(p)')->getQuery()->getSingleScalarResult();

    //     // Apply pagination
    //     $queryBuilder->setFirstResult($start)
    //         ->setMaxResults($length);

    //     // Fetch the posts
    //     $posts = $queryBuilder->getQuery()->getResult();

    //     // Format the data for the datatable response
    //     $data = [];
    //     foreach ($posts as $post) {
    //         $data[] = [
    //             'id' => $post->getId(),
    //             'titre' => $post->getTitre(),
    //             'description' => $post->getDescription(),
    //             // Add other fields as needed
    //         ];
    //     }

    //     // Prepare the response
    //     $response = [
    //         'draw' => $draw,
    //         'recordsTotal' => $totalCount,
    //         'recordsFiltered' => $totalCount, // For simplicity, we assume all records are filtered here
    //         'data' => $data,
    //     ];

    //     return new JsonResponse($response);
    // }


    #[Route('/backoffice/socialMedia', name: 'app_social_media_admin')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $posts = $entityManager
            ->getRepository(Poste::class)
            ->findAll();

        return $this->render('social_media_admin/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    // #[Route('/backoffice/socialMedia', name: 'app_social_media_admin')]
    // public function index(): Response
    // {
    //     return $this->render('social_media_admin/index.html.twig');
    // }

    // #[Route('/backoffice/posts', name: 'app_posts')]
    // public function getPosts(EntityManagerInterface $entityManager): JsonResponse
    // {
    //     $posts = $entityManager
    //         ->getRepository(Poste::class)
    //         ->findAll();

    //     $postData = [];
    //     foreach ($posts as $post) {
    //         $postData[] = [
    //             'id' => $post->getId(),
    //             'titre' => $post->getTitre(),
    //             'description' => $post->getDescription(),
    //             // Add other fields as needed
    //         ];
    //     }

    //     return new JsonResponse($postData);
    // }

    // #[Route('/backoffice/posts', name: 'app_posts')]
    // public function getPosts(EntityManagerInterface $entityManager): Response
    // {
    //     $posts = $entityManager
    //         ->getRepository(Poste::class)
    //         ->findAll();

    //     return $this->render('backOffice/posts.html.twig', [
    //         'posts' => $posts,
    //     ]);
    // }

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
}
