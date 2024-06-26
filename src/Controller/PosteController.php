<?php

namespace App\Controller;

use App\Entity\Poste;
use App\Entity\User;
use App\Form\PosteType;
use App\Form\LikesType;
use App\Entity\Likes;
use DateTime;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\InformationPersonnele;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\InformationPersonneleRepository;
use App\Repository\PosteRepository;
use App\Repository\CommentaireRepository;
use App\Repository\LikesRepository;
use App\Repository\UserRepository;
// use PosteType as GlobalPosteType;
use Symfony\Component\HttpFoundation\File\UploadedFile;


use Symfony\Component\String\UnicodeString;
use Snipe\BanBuilder\CensorWords;
use Symfony\Component\Security\Core\Security;


#[Route('/poste')]
class PosteController extends AbstractController
{

    private $security;
    private $posteRepository;

    public function __construct(Security $security, PosteRepository $posteRepository)
    {
        $this->security = $security;
        $this->posteRepository = $posteRepository;
    }


    #[Route('/', name: 'app_poste_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, Security $security): Response
    {
        $postes = $entityManager
            ->getRepository(Poste::class)
            ->findAll();

        $user = $security->getUser();

        return $this->render('poste/index.html.twig', [
            'postes' => $postes,
            'user' => $user,
        ]);
    }


    #[Route('/new')]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $ur): Response
    {
        if ($this->getUser()) {

            $post = new Poste();
            $form = $this->createForm(PosteType::class, $post);
            $form->handleRequest($request);
            $user = $ur->findOneByEmail($this->getUser()->getUserIdentifier());
            $post->setUser($user);

            if ($form->isSubmitted() && $form->isValid()) {
                if ($this->containsProfanity($post)) {
                    $this->addFlash('warning', 'Your post contains inappropriate content. Please review and try again.');
                } else {
                    /** @var UploadedFile $file */
                    $file = $request->files->get('media');

                    if ($file instanceof UploadedFile) {
                        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                        $file->move(
                            $this->getParameter('your_images_directory'),
                            $fileName
                        );

                        $fileExtension = $file->guessExtension();
                        if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
                            $fileUrl = 'images/' . $fileName;
                            $post->setImage($fileUrl);
                            $post->setVideo(null);
                        } elseif (in_array($fileExtension, ['mp4', 'avi', 'mov', 'mkv'])) {
                            $fileUrl = 'images/' . $fileName;
                            $post->setVideo($fileUrl);
                            $post->setImage(null);
                        } else {
                            throw new \Exception('Invalid file type. Please upload either an image or a video.');
                        }
                    }

                    $entityManager->persist($post);
                    $entityManager->flush();

                    return $this->redirectToRoute('app_poste_index');
                }
            }

            return $this->render('poste/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }


    private function containsProfanity(Poste $post): bool
    {
        $censor = new CensorWords;
        $censor->setDictionary(array(
            'en-us',
            'en-uk',
            'fr'
        ));

        $title = $post->getTitre();
        $description = $post->getDescription();

        $censoredTitle = $censor->censorString($title)['clean'];
        $censoredDescription = $censor->censorString($description)['clean'];

        $profanityDetected = $title !== $censoredTitle || $description !== $censoredDescription;

        return $profanityDetected;
    }




    // #[Route('/new')]
    // public function new(Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $post = new Poste();
    //     $form = $this->createForm(PosteType::class, $post);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         /** @var UploadedFile $file */
    //         $file = $request->files->get('media');

    //         if ($file instanceof UploadedFile) {
    //             $fileName = md5(uniqid()) . '.' . $file->guessExtension();

    //             // Move the file to the specified directory
    //             $file->move(
    //                 $this->getParameter('your_images_directory'),
    //                 $fileName
    //             );

    //             // Determine whether the file is an image or video
    //             $fileExtension = $file->guessExtension();
    //             if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
    //                 // Set image URL in the database
    //                 $fileUrl = 'images/' . $fileName;
    //                 $post->setImage($fileUrl);
    //                 $post->setVideo(null);
    //             } elseif (in_array($fileExtension, ['mp4', 'avi', 'mov', 'mkv'])) {
    //                 // Set video URL in the database
    //                 $fileUrl = 'images/' . $fileName;
    //                 $post->setVideo($fileUrl);
    //                 $post->setImage(null);
    //             } else {
    //                 // Unsupported file type
    //                 throw new \Exception('Invalid file type. Please upload either an image or a video.');
    //             }
    //         }

    //         $entityManager->persist($post);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_poste_index');
    //     }

    //     return $this->render('poste/new.html.twig', [
    //         'form' => $form->createView(),
    //     ]);
    // }

    // #[Route('/new', name: 'app_poste_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, ManagerRegistry $doctrine) 
    // {
    //     $post = new Poste();

    //     $form = $this->createForm(PosteType::class, $post);

    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $file = $form->get('media')->getData();

    //         if ($file instanceof UploadedFile) {
    //             $fileName = md5(uniqid()) . '.' . $file->guessExtension();

    //             $file->move(
    //                 $this->getParameter('media_directory'),
    //                 $fileName
    //             );

    //             $fileExtension = $file->guessExtension();

    //             $allowedImageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    //             $allowedVideoExtensions = ['mp4', 'avi', 'mov', 'mkv'];

    //             if (in_array($fileExtension, $allowedImageExtensions)) {
    //                 $post->setImage($fileName);
    //             } elseif (in_array($fileExtension, $allowedVideoExtensions)) {
    //                 $post->setVideo($fileName);
    //             } else {
    //                 throw new \Exception('Invalid file type. Please upload either an image or a video.');
    //             }
    //         }

    //         $entityManager = $doctrine->getManager();
    //         $entityManager->persist($post);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_post_index');
    //     }

    //     return $this->render('poste/new.html.twig', [
    //         'post' => $post,
    //         'form' => $form->createView(),
    //     ]);
    // }


    // #[Route('/{id}', name: 'app_poste_show', methods: ['GET'])]
    // public function show(Poste $poste): Response
    // {
    //     return $this->render('poste/show.html.twig', [
    //         'poste' => $poste,
    //     ]);
    // }

    // #[Route('/my-posts', name: 'my_posts', methods: ['GET'])]
    // public function myPosts(PosteRepository $postRepository, CommentaireRepository $commentRepository, LikesRepository $likeRepository, UserRepository $userRepository, Security $security): Response
    // {
    //     $current_user = $security->getUser();
    //     $user = $userRepository->findOneByEmail($current_user->getUserIdentifier());
    //     $userPosts = $postRepository->selectPostsByUserId($user->getId());

    //     $postsWithDetails = [];

    //     foreach ($userPosts as $post) {
    //         $comments = $commentRepository->selectCommentsByPostId($post->getId());
    //         $likes = $likeRepository->selectLikesByPostId($post->getId());

    //         $postsWithDetails[] = [
    //             'post' => $post,
    //             'comments' => $comments,
    //             'likes' => $likes,
    //         ];
    //     }

    //     return $this->render('myPosts/my_posts.html.twig', [
    //         'postsWithDetails' => $postsWithDetails,
    //     ]);
    // }

    #[Route('/my_posts', name: 'app_my_posts', methods: ['GET'])]
    public function myPosts(PosteRepository $postRepository, CommentaireRepository $commentRepository, LikesRepository $likeRepository, UserRepository $userRepository, Security $security): Response
    {
        $current_user = $security->getUser();

        if ($current_user) {
            // $userId = $current_user->getId();
            $user = $userRepository->find($current_user);

            if ($user) {
                $userPosts = $postRepository->selectPostsByUserId($user->getId());

                $postsWithDetails = [];

                foreach ($userPosts as $post) {
                    $comments = $commentRepository->selectCommentsByPostId($post->getId());
                    $likes = $likeRepository->selectLikesByPostId($post->getId());

                    $postsWithDetails[] = [
                        'post' => $post,
                        'comments' => $comments,
                        'likes' => $likes,
                    ];
                }

                return $this->render('poste/myPosts/my_posts.html.twig', [
                    'postsWithDetails' => $postsWithDetails,
                ]);
            }
        }

        return $this->redirectToRoute('app_login');
    }




    #[Route('/{id}/edit', name: 'app_poste_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Poste $poste, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PosteType::class, $poste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_my_posts', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('poste/edit.html.twig', [
            'poste' => $poste,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_poste_delete', methods: ['POST'])]
    public function delete(Request $request, Poste $poste, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $poste->getId(), $request->request->get('_token'))) {
            $entityManager->remove($poste);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_my_posts', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/get_username', name: 'get_username', methods: ['GET'])]
    public function getUsername(Request $request, InformationPersonneleRepository $informationPersonneleRepository)
    {
        $id_user = $request->query->get('id_user');

        $informationPersonnele = $informationPersonneleRepository->findByUserId($id_user);

        if (!empty($informationPersonnele)) {
            /** @var InformationPersonnele $informationPersonneleEntity */
            $informationPersonneleEntity = $informationPersonnele[0];
            $username = $informationPersonneleEntity->getNom();
            return new JsonResponse(['username' => $username]);
        } else {
            return new JsonResponse(['username' => 'Unknown User']);
        }
    }

    #[Route('/like/{postId}', name: 'app_post_like', methods: ['POST'])]
    public function likePost(Request $request, int $postId, EntityManagerInterface $entityManager): Response
    {
        $like = new Likes();
        $form = $this->createForm(LikesType::class, $like);

        // Handle the form submission
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if the user is authenticated
            $user = $this->getUser();
            if (!$user) {
                return new JsonResponse(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
            }

            // Get the post from the database
            $post = $entityManager->getRepository(Poste::class)->find($postId);
            if (!$post) {
                return new JsonResponse(['message' => 'Post not found'], Response::HTTP_NOT_FOUND);
            }

            // Set additional data for the like entity
            $like->setUser($user);
            $like->setPoste($post);
            $like->setDate(new DateTime());

            // Persist the like entity
            $entityManager->persist($like);
            $entityManager->flush();

            return new JsonResponse(['message' => 'Post liked'], Response::HTTP_OK);
        }

        // If the form is not submitted or not valid, return a response indicating an error
        return new JsonResponse(['message' => 'Invalid form submission'], Response::HTTP_BAD_REQUEST);
    }
}
