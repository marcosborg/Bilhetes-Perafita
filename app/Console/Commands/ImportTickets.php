<?php

namespace App\Console\Commands;

use App\Models\Brother;
use App\Models\ServiceGroup;
use App\Models\Ticket;
use App\Models\TicketFamily;
use App\Models\TicketImport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ZipArchive;

class ImportTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:import
        {--excel= : Path to bilhetes-congresso.xlsx}
        {--zip= : Path to the ZIP containing PDF tickets}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import ticket families, brothers, PDF links, and QR/internal codes from Excel and ZIP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $excelPath = (string) $this->option('excel');
        $zipPath = (string) $this->option('zip');

        if ($excelPath === '' || ! is_file($excelPath)) {
            $this->error('Use --excel=/absolute/path/to/bilhetes-congresso.xlsx');

            return self::FAILURE;
        }

        if ($zipPath === '' || ! is_file($zipPath)) {
            $this->error('Use --zip=/absolute/path/to/Perafita.zip');

            return self::FAILURE;
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            $this->error('Could not open ZIP file.');

            return self::FAILURE;
        }

        $zipPdfNames = $this->extractPdfFiles($zip);
        $workbook = IOFactory::load($excelPath);
        $generalRows = $this->sheetRows($workbook, 'bilhetes-congresso');
        $mappingRows = $this->sheetRows($workbook, 'Folha1');

        $warnings = [];
        $mappedFiles = [];
        $missingFiles = [];

        DB::transaction(function () use ($excelPath, $zipPath, $zip, $zipPdfNames, $generalRows, $mappingRows, &$warnings, &$mappedFiles, &$missingFiles): void {
            for ($number = 1; $number <= 7; $number++) {
                ServiceGroup::firstOrCreate(
                    ['number' => $number],
                    ['name' => "Grupo {$number}"],
                );
            }

            foreach ($generalRows as $row) {
                $groupNumber = $this->groupNumber($row['Grupo'] ?? null);
                $familyName = $this->clean($row['Família'] ?? null);
                $brotherName = $this->clean($row['Nome'] ?? null);

                if (! $groupNumber || $familyName === '' || $brotherName === '') {
                    continue;
                }

                $this->upsertBrother($groupNumber, $familyName, $brotherName, $row);
            }

            foreach ($mappingRows as $row) {
                $filename = basename($this->clean($row['Ficheiro PDF'] ?? null));
                $code = $this->clean($row['Código'] ?? null);
                $familyName = $this->clean($row['Família'] ?? null);
                $brotherName = $this->clean($row['Nome'] ?? null);
                $groupNumber = $this->groupNumber($row['Grupos'] ?? null);

                if ($filename === '') {
                    continue;
                }

                $mappedFiles[] = $filename;
                $pdfPath = null;
                $status = Ticket::STATUS_PROBLEM;

                if (isset($zipPdfNames[$filename])) {
                    $pdfPath = "tickets/{$filename}";
                    Storage::disk('local')->put($pdfPath, $zip->getFromIndex($zipPdfNames[$filename]));
                    $status = Ticket::STATUS_ASSIGNED;
                } else {
                    $missingFiles[] = $filename;
                    $warnings[] = "PDF indicado no Excel não existe no ZIP: {$filename}";
                }

                $group = $groupNumber ? ServiceGroup::where('number', $groupNumber)->first() : null;
                $family = null;
                $brother = null;

                if ($group && $familyName !== '' && $brotherName !== '') {
                    $brother = $this->upsertBrother($group->number, $familyName, $brotherName, $row);
                    $family = $brother->family;
                } else {
                    $status = Ticket::STATUS_PROBLEM;
                    $warnings[] = "Linha de correspondência incompleta para PDF: {$filename}";
                }

                $ticket = Ticket::updateOrCreate(
                    ['pdf_filename' => $filename],
                    [
                        'service_group_id' => $group?->id,
                        'ticket_family_id' => $family?->id,
                        'brother_id' => $brother?->id,
                        'pdf_path' => $pdfPath,
                        'internal_code' => $code !== '' ? $code : null,
                        'status' => $status,
                        'source_row' => $row,
                    ],
                );

                $ticket->events()->create([
                    'service_group_id' => $ticket->service_group_id,
                    'type' => 'imported',
                    'actor' => 'artisan',
                    'message' => 'Bilhete importado a partir da correspondência do Excel.',
                ]);
            }

            foreach (array_keys($zipPdfNames) as $filename) {
                if (in_array($filename, $mappedFiles, true)) {
                    continue;
                }

                $warnings[] = "PDF no ZIP sem correspondência no Excel: {$filename}";

                Ticket::updateOrCreate(
                    ['pdf_filename' => $filename],
                    [
                        'pdf_path' => "tickets/{$filename}",
                        'status' => Ticket::STATUS_PENDING,
                        'notes' => 'PDF existe no ZIP, mas não tem linha de correspondência na Folha1.',
                    ],
                );

                Storage::disk('local')->put("tickets/{$filename}", $zip->getFromIndex($zipPdfNames[$filename]));
            }

            TicketImport::create([
                'excel_path' => $excelPath,
                'zip_path' => $zipPath,
                'zip_pdf_count' => count($zipPdfNames),
                'mapped_ticket_count' => count(array_unique($mappedFiles)),
                'missing_pdf_count' => count(array_unique($missingFiles)),
                'unmapped_pdf_count' => count(array_diff(array_keys($zipPdfNames), $mappedFiles)),
                'warnings' => array_values(array_unique($warnings)),
                'finished_at' => now(),
            ]);
        });

        $zip->close();

        $this->info('Import complete.');
        $this->line('PDFs in ZIP: '.count($zipPdfNames));
        $this->line('Mapped tickets: '.count(array_unique($mappedFiles)));
        $this->line('Missing PDFs: '.count(array_unique($missingFiles)));
        $this->line('Warnings: '.count(array_unique($warnings)));

        return self::SUCCESS;
    }

    /**
     * @return array<string, int>
     */
    private function extractPdfFiles(ZipArchive $zip): array
    {
        $files = [];

        for ($index = 0; $index < $zip->numFiles; $index++) {
            $name = (string) $zip->getNameIndex($index);

            if (str_ends_with(strtolower($name), '.pdf')) {
                $files[basename($name)] = $index;
            }
        }

        return $files;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function sheetRows($workbook, string $sheetName): array
    {
        $sheet = $workbook->getSheetByName($sheetName);

        if (! $sheet) {
            throw new \RuntimeException("Missing sheet [{$sheetName}]");
        }

        $rows = $sheet->toArray(null, true, true, true);
        $headers = array_shift($rows) ?: [];
        $headers = array_map(fn ($value) => $this->clean($value), $headers);
        $output = [];

        foreach ($rows as $row) {
            $assoc = [];
            $hasValue = false;

            foreach ($headers as $column => $header) {
                if ($header === '') {
                    continue;
                }

                $value = $row[$column] ?? null;
                $assoc[$header] = $this->clean($value);
                $hasValue = $hasValue || $assoc[$header] !== '';
            }

            if ($hasValue) {
                $output[] = $assoc;
            }
        }

        return $output;
    }

    private function upsertBrother(int $groupNumber, string $familyName, string $brotherName, array $row): Brother
    {
        $group = ServiceGroup::firstOrCreate(
            ['number' => $groupNumber],
            ['name' => "Grupo {$groupNumber}"],
        );

        $family = TicketFamily::firstOrCreate([
            'service_group_id' => $group->id,
            'name' => $familyName,
        ]);

        return Brother::updateOrCreate(
            [
                'service_group_id' => $group->id,
                'ticket_family_id' => $family->id,
                'name' => $brotherName,
            ],
            [
                'is_under_12' => $this->truthy($row['<12'] ?? null),
                'is_over_75' => $this->truthy($row['>75'] ?? null),
                'has_locomotion_need' => $this->truthy($row['Locom.'] ?? null),
                'has_mobility_need' => $this->truthy($row['Mobil.'] ?? null),
                'normal_ticket' => $this->truthy($row['Normais'] ?? null),
                'andante' => $this->truthy($row['Andante'] ?? null),
                'distico' => $this->truthy($row['Dístico'] ?? null),
                'source_row' => $row,
            ],
        );
    }

    private function groupNumber(mixed $value): ?int
    {
        $value = $this->clean($value);

        return is_numeric($value) ? (int) $value : null;
    }

    private function truthy(mixed $value): bool
    {
        return $this->clean($value) !== '';
    }

    private function clean(mixed $value): string
    {
        return trim((string) $value);
    }
}
