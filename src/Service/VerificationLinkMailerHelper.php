<?php
declare (strict_types=1);

namespace App\Service;


use App\Entity\User;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class VerificationLinkMailerHelper implements ErrorHandlerInterface
{
    private MailerInterface $mailer;
    private VerifyEmailHelperInterface $verifyEmailHelper;
    private string $appMailFrom;
    private array $errors = [];

    public function __construct(MailerInterface $mailer, VerifyEmailHelperInterface $verifyEmailHelper, string $appMailFrom)
    {
        $this->mailer = $mailer;
        $this->verifyEmailHelper = $verifyEmailHelper;

        //ENV VARIABLE
        $this->appMailFrom = $appMailFrom;
    }

    public function send(User $user): bool
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'app_verify_email',
            (string)$user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()]
        );

        $activationEmail = (new TemplatedEmail())
            ->from($this->appMailFrom)
            ->to($user->getEmail())
            ->subject('Confirmation Email - Symf')
            ->htmlTemplate('emails/verification.html.twig')
            ->context([
                'user' => $user,
                'activationLink' => $signatureComponents->getSignedUrl()
            ]);

        try {
            $this->mailer->send($activationEmail);
            return true;
        } catch (TransportExceptionInterface) {
            return false;
        }
    }

    public function addError(Exception $error): void
    {
        if (method_exists($error, 'getReason')) {
            $errorMessage = $error->getReason();
        } else {
            $errorMessage = $error->getMessage();
        }

        $this->errors[] = $errorMessage;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}