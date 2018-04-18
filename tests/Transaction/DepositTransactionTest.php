<?php

declare(strict_types=1);

namespace App\Tests\Transaction;

use App\Consumer\Dto\CreatorDtoForDeposit;
use App\Entity\Account;
use App\Transaction\DepositTransaction;

class DepositTransactionTest extends AbstractTransaction
{
    /**
     * @dataProvider dataForProcessTest
     *
     * @param  Account|null $account
     * @param int $expectedPersistCount
     * @param bool $actualResult
     */
    public function testProcess(?Account $account, int $expectedPersistCount, bool $actualResult): void
    {
        $this->repository->method('getAccountForUpdate')->willReturn($account);
        $this->em->expects($this->exactly($expectedPersistCount))->method('persist');
        $debitTransaction = new DepositTransaction($this->em, $this->creator, $this->logger);
        $testData = ['recipientId' => 1, 'billingType' => 'debit', 'amount' => 100];
        $result = $debitTransaction->process((new CreatorDtoForDeposit())->createDto($testData));

        $this->assertEquals($result, $actualResult);
    }

    public function dataForProcessTest(): \Generator
    {
        yield 'No account' => [null, 0, false];
        yield 'Success' => [new Account(1, 200), 2, true];
    }
}
