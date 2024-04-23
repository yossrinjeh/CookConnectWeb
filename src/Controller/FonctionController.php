<?php

namespace App\Controller;

use App\Entity\Repas;
use App\Entity\PlanAlim;
use App\Repository\RepasRepository;
use App\Repository\ProductRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Request; // Assurez-vous d'importer la classe Request depuis le composant HttpFoundation
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FonctionController extends AbstractController
{
    #[Route('/fonction', name: 'app_fonction')]
    public function index(): Response
    {
        return $this->render('fonction/index.html.twig', [
            'controller_name' => 'FonctionController',
        ]);
    }

// /**
//      * @Route("/listArb", name="listArb", methods={"GET"})
//      */
//     public function list(RepasRepository $FootRepository): Response
//     {
//         // Configure Dompdf according to your needs
//         $pdfoptions = new Options();
//         $pdfoptions->set('defaultFont', 'Arial');
//         $pdfoptions->set('tempDir', '.\www\DaryGym\public\uploads\images');


//         // Instantiate Dompdf with our options
//         $dompdf = new Dompdf($pdfoptions);
//         // Retrieve the HTML generated in our twig file
//         $html = $this->renderView('repas/show.html.twig', [
//             'categorie' => $FootRepository->findAll(),
//         ]);
//         // Load HTML to Dompdf
//         $dompdf->loadHtml($html);

//         // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
//         $dompdf->setPaper('A4', 'portrait');

//         // Render the HTML as PDF
//         $dompdf->render();

//         // Output the generated PDF to Browser (inline view)
//         $dompdf->stream("mypdf.pdf", [
//             "Attachment" => false
//         ]);
//     }
  


     /**
     * @Route("/TrierspcASC", name="triespc",methods={"GET"})
     */
    public function trierSpecialite(Request $request, RepasRepository $RepasRepository): Response
    {
        // Utilisez directement la méthode trie() du repository
        $arb = $RepasRepository->trie();

        return $this->render('repas/index.html.twig', [
            'repas' => $arb,
        ]);
    }

 /**
     * @Route("/TrierspcDESC", name="triespcDESC",methods={"GET"})
     */
    public function trierSp(Request $request, RepasRepository $repasRepository): Response
    {
        // Utilisez directement la méthode trie() du repository
        $arb = $repasRepository->trieDes();

        return $this->render('repas/index.html.twig', [
            'repas' => $arb,
        ]);
    }

 /**
     * @Route("/Trieprix", name="trieprix",methods={"GET"})
     */
    public function trierprode(Request $request, RepasRepository $repasRepository): Response
    {
        // Utilisez directement la méthode trie() du repository
        $arb = $repasRepository->trierepas();

        return $this->render('repas/index.html.twig', [
            'repass' => $arb,
        ]);
    }


    // /**
    //  * @Route("/Trieprixdes", name="trieprixdes",methods={"GET"})
    //  */
    // public function trierproddes(Request $request, ProductRepository $categorieRepository): Response
    // {
    //     // Utilisez directement la méthode trie() du repository
    //     $arb = $categorieRepository->trieproddes();

    //     return $this->render('product/index.html.twig', [
    //         'products' => $arb,
    //     ]);
    // }




    
 /**
     * @Route("/search", name="recherchearb",methods={"GET"})
     */
    public function searchoffreajax(Request $request, RepasRepository $repasRepository): Response
    {
        $requestString = $request->get('searchValue');

        // Utilisez directement la méthode trie() du repository
        $arb = $repasRepository->findbyNom($requestString);

        return $this->render('repas/index.html.twig', [
            'repas' => $arb,
        ]);
    }



    
// /**
//      * @Route("/stats", name="stats")
//      */
//     public function statistiques(RepasRepository $footRepo){
//         // On va chercher toutes les menus
//         $menus = $footRepo->findAll();

// //Data Category
//         $foot = $footRepo->createQueryBuilder('a')
//             ->select('count(a.id)')
//             ->Where('a.type= :type')
//             ->setParameter('type',"client")
//             ->getQuery()
//             ->getSingleScalarResult();

//         $hand = $footRepo->createQueryBuilder('a')
//             ->select('count(a.id)')
//             ->Where('a.type= :type')
//             ->setParameter('type',"admin")
//             ->getQuery()
//             ->getSingleScalarResult();
//         $volley= $footRepo->createQueryBuilder('a')
//             ->select('count(a.id)')
//             ->Where('a.type= :type')
//             ->setParameter('type',"hhhhh")
//             ->getQuery()
//             ->getSingleScalarResult();

       

//         return $this->render('Stats/stats.html.twig', [
//             'nfoot' => $foot,
//             'nhand' => $hand,
//             'nvol' => $volley,


//         ]);
//     }


}