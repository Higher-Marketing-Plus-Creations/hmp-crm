<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class BrainV2SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedTable('client_conversation_settings', 'postman/conversation_settings.json');
        $this->seedTable('conversation_leads', 'postman/leads.json');
        $this->seedTable('conversation_sessions', 'postman/sessions.json');
        $this->seedTable('conversations', 'postman/conversations.json');
        $this->seedTable('knowledge_base', 'postman/KB ROWS.JSON');
    }

    private function seedTable(string $table, string $relativePath): void
    {
        $path = base_path($relativePath);
        $defaultLeadId = $table === 'conversation_sessions' && Schema::hasTable('leads')
            ? DB::table('leads')->orderBy('id')->value('id')
            : null;

        if (! File::exists($path)) {
            $this->command?->warn("Skipped {$table}: file not found at {$relativePath}");
            return;
        }

        $rows = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($rows)) {
            $this->command?->warn("Skipped {$table}: invalid JSON format");
            return;
        }

        foreach ($rows as $row) {
            if (! is_array($row) || ! array_key_exists('id', $row)) {
                continue;
            }

            if ($table === 'conversation_sessions') {
                if (! array_key_exists('lead_id', $row) || $row['lead_id'] === null) {
                    $row['lead_id'] = $defaultLeadId;
                }
            }

            DB::table($table)->updateOrInsert(
                $this->uniqueKeyFor($table, $row),
                $this->normalizeRow($table, $row)
            );
        }
    }

    private function uniqueKeyFor(string $table, array $row): array
    {
        if ($table === 'conversation_sessions' && ! empty($row['session_id'])) {
            return ['session_id' => $row['session_id']];
        }

        if ($table === 'conversations' && ! empty($row['session_id']) && array_key_exists('created_at', $row)) {
            return [
                'session_id' => $row['session_id'],
                'created_at' => $row['created_at'],
            ];
        }

        if ($table === 'conversation_leads' && ! empty($row['client_id']) && ! empty($row['session_id'])) {
            return [
                'client_id' => $row['client_id'],
                'session_id' => $row['session_id'],
            ];
        }

        return ['id' => $row['id']];
    }

    private function normalizeRow(string $table, array $row): array
    {
        $columns = Schema::hasTable($table) ? Schema::getColumnListing($table) : [];

        $normalized = [];

        foreach ($row as $key => $value) {
            if ($columns && ! in_array($key, $columns, true)) {
                continue;
            }

            if (is_array($value)) {
                $normalized[$key] = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                continue;
            }

            if ($this->isDateColumn($key) && is_string($value) && $value !== '') {
                try {
                    $normalized[$key] = Carbon::parse($value)->format('Y-m-d H:i:s');
                    continue;
                } catch (\Throwable $e) {
                    // Leave the original value if parsing fails.
                }
            }

            $normalized[$key] = $value;
        }

        return $normalized;
    }

    private function isDateColumn(string $column): bool
    {
        return in_array($column, ['created_at', 'updated_at', 'emailed_at', 'qualified_at', 'notification_sent_at', 'last_activity_at'], true)
            || str_ends_with($column, '_at');
    }
}
