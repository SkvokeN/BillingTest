<?php

declare(strict_types=1);

namespace App\Tests\Transaction;

use App\Entity\Repository\AccountRepository;
use App\Transaction\Creators\EntityForTransactionCreatorInterface;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

abstract class AbstractTransaction extends TestCase
{
    /**
     * @var AccountRepository
     */
    protected $repository;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var EntityForTransactionCreatorInterface
     */
    protected $creator;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    public function setUp()
    {
        $this->logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $this->creator = $this->getMockBuilder(EntityForTransactionCreatorInterface::class)->getMock();
        $this->connection = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $this->connection->method('beginTransaction')->willReturn(true);
        $this->connection->method('commit')->willReturn(true);
        $this->connection->method('rollBack')->willReturn(true);
        $this->repository = $this->getMockBuilder(AccountRepository::class)->disableOriginalConstructor()->setMethods(['getAccountForUpdate'])->getMock();
        $this->em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $this->em->method('getRepository')->willReturn($this->repository);
        $this->em->method('getConnection')->willReturn($this->connection);
    }
}
