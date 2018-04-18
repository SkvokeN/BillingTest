<?php

declare(strict_types=1);

namespace App\Transaction;

use App\Consumer\Dto\OperationDto;
use App\Entity\Account;
use App\Exception\UserNotExistException;
use App\Transaction\Creators\EntityForTransactionCreatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractTransaction
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var EntityForTransactionCreatorInterface
     */
    protected $creator;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(EntityManagerInterface $em, EntityForTransactionCreatorInterface $creator, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->creator = $creator;
        $this->logger = $logger;
    }

    public function process(OperationDto $operationDto): bool
    {
        $this->em->getConnection()->beginTransaction();

        try {
            $this->transaction($operationDto);
            $this->em->flush();
            $this->em->getConnection()->commit();
            $this->em->clear();
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            $this->logger->error($e->getMessage());

            return false;
        }

        return true;
    }

    abstract protected function transaction(OperationDto $operationDto): void;

    protected function getAccount($userId): Account
    {
        $account = $this->em->getRepository(Account::class)->getAccountForUpdate($userId);

        if (!$account) {
            throw new UserNotExistException('User\'s account with userId = ' . $userId . '  doesn\'t exist');
        }

        return $account;
    }
}
