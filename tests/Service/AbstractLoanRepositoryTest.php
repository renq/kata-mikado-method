<?php

namespace App\Tests\Service;

use App\Service\LoanApplication;
use App\Service\LoanRepository;
use App\Service\MemoryLoanRepository;
use App\Tests\LoanApplicationMother;
use PHPUnit\Framework\TestCase;

abstract class AbstractLoanRepositoryTest extends TestCase
{
    protected LoanRepository $repository;

    /** @test */
    public function stored_application_can_be_fetched(): void
    {
        $application = LoanApplicationMother::create(1);

        $ticket = $this->repository->store($application);

        self::assertEquals($application, $this->repository->fetch($ticket->getId()));
    }

    /** @test */
    public function application_can_be_approved(): void
    {
        $application = LoanApplicationMother::create(1);
        $ticket = $this->repository->store($application);

        $this->repository->approve($ticket->getId());

        $storedApplication = $this->repository->fetch($ticket->getId());
        self::assertTrue($storedApplication->isApproved());
    }
}
