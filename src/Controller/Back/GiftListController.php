<?php 

// src/Controller/Back/GiftListController.php
namespace App\Controller\Back;

use App\Entity\GiftList;
use App\Form\GiftListType;
use App\Repository\GiftListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN")
 */
#[Route('/gift-list')]
class GiftListController extends AbstractController
{
    
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/new', name: 'admin_gift_list_new', methods: ['GET', 'POST'])]
    public function new(Request $request,  EntityManagerInterface $entityManager): Response
    {
        $giftList = new GiftList();
        $form = $this->createForm(GiftListType::class, $giftList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($giftList);
            $entityManager->flush();

            return $this->redirectToRoute('back_admin_user_index');
        }

        return $this->render('back/gift_list/new.html.twig', [
            'gift_list' => $giftList,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/{id}/edit', name: 'admin_gift_list_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GiftList $giftList, EntityManagerInterface $entityManager): Response
    {
        $giftList = new GiftList();

        $form = $this->createForm(GiftListType::class, $giftList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($giftList);
            $entityManager->flush();

            return $this->redirectToRoute('back_admin_gift_list_index');
        }

        return $this->render('back/gift_list/edit.html.twig', [
            'gift_list' => $giftList,
            'form' => $form->createView(),
        ]);
    }

   /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/{id}', name: 'admin_gift_list_delete', methods: ['POST'])]
    public function delete(Request $request, GiftList $giftList, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$giftList->getId(), $request->request->get('_token'))) {

            $entityManager->remove($giftList);
            $entityManager->flush();
        }

        return $this->redirectToRoute('back_admin_gift_list_index');
    }
}
