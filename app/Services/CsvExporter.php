<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Ekspor data sebagai CSV dengan proteksi CSV injection (C.5).
 */
class CsvExporter
{
    /**
     * Sanitasi nilai sel agar tidak bisa dieksekusi sebagai formula di Excel/Spreadsheet.
     * Sel yang diawali =, +, -, @ ditambahkan prefix tanda kutip.
     */
    public static function safe(mixed $value): string
    {
        $str = (string) ($value ?? '');

        if ($str !== '' && in_array($str[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'".$str;
        }

        return $str;
    }

    /**
     * Buat StreamedResponse CSV dari array header dan data.
     *
     * @param  array  $headers  ['Kolom A', 'Kolom B', ...]
     * @param  iterable  $rows  Tiap baris: array atau object dengan key yang sama
     */
    public function stream(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows): void {
            // BOM untuk Excel agar bisa baca UTF-8
            echo "\xEF\xBB\xBF";

            $out = fopen('php://output', 'w');

            fputcsv($out, $headers);

            foreach ($rows as $row) {
                $row = is_array($row) ? $row : (array) $row;
                fputcsv($out, array_map([self::class, 'safe'], $row));
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
