<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Poste;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\CommentaireRepository;
use App\Repository\PosteRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;
use DateTime;

use Dompdf\Dompdf;
use Dompdf\Options;



#[Route('/commentaire')]
class CommentaireController extends AbstractController
{

    private $logger;
    private $security;

    public function __construct(LoggerInterface $logger, Security $security)
    {
        $this->logger = $logger;
        $this->security = $security;
    }

    #[Route('/', name: 'app_commentaire_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $commentaires = $entityManager
            ->getRepository(Commentaire::class)
            ->findAll();

        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaires,
        ]);
    }

    #[Route('/new', name: 'app_commentaire_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, PosteRepository $posteRepository): Response
    {
        $user = $this->security->getUser();

        // Retrieve data directly from the request object
        $postId = $request->request->get('postId');
        $commentText = $request->request->get('comment');

        if (!$postId || !$commentText) {
            // Return a response with a bad request status if any data is missing
            return new Response('Missing data', Response::HTTP_BAD_REQUEST);
        }

        $post = $posteRepository->selectPostById($postId);

        if (!$post) {
            // Return a response with a not found status if the post is not found
            return new Response('Post not found', Response::HTTP_NOT_FOUND);
        }

        $commentaire = new Commentaire();
        $commentaire->setPoste($post);
        $commentaire->setUser($user);
        $commentaire->setCommentaire($commentText);
        $commentaire->setDate(new \DateTime());

        $entityManager->persist($commentaire);
        $entityManager->flush();

        // Return a simple success response
        return new Response('Success', Response::HTTP_OK);
    }

    #[Route('/show/{id}', name: 'app_commentaire_show', methods: ['GET'])]
    public function show(Commentaire $commentaire): Response
    {
        return $this->render('commentaire/show.html.twig', [
            'commentaire' => $commentaire,
        ]);
    }

    #[Route('/commentaire/{id}/edit', name: 'app_commentaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commentaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commentaire/edit.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form,
        ]);
    }


    #[Route('/delete/{id}', name: 'app_commentaire_delete', methods: ['POST'])]
    public function delete(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $commentaire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commentaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commentaire_index', [], Response::HTTP_SEE_OTHER);
    }


    // #[Route('/get_comments/{id_post}', name: 'get_comments', methods: ['GET'])]
    // public function getComments(Request $request, CommentaireRepository $commentaireRepository)
    // {
    //     $id_post = $request->query->get('id_post');

    //     $comments = $commentaireRepository->selectCommentsByPostId($id_post);

    //     $responseData = [];

    //     foreach ($comments as $comment) {
    //         $user = $comment->getUser();


    //         if ($user) {

    //             $informationPersonnele = $user->getInformationPersonnelle();

    //             if ($informationPersonnele) {
    //                 $responseData[] = [
    //                     'userAvatar' => $user->getImage(),
    //                     'username' => $informationPersonnele->getNom(),
    //                     'commentText' => $comment->getCommentaire(),
    //                     'timestamp' => $comment->getDate()->format('Y-m-d H:i:s')
    //                 ];
    //             } else {
    //                 $responseData[] = [
    //                     'userAvatar' => 'default-avatar.jpg',
    //                     'username' => 'Unknown User',
    //                     'commentText' => $comment->getCommentaire(),
    //                     'timestamp' => $comment->getDate()->format('Y-m-d H:i:s')
    //                 ];
    //             }
    //         }
    //     }

    //     return new JsonResponse($responseData);
    // }

    #[Route('/get_comments/{id_post}', name: 'get_comments', methods: ['GET'])]
    public function getComments(Request $request, CommentaireRepository $commentaireRepository)
    {
        $id_post = $request->attributes->get('id_post');

        $this->logger->info('Received request for comments for post ID: ' . $id_post);

        $comments = $commentaireRepository->selectCommentsByPostId($id_post);

        $this->logger->info('Number of comments retrieved: ' . count($comments));

        $responseData = [];

        foreach ($comments as $comment) {
            $user = $comment->getUser();

            if ($user) {
                $informationPersonnele = $user->getInformationPersonnelle();

                if ($informationPersonnele) {
                    $responseData[] = [
                        'id' => $comment->getId(),
                        'userAvatar' => $user->getImage(),
                        'username' => $informationPersonnele->getNom(),
                        'commentText' => $comment->getCommentaire(),
                        'timestamp' => $comment->getDate()->format('Y-m-d H:i:s')
                    ];
                } else {
                    $responseData[] = [
                        'id' => $comment->getId(),
                        'userAvatar' => 'default-avatar.jpg',
                        'username' => 'Unknown User',
                        'commentText' => $comment->getCommentaire(),
                        'timestamp' => $comment->getDate()->format('Y-m-d H:i:s')
                    ];
                }
            }
        }

        $this->logger->info('Response data: ' . json_encode($responseData));

        return new JsonResponse($responseData);
    }

    // #[Route('/get_comments_pdf/{id_post}', name: 'get_comments', methods: ['GET'])]
    // public function getCommentsForPDF(Request $request, CommentaireRepository $commentaireRepository)
    // {
    //     $id_post = $request->attributes->get('id_post');

    //     $comments = $commentaireRepository->selectCommentsByPostId($id_post);

    //     return $this->renderView('commentaire/list_comments_per_post.html.twig', [
    //         'comments' => $comments,
    //     ]);
    // }

    #[Route('/get_comments_pdf/{id_post}', name: 'get_comments_pdf', methods: ['GET'])]
    public function getCommentsForPDF(Request $request, CommentaireRepository $commentaireRepository, Environment $twig)
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        $id_post = $request->attributes->get('id_post');

        $comments = $commentaireRepository->selectCommentsByPostId($id_post);

        $html = $twig->render('commentaire/list_comments_per_post.html.twig', [
            'comments' => $comments,
        ]);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $pdfContent = $dompdf->output();

        $response = new Response($pdfContent);

        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="comments.pdf"');

        return $response;
    }
}
