<?php 

// src/Security/AccessDeniedHandler.php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function handle(Request $request, AccessDeniedException $accessDeniedException): RedirectResponse
    {
        // Vous pouvez également loguer quelque chose ici
        // Redirigez les utilisateurs vers la page d'accueil par exemple
        return new RedirectResponse('/');
    }
}
