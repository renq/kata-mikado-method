<?php

namespace App\Tests\Service;

use App\Service\ApplicationException;
use App\Service\InMemoryLoanRepository;
use App\Service\LoanApplication;
use PHPUnit\Framework\TestCase;

class InMemoryLoanRepositoryTest extends TestCase
{
    public function testFetchNotExistingApplication(): void
    {
        $inMemoryLoanRepository = new InMemoryLoanRepository();
        $this->expectException(ApplicationException::class);
        $inMemoryLoanRepository->fetch(1);
    }

    public function testStoreAndFetchResult(): void
    {
        // Arrange
        $repository = new InMemoryLoanRepository();
        $loanApplication = new LoanApplication(1);

        // Act
        $ticket = $repository->store($loanApplication);

        // Assert
        self::assertEquals($loanApplication, $repository->fetch($ticket->getId()));
    }

    public function testApproveNotExistingApplication(): void
    {
        $repository = new InMemoryLoanRepository();

        $this->expectException(ApplicationException::class);
        $repository->approve(1);
    }


    public function testApproveExistingApplication(): void
    {
        // Arrange
        $repository = new InMemoryLoanRepository();
        $loanApplication = new LoanApplication(1);
        $ticket = $repository->store($loanApplication);

        // Act
        $repository->approve($ticket->getId());

        // Assert
        self::assertTrue($repository->fetch($ticket->getId())->isApproved());
    }

    public function testGetNextIDForEmptyApp(): void
    {
        $repository = new InMemoryLoanRepository();

        self::assertEquals(1, $repository->getNextId());
    }

    public function testGetNextIDIfThereIsAnotherApplication(): void
    {
        $repository = new InMemoryLoanRepository();
        $loanApplication = new LoanApplication(1);
        $repository->store($loanApplication);

        self::assertEquals(2, $repository->getNextId());
    }
}
