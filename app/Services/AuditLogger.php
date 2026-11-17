<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function record(
        ?User $actor,
        string $event,
        string $description,
        ?Model $subject = null,
        array $metadata = [],
    ): AuditLog {
        return AuditLog::create([
            'actor_id' => $actor?->id,
            'actor_username' => $actor?->username,
            'event' => $event,
            'description' => $description,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'metadata' => $metadata ?: null,
            'occurred_at' => now(),
        ]);
    }
}
