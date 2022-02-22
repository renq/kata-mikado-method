<?php

namespace App\Service;

class InMemoryLoanRepository implements LoanRepository
{
    private array $applications = [];

    public function getNextId(): int
    {
        return count($this->applications) + 1;
    }

    public function fetch(int|string $ticketId): LoanApplication
    {
        return $this->applications[(int)$ticketId] ?? throw new ApplicationException('Ticket not found');
    }

    public function store(LoanApplication $application): Ticket
    {
        $this->applications[$application->getApplicationNo()] = $application;

        return new Ticket($application->getApplicationNo());
    }

    public function approve(string $ticketId): Ticket
    {
        $application = $this->fetch($ticketId);
        $application->approve();
        $this->store($application);

        return new Ticket($application->getApplicationNo());
    }
}
