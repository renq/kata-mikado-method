<?php

declare(strict_types=1);

namespace App\Tests;

use App\Service\LoanApplication;

class LoanApplicationMother
{
    public static function create(int $applicationNo): LoanApplication
    {
        $application = new LoanApplication($applicationNo);
        $application->setAmount(100);
        $application->setContact('donald@ducks.burg');

        return $application;
    }
}
