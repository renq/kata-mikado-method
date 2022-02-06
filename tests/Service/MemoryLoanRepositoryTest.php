<?php

namespace App\Tests\Service;

use App\Service\MemoryLoanRepository;

/**
 * @covers \App\Service\MemoryLoanRepository
 */
final class MemoryLoanRepositoryTest extends AbstractLoanRepositoryTest
{
    protected function setUp(): void
    {
        $this->repository = new MemoryLoanRepository();
    }
}
