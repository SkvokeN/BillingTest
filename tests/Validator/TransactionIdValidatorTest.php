<?php

declare(strict_types=1);

namespace App\Tests\Validator;


use App\Consumer\Dto\CreatorDtoForDebit;
use App\Validator\TransactionIdValidator;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class TransactionIdValidatorTest extends TestCase
{
    public function testValidate()
    {
        $repository = $this->getMockBuilder(ObjectRepository::class)->getMock();
        $repository->expects($this->any())
            ->method('findOneBy')
            ->willReturn(null);
        $em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $em->method('getRepository')->willReturn($repository);
        $testData = ['senderId' => 1, 'tid' => 'randomString', 'billingType' => 'debit', 'amount' => 100];
        $validator = new TransactionIdValidator($em);

        $result = $validator->validate((new CreatorDtoForDebit())->createDto($testData));

        $this->assertEquals(true, $result);
    }
}
