<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\LoanApplication;
use App\Service\LoanRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use function is_numeric;
use function preg_match;

class ApproveController
{
    public const TICKET_ID = "ticketId";
    public const APPROVE = "approve";

    public function __construct(private LoanRepository $loanRepository)
    {
    }

    #[Route('/approve', name: 'approve')]
    public function serve(Request $request): Response
    {
        if ($this->isApproval($request) && $this->idSpecified($request)) {
            $result = $this->approveLoan($request->get(self::TICKET_ID));
        } else {
            $result = ['error' => 'Incorrect parameters provided'];
        }

        return new JsonResponse($result);
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

    private function isApproval(Request $request): bool
    {
        return $request->get('action') === self::APPROVE;
    }

    private function approveLoan(string $parameter): array
    {
        $normalizer = new ObjectNormalizer();

        return $normalizer->normalize($this->loanRepository->approve($parameter));
    }
}
