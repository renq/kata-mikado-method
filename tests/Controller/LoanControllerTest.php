<?php

namespace App\Tests\Controller;

use App\Controller\LoanController;
use App\Service\FileSystemLoanRepository;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

use function glob;

final class LoanControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public static function setUpBeforeClass(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove(glob(FileSystemLoanRepository::REPOSITORY_ROOT . '/*.loan'));
    }

    protected function setUp(): void
    {
        $this->client = self::createClient();
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
    public function givenAnIdTheStatusOfLoanIsReturned(): void
    {
        $this->client->request('POST', '/', $this->fetchParams());

        self::assertEquals(
            [
                'applicationNo' => 1,
                'amount' => 100,
                'contact' => 'donald@ducks.burg',
                'approved' => false,
            ],
            json_decode($this->client->getResponse()->getContent(), true)
        );
    }

    /** @test */
    public function loanApplicationsCanBeApproved(): void
    {
        $this->client->request('POST', '/', $this->approveParams());

        self::assertEquals('{"id":1}', $this->client->getResponse()->getContent());
    }


    #[ArrayShape(['action' => 'string', 'ticketId' => 'string'])]
    private function approveParams(): array
    {
        return [
            'action' => LoanController::APPROVE,
            'ticketId' => '1',
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
