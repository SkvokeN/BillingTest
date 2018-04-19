<?php

declare(strict_types=1);

namespace App\Transaction\Creators;

use App\Entity\Account;
use App\Entity\AccountingEntry;
use App\Entity\AccountingTransaction;

interface EntityForTransactionCreatorInterface
{
    public function createAccountingTransaction(string $type, string $tid, int $amount, ?Account $sender, ?Account $recipient): AccountingTransaction;

    public function createAccountingEntry(Account $account, AccountingTransaction $accountingTransaction, int $amount): AccountingEntry;
}
