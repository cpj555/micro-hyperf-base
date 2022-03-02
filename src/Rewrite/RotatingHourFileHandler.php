<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Rewrite;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class RotatingHourFileHandler extends RotatingFileHandler
{
    public const FILE_PER_HOUR = 'Y-m-d-H';

    public function __construct(string $filename, int $maxFiles = 2, $level = Logger::DEBUG, bool $bubble = true, ?int $filePermission = null, bool $useLocking = false)
    {
        parent::__construct($filename, $maxFiles, $level, $bubble, $filePermission, $useLocking);
        $this->nextRotation = new \DateTimeImmutable('+1 hours');
        $this->dateFormat = static::FILE_PER_HOUR;
    }

    public function write(array $record): void
    {
        parent::write($record); // TODO: Change the autogenerated stub
    }

    public function rotate(): void
    {
        // update filename
        $this->url = $this->getTimedFilename();
        $this->nextRotation = new \DateTimeImmutable('+1 hours');

        // skip GC of old logs if files are unlimited
        if ($this->maxFiles === 0) {
            return;
        }

        $logFiles = glob($this->getGlobPattern());
        if ($this->maxFiles >= \count($logFiles)) {
            // no files to remove
            return;
        }

        // Sorting the files by name to remove the older ones
        usort($logFiles, fn ($a, $b) => strcmp($b, $a));

        foreach (\array_slice($logFiles, $this->maxFiles) as $file) {
            if (is_writable($file)) {
                // suppress errors here as unlink() might fail if two processes
                // are cleaning up/rotating at the same time
                set_error_handler(fn (int $errno, string $errstr, string $errfile, int $errline): bool => false);
                unlink($file);
                restore_error_handler();
            }
        }

        $this->mustRotate = false;
    }
}
