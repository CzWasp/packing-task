<?php

declare(strict_types=1);

namespace App\Packing;

class PackingApiException extends \RuntimeException
{
    /**
     * @param string[][] $errors
     */
    public static function fromErrorArray(array $errors): self
    {
        $messages = array_map(function ($error) {
            $level = strtoupper($error['level'] ?? 'UNKNOWN');
            $message = $error['message'] ?? 'No message';
            return "[{$level}] {$message}";
        }, $errors);

        return new self("Packing API returned errors:\n" . implode("\n", $messages));
    }
}
