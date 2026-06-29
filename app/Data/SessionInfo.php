<?php

namespace App\Data;

class SessionInfo
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $academy,
        public readonly string $academyAbbreviation,
        public readonly string $module,
        public readonly string $course,
        public readonly string $summary,
        public readonly string $goals,
        public readonly string $evaluation,
        public readonly string $sessionUid,
    ) {}
}
