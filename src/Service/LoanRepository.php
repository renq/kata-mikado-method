<?php

namespace App\Service;

interface LoanRepository
{
    public static function fetch(string|int $ticketId): LoanApplication;

    public static function store(LoanApplication $application): Ticket;

    public static function approve(string $ticketId): Ticket;
}