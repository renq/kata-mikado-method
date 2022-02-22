<?php

declare(strict_types=1);

namespace App\Service;

class LoanApplication
{
    private int $applicationNo;
    private int $amount;
    private string $contact = '';
    private bool $approved = false;

    public function __construct()
    {
        $this->applicationNo = FileSystemLoanRepository::getNextId();
    }

    public function getApplicationNo(): int
    {
        return $this->applicationNo;
    }

    public function setApplicationNo(int $applicationNo): void
    {
        $this->applicationNo = $applicationNo;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getContact(): string
    {
        return $this->contact;
    }

    public function setContact(string $contact): void
    {
        $this->contact = $contact;
    }

    public function approve(): void
    {
        $this->setApproved(true);
    }

    public function isApproved(): bool
    {
        return $this->approved;
    }

    public function setApproved(bool $approved): void
    {
        $this->approved = $approved;
    }
}
