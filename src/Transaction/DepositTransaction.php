<?php

declare(strict_types=1);

namespace App\Transaction;

use App\Consumer\Dto\OperationDto;

class DepositTransaction extends AbstractTransaction
{
    protected function transaction(OperationDto $operationDto): void
    {
        $recipientAccount = $this->getAccount($operationDto->getRecipient());

        $accountingTransaction = $this->creator->createAccountingTransaction(
            $operationDto->getType(),
            $operationDto->getTid(),
            $operationDto->getAmount(),
            null,
            $recipientAccount
        );

        $accountingEntry = $this->creator->createAccountingEntry($recipientAccount, $accountingTransaction, $operationDto->getAmount());
        $recipientAccount->calculateBalance($accountingEntry->getAmount());

        $this->em->persist($accountingTransaction);
        $this->em->persist($accountingEntry);
    }
}
