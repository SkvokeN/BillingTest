<?php

declare(strict_types=1);

namespace App\Validator;

use App\Consumer\Dto\OperationDto;
use App\Entity\AccountingTransaction;
use App\Exception\DuplicateTransactionIdException;
use Doctrine\ORM\EntityManagerInterface;

class TransactionIdValidator implements OperationDtoValidatorInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function validate(OperationDto $operationDto): bool
    {
        $transaction = $this->em->getRepository(AccountingTransaction::class)->findOneBy(['tid' => $operationDto->getTid()]);

        if ($transaction) {
            throw new DuplicateTransactionIdException('Duplicate transaction_id(' . $operationDto->getTid() . ')');
        }

        return true;
    }
}
