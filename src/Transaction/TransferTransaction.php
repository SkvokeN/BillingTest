<?php

declare(strict_types=1);

namespace App\Transaction;

use App\Consumer\Dto\OperationDto;
use App\Exception\NotEnoughBalance;

class TransferTransaction extends AbstractTransaction
{
    protected function transaction(OperationDto $operationDto): void
    {
        $senderAccount = $this->getAccount($operationDto->getSender());

        if ($senderAccount->getBalance() < $operationDto->getAmount()) {
            throw new NotEnoughBalance('User with userId = ' . $senderAccount->getUserId() . '  hasn\'t enough money');
        }

        $recipientAccount = $this->getAccount($operationDto->getRecipient());

        $accountingTransaction = $this->creator->createAccountingTransaction(
            $operationDto->getType(),
            $operationDto->getAmount(),
            $senderAccount,
            $recipientAccount
        );

        $accountingEntrySender = $this->creator->createAccountingEntry($senderAccount, $accountingTransaction, -$operationDto->getAmount());
        $senderAccount->calculateBalance($accountingEntrySender->getAmount());

        $accountingEntryRecipient = $this->creator->createAccountingEntry($recipientAccount, $accountingTransaction, $operationDto->getAmount());
        $recipientAccount->calculateBalance($accountingEntryRecipient->getAmount());

        $this->em->persist($accountingTransaction);
        $this->em->persist($accountingEntrySender);
        $this->em->persist($accountingEntryRecipient);
    }
}
