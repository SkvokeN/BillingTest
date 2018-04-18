<?php

declare(strict_types=1);

namespace App\Consumer\Dto;

use App\Consumer\AccountOperationConsumer;

class CreatorDtoForTransfer implements CreatorDtoInterface
{
    public function createDto(array $data): OperationDto
    {
        return new OperationDto(
            $data[AccountOperationConsumer::AMOUNT_INDEX_NAME],
            $data[AccountOperationConsumer::TYPE_INDEX_NAME],
            $data[AccountOperationConsumer::SENDER_INDEX_NAME],
            $data[AccountOperationConsumer::RECIPIENT_INDEX_NAME]
        );
    }
}
