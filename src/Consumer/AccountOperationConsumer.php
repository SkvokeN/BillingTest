<?php

declare(strict_types=1);

namespace App\Consumer;

use App\Consumer\Dto\CreatorDtoInterface;
use App\Transaction\AbstractTransaction;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class AccountOperationConsumer implements ConsumerInterface
{
    public const RECIPIENT_INDEX_NAME = 'recipientId';
    public const SENDER_INDEX_NAME = 'senderId';
    public const TYPE_INDEX_NAME = 'billingType';
    public const AMOUNT_INDEX_NAME = 'amount';

    /**
     * @var CreatorDtoInterface
     */
    private $creatorDto;

    /**
     * @var AbstractTransaction
     */
    private $transaction;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ProducerInterface
     */
    private $delayedProducer;

    public function __construct(ProducerInterface $delayedProducer, CreatorDtoInterface $creatorDto, AbstractTransaction $transaction, LoggerInterface $logger)
    {
        $this->creatorDto = $creatorDto;
        $this->transaction = $transaction;
        $this->logger = $logger;
        $this->delayedProducer = $delayedProducer;
    }

    public function execute(AMQPMessage $msg)
    {
        try {
            $body = unserialize($msg->getBody());
            $depositDto = $this->creatorDto->createDto($body);

            return $this->transaction->process($depositDto);

//            if (!$this->transaction->process($depositDto)) {
//                $this->delayedProducer->publish($msg->getBody(), $depositDto->getType());
//            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
