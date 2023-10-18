<?php

namespace App\Controller\Front;

use App\Entity\AccessToken;
use App\Entity\Gift;
use App\Entity\GiftList;
use App\Form\GiftType;
use App\Form\GiftListType;
use App\Service\ProductScraper;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;



#[Route('/gift-list')]
class GiftListController extends AbstractController
{
    private $productScraper;
    private $tokenService;


    public function __construct(ProductScraper $productScraper, TokenService $tokenService)
    {
        $this->productScraper = $productScraper;
        $this->tokenService = $tokenService;
    }

    /**
     * @IsGranted("PUBLIC_ACCESS")
     */
    #[Route('/', name: 'app_gift_list_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $giftLists = $entityManager->getRepository(GiftList::class)->findAll();

        return $this->render('front/gift_list/index.html.twig', [
            'giftLists' => $giftLists,
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     */
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
    /**
     * @IsGranted("PUBLIC_ACCESS")
     */
    #[Route('/{id}', name: 'app_gift_list_show', methods: ['GET', 'POST'])]
    public function show(GiftList $giftList, Request $request, EntityManagerInterface $entityManager): Response
    {
        $currentDate = new \DateTime();

        // Vérification de l'activité
        if (!$giftList->isIsActive()) {
            $this->addFlash('error', 'La liste de cadeaux n\'est pas active.');
            return $this->redirectToRoute('front_app_gift_list_index');
        }

        if (($giftList->getDateOuverture() && $currentDate < $giftList->getDateOuverture()) || 
        ($giftList->getDateFinOuverture() && $currentDate > $giftList->getDateFinOuverture())) {
            $this->addFlash('error', 'La liste de cadeaux n\'est pas accessible à cette date.');
            return $this->redirectToRoute('front_app_gift_list_index');
         }

         // Création d'un formulaire pour ajouter un cadeau via une URL
         $gift = new Gift();
         $form = $this->createForm(GiftType::class, $gift);
         $form->handleRequest($request);
         
         if ($form->isSubmitted() && $form->isValid()) {
             // Récupérer l'URL du formulaire
            $url = $gift->getLienAchat();

            // Utiliser le service pour scraper les informations du produit
            try {
                $productInfo = $this->productScraper->scrapeProduct($url);

                // Définir les propriétés du cadeau
                $gift->setNom($productInfo['nom']);
                $gift->setPrix($productInfo['prix']);
                $gift->setGiftList($giftList);

                $imageUrl = $productInfo['image']; // assurez-vous que cette URL est correcte
                $gift->setImage($imageUrl); // Utilisez la méthode appropriée pour définir l'URL de l'image dans votre entité

                            $entityManager->persist($gift);
                $entityManager->flush();

                $this->addFlash('success', 'Le cadeau a été ajouté avec succès.');
            } catch (\Exception $e) {

                // Gérer les erreurs de scraping ou autres exceptions ici
                $this->addFlash('error', 'Une erreur est survenue lors de la récupération des informations sur le produit.');
            }

            // Rediriger vers la même page pour éviter les soumissions de formulaire en double
            return $this->redirectToRoute('front_app_gift_list_show', ['id' => $giftList->getId()]);

         }

         // Si la liste est privée
        if ($giftList->isIsPrivate()) {

            if ($request->getSession()->get('validated_gift_list_' . $giftList->getId())) {
                // Si le mot de passe est correct, affichez la liste

                return $this->render('front/gift_list/show_private.html.twig', [
                    'giftList' => $giftList,
                    'urlForm' => $form->createView(), // Passez le formulaire à la vue
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
            'urlForm' => $form->createView(), // Passez le formulaire à la vue
        ]);
    }

    /**
     * @IsGranted("PUBLIC_ACCESS")
     */
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

    /**
     * @IsGranted("ROLE_USER")
     */
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
    
    /**
     * @IsGranted("ROLE_USER")
     */
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

    /**
     * @IsGranted("ROLE_USER")
     */
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

    /**
     * @IsGranted("ROLE_USER")
     */
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

    /**
     * @IsGranted("ROLE_USER")
     */
    #[Route('/{id}/share', name: 'app_gift_list_share', methods: ['GET'])]
    public function share(int $id, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): Response
    {
        $giftList = $entityManager->getRepository(GiftList::class)->find($id);

        if (!$giftList) {
            throw $this->createNotFoundException('La liste de cadeaux demandée n\'existe pas');
        }

        // Vérifiez que l'utilisateur actuel est le propriétaire de la liste de cadeaux
        if ($this->getUser() !== $giftList->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à partager cette liste de cadeaux.');
        }

        // Créez un jeton unique pour cette action de partage
        $token = bin2hex(random_bytes(32)); // Génère un token sécurisé

        // Sauvegardez ce jeton dans la base de données en l'associant à la liste de cadeaux
        $accessToken = new AccessToken();
        $accessToken->setToken($token);
        $accessToken->setGiftList($giftList);
        $accessToken->setCreatedAt(new \DateTime());
        // Vous pouvez également définir une date d'expiration pour le token ici, si nécessaire

        $entityManager->persist($accessToken);
        $entityManager->flush();
        // Générer l'URL de partage qui inclut le jeton
        $shareUrl = $urlGenerator->generate('front_app_gift_list_share_access', [
            'token' => $token,
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        // Ajoutez un message flash avec l'URL de partage
        $this->addFlash('share_url', $shareUrl);

        // Redirigez vers la page de la liste de cadeaux
        return $this->redirectToRoute('front_app_gift_list_show', ['id' => $giftList->getId()]);
    }   

    /**
     * @IsGranted("PUBLIC_ACCESS")
     */
    #[Route('/share/access/{token}', name: 'app_gift_list_share_access', methods: ['GET'])]
    public function accessGiftListByToken(Request $request, string $token): Response
    {
        $accessToken = $this->tokenService->verifyToken($token);

        if ($accessToken) {
            $giftList = $accessToken->getGiftList();

            $giftListDetails = [
                'titre' => $giftList->getTitre(), // Assurez-vous que la méthode getTitre() existe dans votre entité GiftList
                'link' => $this->generateUrl('front_app_gift_list_show', [
                    'id' => $giftList->getId() // ou tout autre paramètre nécessaire pour la route
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                'password' => $giftList->getPassword() // Assurez-vous que la méthode getPassword() existe si votre GiftList a un mot de passe
            ];

            return $this->render('front/gift_list/show_shared_gift_list.html.twig', ['giftList' => $giftListDetails]);
        }

        // Si le token est invalide, ajoutez un message flash d'erreur et redirigez vers la route désirée.
        $this->addFlash('error', 'Ce lien n\'est plus valide.');

        return $this->redirectToRoute('front_app_gift_list_index');    }

}
