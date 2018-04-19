<?php

declare(strict_types=1);

namespace App\Validator;

use App\Consumer\Dto\OperationDto;

interface OperationDtoValidatorInterface
{
    public function validate(OperationDto $operationDto): bool;
}
