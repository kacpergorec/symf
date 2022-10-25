<?php
declare (strict_types=1);

namespace App\Service;

use App\Exception\NotImplementedHttpException;
use Exception;

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
    private const DEFAULT_ALPHABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    private string $alphabet;

    private int $alphabetLength;

    private int $tokenLength = 5;

    private int $outcomesCount;

    public function __construct(string $alphabet = self::DEFAULT_ALPHABET, int $tokenLength = 5)
    {
        $this->setAlphabet($alphabet);
        $this->setTokenLength($tokenLength);
    }

    /**
     * @return string
     * Returns a pseudo-unique token that is generated from the given alphabet.
     * Subtracting 1 from alphabet length is needed due to alphabet stringarray last char key.
     */
    public function generate(): string
    {
        $token = '';

        for ($i = 0; $i < $this->tokenLength; $i++) {

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
        } catch (Exception) {
            throw new NotImplementedHttpException('Randomness entropy is not defined in server');
        }

        return $this->alphabet[$randomKey];
    }

    public function setAlphabet(string $alphabet): self
    {
        $this->alphabet = count_chars($alphabet, 3); // Make sure letters dont repeat
        $this->alphabetLength = strlen($this->alphabet);
        $this->setOutcomesCount();

        return $this;
    }

    public function setTokenLength(int $tokenLength): self
    {
        $this->tokenLength = $tokenLength;
        $this->setOutcomesCount();

        return $this;
    }

    /**
     * This is used when all possible keys at current key length has been used
     * and we need to generate new unique ones.
     *
     * @return $this
     */
    public function incrementTokenLength(): self
    {
        $tokenLength = $this->tokenLength + 1;

        $this->setTokenLength($tokenLength);

        return $this;
    }

    /**
     * Sets all possible outcomes count of current generation config.
     * It's a fairly simple equation. Count of alphabet chars to the power of current token length.
     *
     * @return $this
     */
    public function setOutcomesCount(): self
    {
        $this->outcomesCount = strlen($this->alphabet) ** $this->tokenLength;

        return $this;
    }

    public function getOutcomesCount(): int
    {
        return $this->outcomesCount;
    }

}