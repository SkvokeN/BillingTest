<?php

declare(strict_types=1);

namespace App\Transaction\Creators;

use App\Entity\Account;
use App\Entity\AccountingEntry;
use App\Entity\AccountingTransaction;

class EntityForTransactionCreator implements EntityForTransactionCreatorInterface
{
    public function createAccountingTransaction(string $type, int $amount, ?Account $sender, ?Account $recipient): AccountingTransaction
    {
        return new AccountingTransaction($type, $amount, $sender, $recipient);
    }

    public function createAccountingEntry(Account $account, AccountingTransaction $accountingTransaction, int $amount): AccountingEntry
    {
        return new AccountingEntry($account, $accountingTransaction, $amount);
    }
}
