<?php
namespace App\Services;

use App\Models\Priority;
use Carbon\Carbon;

class TaskInputParser
{
    public static function parse(string $input): array
    {
        $priorityId = null;
        if (preg_match('/!(low|medium|high)/i', $input, $matches)) {
            $priority = strtolower($matches[1]);
            $priorityId = Priority::whereName($priority)->value('id') ?? null;
        }

        return [
            'priority_id' => $priorityId,
        ];
    }
}
