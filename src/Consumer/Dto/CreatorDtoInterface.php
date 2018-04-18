<?php

declare(strict_types=1);

namespace App\Consumer\Dto;

interface CreatorDtoInterface
{
    public function createDto(array $data): OperationDto;
}
