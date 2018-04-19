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
    public const TID_INDEX_NAME = 'tid';
    public const COUNT_INDEX_NAME = 'countQuery';
    public const MAX_COUNT_QUERY = 5;

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

            if (isset($body[self::COUNT_INDEX_NAME]) && $body[self::COUNT_INDEX_NAME] > self::MAX_COUNT_QUERY) {
                return true;
            }

            if (!$this->transaction->process($depositDto)) {
                $body[self::COUNT_INDEX_NAME] = ($body[self::COUNT_INDEX_NAME] ?? 1) + 1;
                $this->delayedProducer->publish(serialize($body), $depositDto->getType());
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
