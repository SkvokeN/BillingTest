<?php

declare(strict_types=1);

namespace App\Tests\Transaction;

use App\Consumer\Dto\CreatorDtoForTransfer;
use App\Entity\Account;
use App\Transaction\TransferTransaction;

class TransferTransactionTest extends AbstractTransaction
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
        $debitTransaction = new TransferTransaction($this->em, $this->creator, $this->logger, $this->dtoValidator);
        $testData = ['senderId' => 1, 'tid' => 'randomString', 'recipientId' => 2, 'billingType' => 'debit', 'amount' => 100];
        $result = $debitTransaction->process((new CreatorDtoForTransfer())->createDto($testData));

        $this->assertEquals($result, $actualResult);
    }

    public function dataForProcessTest(): \Generator
    {
        yield 'No account' => [null, 0, false];
        yield 'Not enough balance' => [new Account(1, 99), 0, false];
        yield 'Success' => [new Account(1, 200), 3, true];
    }
}
