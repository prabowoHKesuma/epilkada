<?php

namespace App\Services;
use App\Models\AuditLog;

class AuditLogger
{
    public static function log(string $action, ?string $description = null, array $context = []): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'organization_id' => $context['organization_id'] ?? auth()->user()?->organization_id,
            'region_id' => $context['region_id'] ?? auth()->user()?->region_id,
            'election_id' => $context['election_id'] ?? null,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
