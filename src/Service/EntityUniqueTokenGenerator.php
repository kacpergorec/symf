<?php
declare (strict_types=1);

namespace App\Service;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class EntityUniqueTokenGenerator
{
    public function __construct(
        private UniqueTokenGenerator $generator,
    )
    {
    }

    /**
     * This is a temporary solution.
     *
     * The problem here is that when the record limit for given length is peaked,
     * Every new request, counting will start from here and check a LOT of records.
     *
     * The incremented tokenLength value should be stored somewhere (ex. database) and incremented once
     * every time the limit is peaked.
     */
    public function generateUniqueToken(int $tokenLength, ServiceEntityRepository $repository, $dbColumn = 'shortKey'): string
    {

        $this->generator->setTokenLength($tokenLength);

        $i = 0;
        do {
            if ($this->generator->getOutcomesCount() === $i) {
                $this->generator->incrementTokenLength();
            }

            $uniqueKey = $this->generator->generate();

            $i++;
        } while ($repository->findOneBy([$dbColumn => $uniqueKey]));


        return $uniqueKey;
    }
}