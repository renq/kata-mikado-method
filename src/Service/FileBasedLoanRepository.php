<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use function file_get_contents;
use function json_decode;
use function json_encode;

class FileBasedLoanRepository implements LoanRepository
{
    public const REPOSITORY_ROOT = __DIR__ . '/../../var/repository';
    public const FILE_EXTENSION = '.loan';

    public static function fetch(string | int $ticketId): LoanApplication
    {
        $ticketId = (int)$ticketId;

        $file = @file_get_contents(self::fileFromApplication($ticketId));

        if ($file !== false) {
            $normalizer = new ObjectNormalizer();

            return $normalizer->denormalize(json_decode($file, true), LoanApplication::class);
        } else {
            throw new ApplicationException('Ticket not found');
        }
    }

    public static function store(LoanApplication $application): Ticket
    {
        $filesystem = new Filesystem();
        $applicationFile = self::fileFromApplication($application->getApplicationNo());

        try {
            $filesystem->mkdir(self::REPOSITORY_ROOT);
            $filesystem->touch($applicationFile);

            $normalizer = new ObjectNormalizer();
            $filesystem->dumpFile(
                $applicationFile,
                json_encode($normalizer->normalize($application, context: ['skip_uninitialized_values' => false]))
            );

            return new Ticket($application->getApplicationNo());
        } catch (IOException $e) {
            throw new ApplicationException('Could not store application', 0, $e);
        }
    }

    public static function approve(string $ticketId): Ticket
    {
        $application = self::fetch($ticketId);
        $application->approve();
        self::store($application);

        return new Ticket($application->getApplicationNo());
    }

    private static function fileFromApplication(int $applicationNo): string
    {
        return self::REPOSITORY_ROOT . '/' . $applicationNo . self::FILE_EXTENSION;
    }
}
