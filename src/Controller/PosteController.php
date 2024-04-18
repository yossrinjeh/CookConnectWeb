<?php

namespace App\Controller;

use App\Entity\Poste;
use App\Form\PosteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\InformationPersonnele;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\InformationPersonneleRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;


#[Route('/poste')]
class PosteController extends AbstractController
{
    #[Route('/', name: 'app_poste_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $postes = $entityManager
            ->getRepository(Poste::class)
            ->findAll();

        return $this->render('poste/index.html.twig', [
            'postes' => $postes,
        ]);
    }

    #[Route('/new', name: 'app_poste_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ManagerRegistry $doctrine) // Inject ManagerRegistry
    {
        // Create a new Post instance
        $post = new Poste();

        // Create the form using the PosteType form class
        $form = $this->createForm(PosteType::class, $post);

        // Handle the form submission
        $form->handleRequest($request);

        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the uploaded file
            $file = $form->get('media')->getData();

            // Check if a file is uploaded
            if ($file instanceof UploadedFile) {
                // Generate a unique filename with extension
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                // Move the file to the media directory
                $file->move(
                    $this->getParameter('media_directory'),
                    $fileName
                );

                // Get the file extension
                $fileExtension = $file->guessExtension();

                // Define allowed extensions
                $allowedImageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $allowedVideoExtensions = ['mp4', 'avi', 'mov', 'mkv'];

                // Check file extension and set appropriate property
                if (in_array($fileExtension, $allowedImageExtensions)) {
                    $post->setImage($fileName);
                } elseif (in_array($fileExtension, $allowedVideoExtensions)) {
                    $post->setVideo($fileName);
                } else {
                    throw new \Exception('Invalid file type. Please upload either an image or a video.');
                }
            }

            // Save the post to the database
            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            // Redirect to the index page or wherever you want
            return $this->redirectToRoute('app_post_index');
        }

        // Render the new post form
        return $this->render('poste/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'app_poste_show', methods: ['GET'])]
    public function show(Poste $poste): Response
    {
        return $this->render('poste/show.html.twig', [
            'poste' => $poste,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_poste_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Poste $poste, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PosteType::class, $poste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_poste_index', [], Response::HTTP_SEE_OTHER);
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

        return $this->redirectToRoute('app_poste_index', [], Response::HTTP_SEE_OTHER);
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
}
