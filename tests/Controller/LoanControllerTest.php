<?php

namespace App\Tests\Controller;

use App\Controller\LoanController;
use App\Service\FileSystemLoanRepository;
use App\Service\InMemoryLoanRepository;
use App\Service\LoanApplication;
use App\Service\LoanRepository;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

use function glob;

final class LoanControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private LoanRepository $loanRepository;

    protected function setUp(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove(glob(FileSystemLoanRepository::REPOSITORY_ROOT . '/*.loan'));

        $this->client = self::createClient();
        $this->loanRepository = $this->client->getContainer()->get(LoanRepository::class);
    }

    /** @test */
    public function incompleteRequest(): void
    {
        $this->client->request('GET', '/');

        self::assertEquals('{"error":"Incorrect parameters provided"}', $this->client->getResponse()->getContent());
    }

    /** @test */
    public function completeApplication(): void
    {
        $this->client->request('GET', '/', $this->applyParams());

        self::assertEquals('{"id":1}', $this->client->getResponse()->getContent());
    }

    /** @test */
    public function loanApplicationsCanBeApproved(): void
    {
        // Arrange
        $id = 2;
        $this->loanRepository->store($this->createApplication($id));

        // Act
        $this->client->request('POST', '/', $this->approveParams($id));

        // Assert
        self::assertEquals('{"id":2}', $this->client->getResponse()->getContent());
    }

    /** @test */
    public function givenAnIdTheStatusOfLoanIsReturned(): void
    {
        // Arrange
        $this->loanRepository->store($this->createApplication(1));

        // Act
        $this->client->request('POST', '/', $this->fetchParams());

        // Assert
        self::assertEquals(
            [
                'applicationNo' => 1,
                'amount' => 10,
                'contact' => 'donald@ducks.burg',
                'approved' => false,
            ],
            json_decode($this->client->getResponse()->getContent(), true)
        );
    }

    private function createApplication(int $id): LoanApplication
    {
        $application = new LoanApplication($id);
        $application->setAmount(10);
        $application->setApproved(false);
        $application->setContact('donald@ducks.burg');

        return $application;
    }

    #[ArrayShape(['action' => 'string', 'ticketId' => 'string'])]
    private function approveParams(string $id): array
    {
        return [
            'action' => LoanController::APPROVE,
            'ticketId' => $id,
        ];
    }

    #[ArrayShape(['action' => 'string', 'amount' => 'string', 'contact' => 'string'])]
    private function applyParams(): array
    {
        return [
            'action' => LoanController::APPLICATION,
            'amount' => '100',
            'contact' => 'donald@ducks.burg',
        ];
    }

    #[ArrayShape(['action' => "string", 'ticketId' => 'string'])]
    private function fetchParams(): array
    {
        return [
            'action' => LoanController::FETCH,
            'ticketId' => '1',
        ];
    }
}
