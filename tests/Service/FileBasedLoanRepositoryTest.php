<?php

namespace App\Tests\Service;

use App\Service\FileBasedLoanRepository;
use App\Service\MemoryLoanRepository;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Service\FileBasedLoanRepository
 */
final class FileBasedLoanRepositoryTest extends AbstractLoanRepositoryTest
{
    protected function setUp(): void
    {
        $this->repository = new FileBasedLoanRepository();
    }
}
