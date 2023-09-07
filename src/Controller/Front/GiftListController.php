<?php

namespace App\Controller\Front;

use App\Entity\GiftList;
use App\Form\GiftListType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function new(Request $request): Response
    {
        $giftList = new GiftList();
        $form = $this->createForm(GiftListType::class, $giftList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($giftList);
            $entityManager->flush();

            return $this->redirectToRoute('app_gift_list_index');
        }

        return $this->render('front/gift_list/new.html.twig', [
            'giftList' => $giftList,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/{id}', name: 'app_gift_list_show', methods: ['GET'])]
    public function show(GiftList $giftList): Response
    {
        return $this->render('front/gift_list/show.html.twig', [
            'giftList' => $giftList,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gift_list_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GiftList $giftList): Response
    {
        $form = $this->createForm(GiftListType::class, $giftList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_gift_list_index');
        }

        return $this->render('front/gift_list/edit.html.twig', [
            'giftList' => $giftList,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_gift_list_delete', methods: ['POST'])]
    public function delete(Request $request, GiftList $giftList): Response
    {
        if ($this->isCsrfTokenValid('delete' . $giftList->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($giftList);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_gift_list_index');
    }

}
