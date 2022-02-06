<?php

declare(strict_types=1);

namespace App\Service;

interface LoanRepository
{
    public static function getNextId(): int;

    /**
     * @throws ApplicationException
     */
    public function fetch(string|int $ticketId): LoanApplication;

    public function store(LoanApplication $application): Ticket;

    public function approve(string $ticketId): Ticket;
}