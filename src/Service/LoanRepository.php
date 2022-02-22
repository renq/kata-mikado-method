<?php

namespace App\Service;

interface LoanRepository
{
    public function getNextId(): int;

    public function store(LoanApplication $application): Ticket;

    public function fetch(string|int $ticketId): LoanApplication;

    public function approve(string $ticketId): Ticket;
}