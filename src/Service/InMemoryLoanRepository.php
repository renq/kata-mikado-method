<?php

namespace App\Service;

class InMemoryLoanRepository implements LoanRepository
{

    public function fetch(int|string $ticketId): LoanApplication
    {
        throw new ApplicationException('Ticket not found');
    }

    public function store(LoanApplication $application): Ticket
    {
        // TODO: Implement store() method.
    }

    public function approve(string $ticketId): Ticket
    {
        // TODO: Implement approve() method.
    }
}