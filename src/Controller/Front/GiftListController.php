<?php

namespace App\Controller\Front;

use App\Entity\GiftList;
use App\Form\GiftListType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @IsGranted("ROLE_USER")
 */
#[Route('/gift-list')]
class GiftListController extends AbstractController
{
    #[Route('/', name: 'app_gift_list_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $giftLists = $entityManager->getRepository(GiftList::class)->findAll();

        return $this->render('front/gift_list/index.html.twig', [
            'giftLists' => $giftLists,
        ]);
    }

    #[Route('/new', name: 'app_gift_list_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $giftList = new GiftList();
        $form = $this->createForm(GiftListType::class, $giftList, [
            'validation_groups' => ['Default']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $giftList->setUser($this->getUser());
            $giftList->setArchived(false);

            $entityManager->persist($giftList);
            $entityManager->flush();

            return $this->redirectToRoute('front_app_gift_list_index');
        }

        return $this->render('front/gift_list/new.html.twig', [
            'giftList' => $giftList,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/{id}', name: 'app_gift_list_show', methods: ['GET'])]
    public function show(GiftList $giftList, Request $request): Response
    {
        $currentDate = new \DateTime();

        // Vérification de l'activité
        if (!$giftList->isIsActive() && $this->getUser() !== $giftList->getUser()) {
            $this->addFlash('error', 'La liste de cadeaux n\'est pas active.');
            return $this->redirectToRoute('front_app_gift_list_index');
        }

        if (($giftList->getDateOuverture() && $currentDate < $giftList->getDateOuverture()) || 
        ($giftList->getDateFinOuverture() && $currentDate > $giftList->getDateFinOuverture())) {
            $this->addFlash('error', 'La liste de cadeaux n\'est pas accessible à cette date.');
            return $this->redirectToRoute('front_app_gift_list_index');
         }
        // Si la liste est privée
        if ($giftList->isIsPrivate()) {

            if ($request->getSession()->get('validated_gift_list_' . $giftList->getId())) {
                // Si le mot de passe est correct, affichez la liste
                $request->getSession()->remove('validated_gift_list_' . $giftList->getId());

                return $this->render('front/gift_list/show_private.html.twig', [
                    'giftList' => $giftList,
                ]);
            } else {
                $this->addFlash('error', 'Mot de passe incorrect.');
            }

            // Si le mot de passe n'a pas été soumis ou est incorrect, affichez le formulaire de mot de passe
            return $this->render('front/gift_list/password_form.html.twig', [
                'giftList' => $giftList,
            ]);
        }

        // Si la liste est publique
        return $this->render('front/gift_list/show.html.twig', [
            'giftList' => $giftList,
        ]);
    }

    #[Route('/{id}/check-password', name: 'app_gift_list_check_password', methods: ['POST'])]
    public function checkPassword(GiftList $giftList, Request $request): Response
    {
        $submittedPassword = $request->request->get('password');

        if ($submittedPassword && $submittedPassword === $giftList->getPassword()) {
            // Si le mot de passe est correct, affichez la liste privée
            $request->getSession()->set('validated_gift_list_' . $giftList->getId(), true);
            return $this->redirectToRoute('front_app_gift_list_show', ['id' => $giftList->getId()]);
        }

        $this->addFlash('error', 'Mot de passe incorrect.');
        return $this->redirectToRoute('front_app_gift_list_show', ['id' => $giftList->getId()]);
    }

    #[Route('/{id}/edit', name: 'app_gift_list_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GiftList $giftList, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== $giftList->getUser()) {
            $this->addFlash('error', 'Vous n\'avez pas le droit de modifier cette liste.');
            return $this->redirectToRoute('app_gift_list_index');
        }


        $form = $this->createForm(GiftListType::class, $giftList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($giftList);
            $entityManager->flush();

            return $this->redirectToRoute('front_app_gift_list_index');
        }

        return $this->render('front/gift_list/edit.html.twig', [
            'giftList' => $giftList,
            'form' => $form->createView(),
        ]);
    }
    
    
    #[Route('/{id}/archive', name: 'app_gift_list_archive', methods: ['GET', 'POST'])]
    public function archive(GiftList $giftList, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur courant est le propriétaire de la liste de cadeaux
        if ($this->getUser() !== $giftList->getUser()) {
            $this->addFlash('error', 'Vous n\'avez pas le droit d\'archiver cette liste.');
            return $this->redirectToRoute('front_app_gift_list_index');
        }

        // Modifier le statut de la liste de cadeaux
        // Cela dépend de votre logique d'entreprise, ici je suppose qu'il y a une méthode setIsActive ou setIsArchived.
        $giftList->setArchived(true); // ou setIsActive(false) selon votre logique

        // Enregistrer les changements
        $entityManager->persist($giftList);
        $entityManager->flush();

        // Ajouter un message flash et rediriger vers la liste des cadeaux
        $this->addFlash('success', 'La liste de cadeaux a été archivée avec succès.');
        return $this->redirectToRoute('front_app_gift_list_index');
    }

    #[Route('/{id}/unarchive', name: 'app_gift_list_unarchive', methods: ['GET', 'POST'])]
    public function unarchive(GiftList $giftList, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur courant est le propriétaire de la liste de cadeaux
        if ($this->getUser() !== $giftList->getUser()) {
            $this->addFlash('error', 'Vous n\'avez pas le droit de désarchiver cette liste.');
            return $this->redirectToRoute('front_app_gift_list_index');
        }

        // Modifier le statut de la liste de cadeaux
        // Cela dépend de votre logique d'entreprise, ici je suppose qu'il y a une méthode setIsActive ou setIsArchived.
        $giftList->setArchived(false); // ou setIsActive(false) selon votre logique

        // Enregistrer les changements
        $entityManager->persist($giftList);
        $entityManager->flush();

        // Ajouter un message flash et rediriger vers la liste des cadeaux
        $this->addFlash('success', 'La liste de cadeaux a été désarchivée avec succès.');
        return $this->redirectToRoute('front_app_gift_list_index');
    }

    #[Route('/{id}/delete', name: 'app_gift_list_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, GiftList $giftList, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur courant est le propriétaire de la liste de cadeaux
        if ($this->getUser() !== $giftList->getUser()) {
            $this->addFlash('error', 'Vous n\'avez pas le droit de supprimer cette liste.');
            return $this->redirectToRoute('front_app_gift_list_index');
        }

        if ($this->isCsrfTokenValid('delete' . $giftList->getId(), $request->request->get('_token'))) {
            $entityManager->remove($giftList);
            $entityManager->flush();
            $this->addFlash('success', 'La liste de cadeaux a été supprimée.');
        }

        return $this->redirectToRoute('front_app_gift_list_index');
    }

}
