<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\FileSystemLoanRepository;
use App\Service\LoanApplication;
use App\Service\LoanRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use function is_numeric;
use function preg_match;

class LoanController
{
    public const APPLICATION = "apply";
    public const FETCH = "fetch";
    public const TICKET_ID = "ticketId";
    public const APPROVE = "approve";

    public function __construct(
        private LoanRepository $fileSystemLoanRepository
    ) {
    }

    #[Route('/', name: 'loan')]
    public function serve(Request $request): Response
    {
        if ($this->isApplication($request)) {
            $application = new LoanApplication(FileSystemLoanRepository::getNextId());
            $application->setAmount($this->amountFrom($request));
            $application->setContact($this->contactFrom($request));
            $ticket = $this->fileSystemLoanRepository->store($application);
            $normalizer = new ObjectNormalizer();
            $result = $normalizer->normalize($ticket);
        } else if ($this->isStatusRequest($request) && $this->idSpecified($request)) {
            $result = $this->fetchLoanInfo($request->get(self::TICKET_ID));
        } else if ($this->isApproval($request) && $this->idSpecified($request)) {
            $result = $this->approveLoan($request->get(self::TICKET_ID));
        } else {
            $result = ['error' => 'Incorrect parameters provided'];
        }

        return new JsonResponse($result);
    }

    private function isApplication(Request $request): bool
    {
        return $request->get('action') === self::APPLICATION;
    }

    private function isStatusRequest(Request $request): bool
    {
        return $request->get('action') === self::FETCH;
    }

    private function idSpecified(Request $request): bool
    {
        return $request->request->has(self::TICKET_ID) && $this->validId($request) >= 0;
    }

    private function validId(Request $request): int
    {
        $ticketId = $request->get(self::TICKET_ID);

        return is_numeric($ticketId) && preg_match('/^[\d]+$/', $ticketId) ? (int)$ticketId : -1;
    }

    private function fetchLoanInfo(string $ticketId): array
    {
        $formerApplication = $this->fileSystemLoanRepository->fetch($ticketId);
        $normalizer = new ObjectNormalizer();

        return $normalizer->normalize($formerApplication);
    }

    private function isApproval(Request $request): bool
    {
        return $request->get('action') === self::APPROVE;
    }

    private function approveLoan(string $parameter): array
    {
        $normalizer = new ObjectNormalizer();

        return $normalizer->normalize($this->fileSystemLoanRepository->approve($parameter));
    }

    private function amountFrom(Request $request): int
    {
        return (int)$request->get('amount');
    }

    private function contactFrom(Request $request): string
    {
        return $request->get('contact');
    }
}
