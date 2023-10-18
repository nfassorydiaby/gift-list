<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserEditBackType;
use App\Repository\GiftListRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @IsGranted("ROLE_ADMIN")
 */
#[Route('/user')]
class UserController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/', name: 'admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        if (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {

            return $this->redirectToRoute('front_app_default');
        }
        return $this->render('back/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/new', name: 'admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {

        if (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {

            return $this->redirectToRoute('front_app_default');
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $user->setRoles($form->get('roles')->getData());
            $user->setIsVerified(true);
    

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('back_admin_user_index');
        }

        return $this->render('back/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/{id}', name: 'admin_user_show', methods: ['GET'])]
    public function show(User $user, GiftListRepository $giftListRepository): Response
    {
        if (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {

            return $this->redirectToRoute('front_app_default');
        }


        $allGiftLists = $giftListRepository->findAll();

        // Filtrer les listes de cadeaux pour obtenir celles qui appartiennent à l'utilisateur spécifié
        $userGiftLists = [];
        foreach ($allGiftLists as $giftList) {
            if ($giftList->getUser() === $user) {
                $userGiftLists[] = $giftList;
            }
        }

        return $this->render('back/user/show.html.twig', [
            'user' => $user,
            'giftLists' => $userGiftLists,
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/{id}/edit', name: 'admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        if (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {

            return $this->redirectToRoute('front_app_default');
        }
        $form = $this->createForm(UserEditBackType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($user);

            $entityManager->flush();

            return $this->redirectToRoute('back_admin_user_index');
        }

        return $this->render('back/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/{id}', name: 'admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {

            return $this->redirectToRoute('front_app_default');
        }
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('back_admin_user_index');
    }

}
