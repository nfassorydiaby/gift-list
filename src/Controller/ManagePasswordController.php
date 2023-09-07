<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

#[Route('/manage-password')]
class ManagePasswordController extends AbstractController
{

    private EmailVerifier $emailVerifier;
    private EmailService $emailService;

    public function __construct(EmailVerifier $emailVerifier, EmailService $emailService)
    {
        $this->emailVerifier = $emailVerifier;
        $this->emailService = $emailService;
    }

    #[Route(path: '/', name: 'app_default_manage_password')]
    public function defaultManagePassword(AuthenticationUtils $authenticationUtils): Response
    {

        return $this->render('security/manage-password-default.html.twig', []);
    }

    #[Route(path: '/check-email', name: 'app_check_email')]
    public function checkEmail(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, VerifyEmailHelperInterface $verifyEmailHelper): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');

            // Check if the email exists in the user table
            $user = $userRepository->findOneBy(['email' => $email]);

            if ($user) {

                $token = bin2hex(random_bytes(32));

                // Store the token in the user's entity
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();

                $resetUrl = $this->generateUrl('app_reset_password_forgot', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                $destinator = $user->getEmail();
                $htmlContent = $this->renderView('security/manage-credentials/confirmation_reset_password.html.twig') . '<a href=' . $resetUrl . '>Réinitialiser</a>';
                $subject = 'Réinitialisation du mot de passe';

                $this->emailService->sendVerificationEmail($destinator, $subject, $htmlContent);

                return $this->render('security/manage-credentials/forgot-password-template.html.twig', [
                ]);
            }else{
                $this->addFlash('failure', 'Email incorrecte');
                return $this->redirectToRoute('app_check_email');
            }
        }

        return $this->render('security/manage-credentials/check-email.html.twig', []);
    }

    #[Route('/verify/email', name: 'app_verify_email_forgot_password')]
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link
        try {
            $this->emailVerifier->handleEmailConfirmationPassword($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_login');
        }

        return $this->redirectToRoute('app_reset_password_forgot', array('id' => $id));

        //return $this->render('security/manage-credentials/reset-password-forgot.html.twig', ['email' => $user->getEmail(), 'id' => $user->getId()]);
    }


    #[Route('/reset-password-forgot/{token}', name: 'app_reset_password_forgot')]
    public function resetPasswordForgot(string $token, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        // On recherche l'utilisateur avec le token
        $user = $userRepository->findOneBy(['resetToken' => $token]);

        if (!$user) {
            $this->addFlash('failure', 'Token invalide ou expiré');
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('password');
            if (!$newPassword) {
                $this->addFlash('failure', 'Le mot de passe ne peut pas être vide');
                return $this->redirectToRoute('app_reset_password_forgot', ['token' => $token]);
            }

            // Vérification du mot de passe
            if (strlen($newPassword) < 8 ||
                !preg_match("/[A-Z]/", $newPassword) ||
                !preg_match("/[a-z]/", $newPassword) ||
                !preg_match("/[0-9]/", $newPassword) ||
                !preg_match("/[@$!%*#?&]/", $newPassword))
            {
                $this->addFlash('failure', 'Le mot de passe doit avoir au moins 8 caractères, contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial (@$!%*#?&).');
                return $this->redirectToRoute('app_reset_password_forgot', ['token' => $token]);
            }

            // On réinitialise le mot de passe et le token
            $user->setPassword($userPasswordHasher->hashPassword($user, $newPassword));
            $user->setResetToken(null);  // important pour éviter la réutilisation du lien

            $entityManager->persist($user);
            $entityManager->flush();

            // Redirection vers la page de connexion avec un message de succès
            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/manage-credentials/reset-password-forgot.html.twig', ['email' => $user->getEmail(), 'token' => $token]);
    }

}

