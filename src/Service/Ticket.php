<?php

declare(strict_types=1);

namespace App\Service;

class Ticket
{
    private int $id;

    public function __construct(int $applicationNo)
    {
        $this->id = $applicationNo;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
