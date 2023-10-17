<?php 

// src/Controller/GiftController.php

namespace App\Controller\Front;

use App\Entity\Gift;
use App\Form\GiftType;
use App\Service\ProductScraper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GiftController extends AbstractController
{
    /**
     * @Route("/gift/new", name="gift_new", methods={"GET","POST"})
     */
    public function new(Request $request, ProductScraper $scraper): Response
    {
        $gift = new Gift();
        $form = $this->createForm(GiftType::class, $gift);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // Scraping de données de produit
            $productData = $scraper->scrapeProduct($gift->getLienAchat());

            // Remplir les champs manquants de l'entité Gift
            $gift->setNom($productData['nom']);
            $gift->setImage($productData['image']);
            $gift->setPrix($productData['prix']);

            $entityManager->persist($gift);
            $entityManager->flush();

            return $this->redirectToRoute('gift_index');
        }

        return $this->render('gift/new.html.twig', [
            'gift' => $gift,
            'form' => $form->createView(),
        ]);
    }
}
