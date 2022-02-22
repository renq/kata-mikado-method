<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use function file_get_contents;
use function json_decode;
use function json_encode;

class FileSystemLoanRepository implements LoanRepository
{
    public const REPOSITORY_ROOT = __DIR__ . '/../../var/repository';
    public const FILE_EXTENSION = '.loan';

    public function getNextId(): int
    {
        $filesystem = new Filesystem();
        $filesystem->mkdir(FileSystemLoanRepository::REPOSITORY_ROOT);

        $finder = new Finder();
        $finder->files()->in(FileSystemLoanRepository::REPOSITORY_ROOT)->name('*' . FileSystemLoanRepository::FILE_EXTENSION);

        return $finder->count() + 1;
    }

    public function fetch(string | int $ticketId): LoanApplication
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

    public function store(LoanApplication $application): Ticket
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

    public function approve(string $ticketId): Ticket
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
