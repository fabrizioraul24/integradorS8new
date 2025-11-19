<?php

namespace App\Services;

use App\Models\Backup;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupService
{
    private Filesystem $disk;
    private string $connection;

    public function __construct()
    {
        $this->disk = Storage::disk('local');
        $this->connection = Config::get('database.default');
    }

    public function create(?int $userId = null): Backup
    {
        $fileName = 'backup_' . now()->format('Ymd_His') . '.sql';
        $path = 'backups/' . $fileName;
        $absolutePath = storage_path('app/' . $path);

        $this->disk->makeDirectory('backups');

        $backup = Backup::create([
            'file_name' => $fileName,
            'disk' => 'local',
            'status' => 'running',
            'created_by' => $userId,
        ]);

        try {
            $this->dumpDatabase($absolutePath);

            $size = is_file($absolutePath) ? filesize($absolutePath) : 0;
            $backup->update([
                'size' => $size,
                'status' => 'completed',
                'message' => 'Copia generada correctamente',
            ]);
        } catch (\Throwable $e) {
            $backup->update([
                'status' => 'failed',
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }

        return $backup;
    }

    private function dumpDatabase(string $targetPath): void
    {
        $connection = DB::connection($this->connection);
        $pdo = $connection->getPdo();
        $database = $connection->getDatabaseName();

        $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
        if (! $tables) {
            throw new \RuntimeException('No se encontraron tablas para respaldar.');
        }

        $handle = fopen($targetPath, 'w+');
        if (! $handle) {
            throw new \RuntimeException('No se pudo crear el archivo temporal del backup.');
        }

        fwrite($handle, sprintf("-- Backup generado el %s\r\n", now()->toDateTimeString()));
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\r\n\r\n");

        foreach ($tables as $table) {
            $createRecord = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
            $createSql = $createRecord['Create Table'] ?? null;
            if (! $createSql) {
                continue;
            }

            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\r\n");
            fwrite($handle, $createSql . ";\r\n\r\n");

            $columns = $connection->getSchemaBuilder()->getColumnListing($table);
            if (! $columns) {
                continue;
            }
            $columnList = '(' . implode(', ', array_map(fn ($col) => "`{$col}`", $columns)) . ')';

            $rows = $connection->table($table)->cursor();

            foreach ($rows as $row) {
                $values = [];
                foreach ($columns as $column) {
                    $values[] = $this->escapeValue($row->{$column} ?? null);
                }
                fwrite($handle, sprintf(
                    "INSERT INTO `%s` %s VALUES (%s);\r\n",
                    $table,
                    $columnList,
                    implode(', ', $values)
                ));
            }

            fwrite($handle, "\r\n");
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\r\n");
        fclose($handle);
    }

    private function escapeValue($value): string
    {
        if (is_null($value)) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        $escaped = str_replace(
            ["\\", "\0", "\n", "\r", "'", '"', "\x1a"],
            ["\\\\", "\\0", "\\n", "\\r", "\\'", '\\"', "\\Z"],
            $value
        );

        return "'" . $escaped . "'";
    }
}
