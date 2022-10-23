<?php
declare (strict_types=1);

namespace App\Service;

use App\Exception\NotImplementedHttpException;

/**
 * Class UIDGenerator.
 *
 * This class is used to generate short pseudo-unique tokens.
 * You can specify the length of the token and your own set of characters by setting an alphabet.
 *
 * There are 14,776,336 possible combinations with the default alphabet (62 characters).
 * For example with key length of 5 and a default alphabet you have approx. 99.99994% chance of getting unique id.
 *
 * @package App\Service
 *
 * @author Kacper Górec
 */
class UniqueTokenGenerator
{

    /**
     * This is the default alphabet that UIDGenerator It contains numbers [0-9], and letters [a-z] , [A-Z]
     * @var string
     */
    private const DEFAULT_ALPHABET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    private string $alphabet;

    private int $alphabetLength;

    public function __construct(string $alphabet = self::DEFAULT_ALPHABET)
    {
        $this->setAlphabet($alphabet);
    }

    /**
     * @param int $length
     * @return string
     * Returns a pseudo-unique token that is generated from the given alphabet.
     * Subtracting 1 from alphabet length is needed due to alphabet stringarray last char key.
     */
    public function generate(int $length = 5): string
    {
        $token = '';

        for ($i = 0; $i < $length; $i++) {

            $token .= $this->generateRandomLetter();

        }

        return $token;
    }

    /**
     * @return string
     * @throws NotImplementedHttpException
     * Throws an exception with server error 501 status code when server does not operate with randomness source.
     */
    private function generateRandomLetter(): string
    {
        try {
            $randomKey = random_int(0, $this->alphabetLength - 1);
        } catch (\Exception $e) {
            throw new NotImplementedHttpException('Random');
        }

        return $this->alphabet[$randomKey];
    }

    public function setAlphabet(string $alphabet): void
    {
        $this->alphabet = $alphabet;
        $this->alphabetLength = strlen($alphabet);
    }

}