<?php

namespace App\Services;

use Illuminate\Http\Response;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;
use OpenSpout\Writer\WriterInterface;

class ExportService
{
    /**
     * Stream a CSV/XLSX download built from the given rows.
     *
     * @param  array<int, string>  $header
     * @param  iterable<int, array<int, mixed>>  $rows
     */
    public function download(array $header, iterable $rows, string $filename, string $format = 'csv'): Response
    {
        $format = strtolower($format);

        $writer = $this->createWriter($format);

        return response()->streamDownload(function () use ($writer, $header, $rows) {
            $writer->openToOutput();
            $writer->addRow(WriterEntityFactory::createRowFromArray($header));

            foreach ($rows as $row) {
                $writer->addRow(WriterEntityFactory::createRowFromArray($row));
            }

            $writer->close();
        }, $filename, $this->headers($format));
    }

    protected function headers(string $format): array
    {
        return match ($format) {
            'xlsx' => [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ],
            default => [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ],
        };
    }

    protected function createWriter(string $format): WriterInterface
    {
        return match ($format) {
            'xlsx' => WriterEntityFactory::createXLSXWriter(),
            default => tap(WriterEntityFactory::createCSVWriter(), function ($writer) {
                $writer->setFieldDelimiter(';');
                $writer->setFieldEnclosure('"');
                $writer->setShouldAddBOM(true);
            }),
        };
    }
}

