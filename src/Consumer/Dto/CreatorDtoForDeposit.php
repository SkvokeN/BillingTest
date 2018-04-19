<?php

declare(strict_types=1);

namespace App\Consumer\Dto;

use App\Consumer\AccountOperationConsumer;

class CreatorDtoForDeposit implements CreatorDtoInterface
{
    public function createDto(array $data): OperationDto
    {
        return new OperationDto(
            $data[AccountOperationConsumer::AMOUNT_INDEX_NAME],
            $data[AccountOperationConsumer::TYPE_INDEX_NAME],
            $data[AccountOperationConsumer::TID_INDEX_NAME],
            null,
            $data[AccountOperationConsumer::RECIPIENT_INDEX_NAME]
        );
    }
}
