<?php
declare (strict_types=1);

namespace App\Service;


use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class VerifyURLValidator
{

    private User $user;
    private UserRepository $userRepository;
    private VerifyEmailHelperInterface $verifyEmailHelper;
    private array $errors = [];
    private bool $isValid;

    public function __construct(UserRepository $userRepository, VerifyEmailHelperInterface $verifyEmailHelper)
    {

        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->userRepository = $userRepository;
    }

    public function validate(Request $request): bool
    {

        try {
            $user = $this->userRepository->find($request->query->get('id'));
        } catch (\Exception $e) {
            $this->addError(new \Exception('Activation link is not valid.'));
            $this->setIsValid(false);
            return false;
        }

        if (!$user) {
            $this->addError(new UserNotFoundException('User associated with this activation link was not found.'));
            $this->setIsValid(false);
            return false;
        }


        try {
            $this->verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                (string)$user->getId(),
                $user->getEmail()
            );
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addError($e);
            $this->setIsValid(false);
            return false;
        }

        $this->setIsValid(true);
        $this->setUser($user);

        return $this->isValid();
    }

    private function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    private function addError(\Exception $error): void
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

    private function setIsValid(bool $value): void
    {
        $this->isValid = $value;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }
}