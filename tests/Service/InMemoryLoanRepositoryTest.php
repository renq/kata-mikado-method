<?php

namespace App\Tests\Service;

use App\Service\ApplicationException;
use App\Service\InMemoryLoanRepository;
use PHPUnit\Framework\TestCase;

class InMemoryLoanRepositoryTest extends TestCase
{
    public function testFetch(): void
    {
        $inMemoryLoanRepository = new InMemoryLoanRepository();
        $this->expectException(ApplicationException::class);
        $inMemoryLoanRepository->fetch(1);
    }
}
