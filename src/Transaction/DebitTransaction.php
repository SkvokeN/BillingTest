<?php

declare(strict_types=1);

namespace App\Transaction;

use App\Consumer\Dto\OperationDto;
use App\Exception\NotEnoughBalance;

class DebitTransaction extends AbstractTransaction
{
    protected function transaction(OperationDto $operationDto): void
    {
        $senderAccount = $this->getAccount($operationDto->getSender());

        if ($senderAccount->getBalance() < $operationDto->getAmount()) {
            throw new NotEnoughBalance('Account with userId = ' . $senderAccount->getUserId() . '  hasn\'t enough balance');
        }

        $accountingTransaction = $this->creator->createAccountingTransaction(
            $operationDto->getType(),
            $operationDto->getTid(),
            $operationDto->getAmount(),
            $senderAccount,
            null
        );

        $accountingEntry = $this->creator->createAccountingEntry($senderAccount, $accountingTransaction, -$operationDto->getAmount());
        $senderAccount->calculateBalance($accountingEntry->getAmount());

        $this->em->persist($accountingTransaction);
        $this->em->persist($accountingEntry);
    }
}
