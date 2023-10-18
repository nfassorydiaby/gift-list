<?php

namespace App\Service;

use Exception;
use SendGrid;
use SendGrid\Mail\Mail;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailService
{
    private VerifyEmailHelperInterface $verifyEmailHelper;
    private string $sendGridApiKey;

    public function __construct(VerifyEmailHelperInterface $verifyEmailHelper, string $sendGridApiKey)
    {
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->sendGridApiKey = $sendGridApiKey;
    }

    public function sendVerificationEmail($destinator, $subject, $htmlContent): void
    {

        $email = new Mail();
        $email->setFrom('ndiaby6@myges.fr', 'Gift Online');
        $email->setSubject($subject);
        $email->addTo($destinator);
        $email->addContent("text/html", $htmlContent);

        // Headers
        $email->addHeaders([
            'MIME-version' => '1.0',
            'Date' => date('r'),
            'X-Mailer' => 'PHP/' . phpversion()
        ]);

        $sendgrid = new SendGrid($this->sendGridApiKey);
        try {
            $response = $sendgrid->send($email);

            if ($response->statusCode() != 202) {
                throw new Exception('Failed to send email: ' . $response->body());
            }
        } catch (Exception $e) {
            echo 'Exception when sending email: ', $e->getMessage(), PHP_EOL;
        }
    }
}
