<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/users-manage')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_users')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('back/user/users.html.twig', [
            'users' => $userRepository->findAll()
        ]);
    }

    #[Route('/edit-level/{id}/{slug_role}', name: 'app_users_edit_level')]
    public function editLevel(User $user, string $slug_role, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user->setRoles([$slug_role]); // Notice the use of square brackets to create an array.

        $entityManager->persist($user);
        $entityManager->flush();

        // Return a response, like a redirect or a JSON response, depending on your needs.
        $arr = [
            "succeed" => 'yes'
        ];

        return new JsonResponse($arr);
    }

}
