<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {

            if (!$this->getUser()->isVerified()) { // Assurez-vous que la méthode isActive() existe dans votre entité User
                // Déconnecter l'utilisateur
                return $this->redirectToRoute('app_logout');
    
                // Rediriger vers la page de connexion avec un message d'erreur
                $this->addFlash('error', 'Votre compte n\'est pas activé.'); // Assurez-vous que votre site prend en charge les messages flash
                return $this->redirectToRoute('app_login'); // Utilisez le nom de route correct pour votre page de connexion
            }
    
             return $this->redirectToRoute('front_app_gift_list_index');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        //dd($error);
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
