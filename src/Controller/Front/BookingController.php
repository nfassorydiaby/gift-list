<?php 

// src/Controller/BookingController.php
namespace App\Controller\Front;


use App\Entity\Gift;
use App\Entity\BookingGift;
use App\Form\BookingGiftType;
use App\Service\EmailService;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/booking')]
class BookingController extends AbstractController
{

    private EmailVerifier $emailVerifier;
    private EmailService $emailService;

    public function __construct(EmailVerifier $emailVerifier, EmailService $emailService)
    {
        $this->emailVerifier = $emailVerifier;
        $this->emailService = $emailService;
    }

     /**
     * @IsGranted("PUBLIC_ACCESS")
     */
    #[Route('/reserve/{id}', name: 'app_booking_reserve')]
    public function reserve(Gift $gift, Request $request, EntityManagerInterface $entityManager, VerifyEmailHelperInterface $verifyEmailHelper): Response
    {
        if ($gift->isBooked()) {
            $this->addFlash('error', 'Le cadeau est déjà réservé!');
            return $this->redirectToRoute('front_app_gift_list_show', ['id' => $gift->getGiftList()->getId()]);
        }

        $booking = new BookingGift();
        $form = $this->createForm(BookingGiftType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $gift->setIsBooked(true);
            $gift->setBookedBy($this->getUser());

            // Associez la réservation au cadeau concerné
            $booking->setGift($gift);

            // ... autres initialisations ...

            $entityManager->persist($booking);
            $entityManager->persist($gift);
            $entityManager->flush();

            $giftList = $gift->getGiftList();
            $user = $giftList->getUser();
            $userEmail = $user->getEmail();

           try {
                $signatureComponents = $verifyEmailHelper->generateSignature(
                    'front_app_booking_cancel',
                    $user->getId(),
                    $user->getEmail(),
                    ['id' => $gift->getId()]
                );
                $htmlContent = $this->renderView('front/booking/confirmation_reservation_ext.html.twig', [
                    'gift' => $gift,
                    'user' => $booking,
                    'cancelUrl' => $signatureComponents->getSignedUrl()
                ]);

            
                $subject = 'Booking Gift';



                // Utilisez votre service d'email pour envoyer l'email
                $this->emailService->sendVerificationEmail($booking->getEmail(), $subject, $htmlContent);

                $this->addFlash('success', 'Le cadeau a été réservé avec succès! Un e-mail de confirmation a été envoyé.');
            } catch (\Exception $e) {
                // Log l'exception, vous pouvez également enregistrer le message d'erreur pour le débogage
                $this->addFlash('error', "Il y a eu un problème lors de l'envoi de l'e-mail de confirmation.");
            }

            return $this->redirectToRoute('front_app_gift_list_show', ['id' => $gift->getGiftList()->getId()]);

        }

        // Si le formulaire n'est pas (encore) soumis ou s'il est invalide, affichez le formulaire
        return $this->render('front/booking/book_gift.html.twig', [
            'gift' => $gift,
            'form' => $form->createView(),
        ]);
        
    }

     /**
     * @IsGranted("PUBLIC_ACCESS")
     */
    #[Route('/cancel/{id}', name: 'app_booking_cancel')]
    public function cancel(Gift $gift, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($gift->isBooked() && $this->getUser() === $gift->getBookedBy()) {
            $gift->setIsBooked(false);
            $gift->setBookedBy(null);
    
            $entityManager->persist($gift);
            $entityManager->flush();
    
            $this->addFlash('success', 'La réservation du cadeau a été annulée avec succès!');
        } else {
            $this->addFlash('error', "Vous ne pouvez pas annuler la réservation de ce cadeau.");
        }

        // Rediriger vers la page de la liste de cadeaux
        return $this->redirectToRoute('front_app_gift_list_show', ['id' => $gift->getGiftList()->getId()]);
    }
}

