<?php

namespace App\Tests\Service;

use App\Service\ApplicationException;
use App\Service\InMemoryLoanRepository;
use App\Service\LoanApplication;
use PHPUnit\Framework\TestCase;

class InMemoryLoanRepositoryTest extends TestCase
{
    public function testFetch(): void
    {
        $inMemoryLoanRepository = new InMemoryLoanRepository();
        $this->expectException(ApplicationException::class);
        $inMemoryLoanRepository->fetch(1);
    }

    public function testStoreAndFetchResult(): void
    {
        // Arrange
        $repository = new InMemoryLoanRepository();
        $loanApplication = new LoanApplication();

        // Act
        $ticket = $repository->store($loanApplication);

        // Assert
        self::assertEquals($loanApplication, $repository->fetch($ticket->getId()));
    }
}
