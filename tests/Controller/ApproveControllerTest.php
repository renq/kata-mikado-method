<?php

namespace App\Tests\Controller;

use App\Controller\ApproveController;
use App\Controller\LoanController;
use App\Service\MemoryLoanRepository;
use App\Tests\LoanApplicationMother;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ApproveControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private MemoryLoanRepository $loanRepository;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->loanRepository = self::getContainer()->get(MemoryLoanRepository::class);

    }

    /** @test */
    public function incompleteRequest(): void
    {
        $this->client->request('GET', '/approve');
        self::assertEquals('{"error":"Incorrect parameters provided"}', $this->client->getResponse()->getContent());
    }

    /** @test */
    public function loanApplicationsCanBeApproved(): void
    {
        $this->loanRepository->store(LoanApplicationMother::create(1));

        $this->client->request('POST', '/approve', $this->approveParams());

        self::assertEquals('{"id":1}', $this->client->getResponse()->getContent());
    }


    #[ArrayShape(['action' => 'string', 'ticketId' => 'string'])]
    private function approveParams(): array
    {
        return [
            'action' => ApproveController::APPROVE,
            'ticketId' => '1',
        ];
    }
}
