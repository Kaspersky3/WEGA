<?php

namespace App\Services;

use Illuminate\Http\Response;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;
use Dompdf\Dompdf;
use Dompdf\Options;

class ExportService
{
    /**
     * Télécharge un fichier Excel ou PDF.
     *
     * @param  array<int, string>  $header
     * @param  iterable<int, array<int, mixed>>  $rows
     */
    public function download(array $header, iterable $rows, string $filename, string $format = 'xlsx'): Response
    {
        $format = strtolower($format);

        if ($format === 'pdf') {
            return $this->downloadPdf($header, $rows, $filename);
        }

        // Par défaut, on exporte en Excel
        return $this->downloadExcel($header, $rows, $filename);
    }

    /**
     * Télécharge un fichier Excel (XLSX).
     */
    protected function downloadExcel(array $header, iterable $rows, string $filename): Response
    {
        // S'assurer que le nom de fichier a l'extension .xlsx
        if (!str_ends_with(strtolower($filename), '.xlsx')) {
            $filename = preg_replace('/\.(xlsx|csv)$/i', '', $filename) . '.xlsx';
        }

        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'wega_export_' . uniqid() . '.xlsx';

        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($tempPath);
        
        // Ajouter l'en-tête
        $writer->addRow(WriterEntityFactory::createRowFromArray($header));

        // Ajouter les lignes de données
        foreach ($rows as $row) {
            // Convertir les valeurs null en chaînes vides et nettoyer les données
            $cleanRow = array_map(function ($value) {
                if ($value === null) {
                    return '';
                }
                // Convertir les objets en chaînes si nécessaire
                if (is_object($value) && method_exists($value, '__toString')) {
                    return (string) $value;
                }
                return $value;
            }, $row);
            
            $writer->addRow(WriterEntityFactory::createRowFromArray($cleanRow));
        }

        $writer->close();

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Télécharge un fichier PDF.
     */
    protected function downloadPdf(array $header, iterable $rows, string $filename): Response
    {
        $html = $this->generatePdfHtml($header, $rows);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $tempPath = tempnam(sys_get_temp_dir(), 'wega_export_') . '.pdf';
        file_put_contents($tempPath, $dompdf->output());

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/pdf',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Génère le HTML pour le PDF.
     */
    protected function generatePdfHtml(array $header, iterable $rows): string
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #6366f1; color: white; padding: 8px; text-align: left; font-weight: bold; }
        td { padding: 6px; border: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f9fafb; }
        .header-row { background-color: #e5e7eb !important; font-weight: bold; }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>';

        foreach ($header as $col) {
            $html .= '<th>' . htmlspecialchars($col, ENT_QUOTES, 'UTF-8') . '</th>';
        }

        $html .= '</tr>
        </thead>
        <tbody>';

        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $value = $cell ?? '';
                // Formater les nombres
                if (is_numeric($value)) {
                    $value = number_format((float)$value, 0, ',', ' ');
                }
                $html .= '<td>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody>
    </table>
</body>
</html>';

        return $html;
    }

}

