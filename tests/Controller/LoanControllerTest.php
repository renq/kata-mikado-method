<?php

namespace App\Tests\Controller;

use App\Controller\LoanController;
use App\Service\FileBasedLoanRepository;
use App\Service\LoanRepository;
use JetBrains\PhpStorm\ArrayShape;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

use function glob;

final class LoanControllerTest extends TestCase
{
    private LoanController $loanController;
    private LoanRepository $loanRepository;

    public static function setUpBeforeClass(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove(glob(FileBasedLoanRepository::REPOSITORY_ROOT . '/*.loan'));
    }

    protected function setUp(): void
    {
        $this->loanRepository = new FileBasedLoanRepository();
        $this->loanController = new LoanController($this->loanRepository);
    }

    /** @test */
    public function incompleteRequest(): void
    {
        $request = Request::create('/loan');
        $response = $this->loanController->serve($request);

        self::assertEquals('{"error":"Incorrect parameters provided"}', $response->getContent());
    }

    /** @test */
    public function completeApplication(): void
    {
        $request = Request::create('/loan', 'GET', $this->applyParams());
        $response = $this->loanController->serve($request);
        self::assertEquals('{"id":1}', $response->getContent());
    }

    /** @test */
    public function givenAnIdTheStatusOfLoanIsReturned(): void
    {
        $request = Request::create('/loan', 'POST', $this->fetchParams());
        $response = $this->loanController->serve($request);
        self::assertEquals(
            [
                'applicationNo' => 1,
                'amount' => 100,
                'contact' => 'donald@ducks.burg',
                'approved' => false,
            ],
            json_decode($response->getContent(), true)
        );
    }

    /** @test */
    public function loanApplicationsCanBeApproved(): void
    {
        $request = Request::create('/loan', 'POST', $this->approveParams());
        $response = $this->loanController->serve($request);
        self::assertEquals('{"id":1}', $response->getContent());
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
