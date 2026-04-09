<?php

namespace App\Support;

final class AssistanceRequestFlow
{
    public const CREATED = 'created';
    public const ASSIGNED = 'assigned';
    public const IN_PROGRESS = 'in_progress';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';

    /**
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::CREATED,
            self::ASSIGNED,
            self::IN_PROGRESS,
            self::COMPLETED,
            self::CANCELLED,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function activeStatuses(): array
    {
        return [
            self::CREATED,
            self::ASSIGNED,
            self::IN_PROGRESS,
        ];
    }

    public static function isTerminal(string $status): bool
    {
        return in_array($status, [self::COMPLETED, self::CANCELLED], true);
    }

    public static function clientCanCancel(string $status): bool
    {
        return in_array($status, [self::CREATED, self::ASSIGNED], true);
    }

    public static function providerCanAccept(string $status, ?int $providerId): bool
    {
        return $status === self::CREATED && is_null($providerId);
    }

    public static function canProviderTransition(string $from, string $to): bool
    {
        $allowed = [
            self::ASSIGNED => [self::IN_PROGRESS, self::CANCELLED],
            self::IN_PROGRESS => [self::COMPLETED, self::CANCELLED],
        ];

        return in_array($to, $allowed[$from] ?? [], true);
    }

    public static function canAdminTransition(string $from, string $to): bool
    {
        if ($from === $to) {
            return true;
        }

        $allowed = [
            self::CREATED => [self::ASSIGNED, self::CANCELLED],
            self::ASSIGNED => [self::IN_PROGRESS, self::CANCELLED],
            self::IN_PROGRESS => [self::COMPLETED, self::CANCELLED],
            self::COMPLETED => [],
            self::CANCELLED => [],
        ];

        return in_array($to, $allowed[$from] ?? [], true);
    }
}
