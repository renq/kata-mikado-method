<?php

namespace App\Tests\Service;

use App\Service\LoanApplication;
use App\Service\LoanRepository;
use App\Service\MemoryLoanRepository;
use PHPUnit\Framework\TestCase;

abstract class AbstractLoanRepositoryTest extends TestCase
{
    protected LoanRepository $repository;

    /** @test */
    public function stored_application_can_be_fetched(): void
    {
        $application = $this->createApplication();

        $ticket = $this->repository->store($application);

        self::assertEquals($application, $this->repository->fetch($ticket->getId()));
    }

    /** @test */
    public function application_can_be_approved(): void
    {
        $application = $this->createApplication();
        $ticket = $this->repository->store($application);

        $this->repository->approve($ticket->getId());

        $storedApplication = $this->repository->fetch($ticket->getId());
        self::assertTrue($storedApplication->isApproved());
    }

    private function createApplication(): LoanApplication
    {
        $application = new LoanApplication();
        $application->setApplicationNo(1);
        $application->setAmount(100);
        $application->setContact('test@contact.com');

        return $application;
    }
}
