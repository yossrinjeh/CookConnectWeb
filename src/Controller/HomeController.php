<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
    public function back(UserRepository $userRepository,SessionInterface $session , Request $request): Response
    {
        if ($this->getUser()  && in_array('ADMIN', $this->getUser()->getRoles())){
            $userCounts = [];

            $registrationDates = $userRepository->findAllRegistrationDates();
    
            $monthLabels = [];
    
            foreach ($registrationDates as $date) {
                if ($date['date'] !== null) {
                // Extract month name from the registration date
                $monthLabels[] = $date['date']->format('F');
            }
            }
    
            $monthLabels = array_unique($monthLabels);
            $auser =  count($userRepository->findByIsactive(true));
            $iuser= count($userRepository->findByIsactive(false));
            foreach ($monthLabels as $month) {
                $users = $userRepository->findByRegistrationMonth($month);
    
                // Initialize counter for active users
                $activeCount = 0;
                $inactive=0;
                // Loop through users to count active users
                foreach ($users as $user) {
                    if ($user->isIsActive()) {
                        $activeCount++;
                       
                    }else{
                        $inactive ++;
                       
                    }
                }
    
                $activeUser[] = $activeCount;
            $inactiveUser[] = $inactive;
            }
            
            //dd($activeUser ,$inactiveUser );
          /*  $sessionDir = ini_get('session.save_path');

            // Count session files
            $sessionFiles = glob($sessionDir . '/*');
            $sessionCount = count($sessionFiles);*/

            $session = $request->getSession();

// Check if the counter exists
if (!$session->has('session_counter')) {
    // Initialize the counter
    $session->set('session_counter', 1);
} else {
    // Increment the counter
    $session->set('session_counter', $session->get('session_counter') + 1);
}

// Get the current counter value
$sessionNumber = $session->get('session_counter');
            
        return $this->render('BackOffice/base.html.twig', [
            'months' => array_values($monthLabels),
            'dataActive'=>$activeUser,
            'dataInactive'=>$inactiveUser,
            'session'=>$sessionNumber,
            'Actuser'=>$auser,
            'Inuser'=>$iuser,
            'cnumber'=>count($userRepository->findByRole("CLIENT"))

        ]);
        }else{
            return $this->render('frontOffice/403.html.twig');
        }
    }
    #[Route('/run-python-script', name: 'app_run',methods:["POST"])]
    public function runPythonScript(Request $request): Response
    {
        // Execute the Python script
        $output = [];
        $returnValue = null;
        exec('python G:\py\server.py', $output, $returnValue);

        // Check if the script executed successfully
        if ($returnValue === 0) {
            return $this->json(['success' => true, 'message' => 'Images Updated ']);
        } else {
            return $this->json(['success' => false, 'message' => 'Failed to execute Python script']);
        }
    }
}
