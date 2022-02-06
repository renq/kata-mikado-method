<?php
declare(strict_types=1);

namespace App\Service;

class MemoryLoanRepository implements LoanRepository
{
    private array $data = [];

    public static function getNextId(): int
    {
        static $i = 0;

        return ++$i;
    }

    public function fetch(int|string $ticketId): LoanApplication
    {
        return $this->data[(int)$ticketId] ?? throw new ApplicationException('Ticket not found');
    }

    public function store(LoanApplication $application): Ticket
    {
        $this->data[$application->getApplicationNo()] = $application;

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
