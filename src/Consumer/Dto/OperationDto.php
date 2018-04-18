<?php

declare(strict_types=1);

namespace App\Consumer\Dto;

class OperationDto
{
    /**
     * @var int
     */
    private $amount;

    /**
     * @var string
     */
    private $type;

    /**
     * @var int|null
     */
    private $sender;
    /**
     * @var int|null
     */
    private $recipient;

    public function __construct(int $amount, string $type, ?int $sender, ?int $recipient)
    {
        $this->amount = $amount;
        $this->type = $type;
        $this->sender = $sender;
        $this->recipient = $recipient;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return int|null
     */
    public function getSender(): ?int
    {
        return $this->sender;
    }

    /**
     * @return int|null
     */
    public function getRecipient(): ?int
    {
        return $this->recipient;
    }
}
