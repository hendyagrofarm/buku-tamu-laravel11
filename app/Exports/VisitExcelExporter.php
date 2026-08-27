<?php

namespace App\Exports;

use App\Models\Visit;
use Illuminate\Support\Collection;
use ZipArchive;

class VisitExcelExporter
{
    public function download(Collection $visits, array $summary)
    {
        if (!class_exists(ZipArchive::class)) {
            throw new \RuntimeException('Extension PHP ZipArchive belum aktif. Aktifkan extension=zip pada php.ini XAMPP.');
        }

        $filename = 'laporan-kunjungan-' . now()->format('Ymd-His') . '.xlsx';
        $path = storage_path('app/' . $filename);

        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('File Excel tidak dapat dibuat.');
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypes());
        $zip->addFromString('_rels/.rels', $this->rootRels());
        $zip->addFromString('docProps/core.xml', $this->coreProps());
        $zip->addFromString('docProps/app.xml', $this->appProps());
        $zip->addFromString('xl/workbook.xml', $this->workbook());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRels());
        $zip->addFromString('xl/styles.xml', $this->styles());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->sheet($visits, $summary));
        $zip->close();

        return [$path, $filename];
    }

    private function sheet(Collection $visits, array $summary): string
    {
        $rows = [];
        $rows[] = $this->row(1, [
            $this->cell('A1', 'BUKU TAMU DIGITAL', 1),
        ], 'A1:M1');
        $rows[] = $this->row(2, [
            $this->cell('A2', 'Laporan Data Kunjungan - ' . ($summary['location'] ?? 'Semua Lokasi'), 2),
        ], 'A2:M2');
        $rows[] = $this->row(3, [
            $this->cell('A3', 'Dicetak: ' . now()->format('d/m/Y H:i') . ' WIB', 2),
        ], 'A3:M3');

        $rows[] = $this->row(5, [
            $this->cell('A5', 'TOTAL KUNJUNGAN', 10), $this->cell('B5', (string) $summary['total'], 11),
            $this->cell('D5', 'SUDAH SURVEY', 10), $this->cell('E5', (string) $summary['surveyed'], 11),
            $this->cell('G5', 'BELUM SURVEY', 10), $this->cell('H5', (string) $summary['pending'], 11),
            $this->cell('J5', 'RATA-RATA RATING', 10), $this->cell('K5', number_format((float) $summary['average'], 1) . ' / 5', 6),
        ]);

        $headers = ['No', 'Nomor Kunjungan', 'Lokasi', 'Nama Tamu', 'Instansi / Perusahaan', 'Pegawai Tujuan', 'Divisi', 'Keperluan', 'Jumlah Orang', 'Check In', 'Survey', 'Rating', 'Status'];
        $cells = [];
        foreach ($headers as $i => $header) {
            $cells[] = $this->cell($this->columnName($i + 1) . '7', $header, 3);
        }
        $rows[] = $this->row(7, $cells);

        $rowNumber = 8;
        foreach ($visits as $index => $visit) {
            $rating = $visit->satisfaction_rating ? str_repeat('★', (int) $visit->satisfaction_rating) : '-';
            $status = $visit->satisfaction_rating ? 'Sudah Survey' : 'Belum Survey';
            $values = [
                (string) ($index + 1),
                $visit->visit_number,
                $visit->location?->name ?? '-',
                $visit->visitor?->name ?? '-',
                $visit->visitor?->company ?? '-',
                $visit->employee_name ?: ($visit->employee?->name ?? '-'),
                $visit->employee?->division?->name ?? '-',
                $visit->purpose,
                (string) ($visit->number_of_people ?? 1),
                $visit->check_in_at?->format('d/m/Y H:i') ?? '-',
                $visit->surveyed_at?->format('d/m/Y H:i') ?? '-',
                $rating,
                $status,
            ];
            $cells = [];
            foreach ($values as $i => $value) {
                $style = 4;
                if ($i === 0 || $i === 8) $style = 5;
                if ($i === 11) $style = $visit->satisfaction_rating ? 7 : 4;
                if ($i === 12) $style = $visit->satisfaction_rating ? 8 : 9;
                $cells[] = $this->cell($this->columnName($i + 1) . $rowNumber, $value, $style);
            }
            $rows[] = $this->row($rowNumber, $cells);
            $rowNumber++;
        }

        if ($visits->isEmpty()) {
            $rows[] = $this->row(8, [$this->cell('A8', 'Tidak ada data kunjungan.', 4)], 'A8:L8');
        }

        $lastRow = max(8, $rowNumber - 1);
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheetViews><sheetView workbookViewId="0"><pane ySplit="7" topLeftCell="A8" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
            . '<sheetFormatPr defaultRowHeight="20"/>'
            . '<cols>'
            . '<col min="1" max="1" width="7" customWidth="1"/><col min="2" max="2" width="24" customWidth="1"/>'
            . '<col min="3" max="3" width="18" customWidth="1"/><col min="4" max="4" width="24" customWidth="1"/>'
            . '<col min="5" max="5" width="24" customWidth="1"/><col min="6" max="6" width="20" customWidth="1"/>'
            . '<col min="7" max="7" width="32" customWidth="1"/><col min="8" max="8" width="13" customWidth="1"/>'
            . '<col min="9" max="10" width="19" customWidth="1"/><col min="11" max="11" width="16" customWidth="1"/><col min="12" max="12" width="18" customWidth="1"/><col min="13" max="13" width="18" customWidth="1"/>'
            . '</cols><sheetData>' . implode('', $rows) . '</sheetData>'
            . ($visits->isEmpty() ? '<mergeCells count="4"><mergeCell ref="A1:M1"/><mergeCell ref="A2:M2"/><mergeCell ref="A3:M3"/><mergeCell ref="A8:M8"/></mergeCells>' : '<mergeCells count="3"><mergeCell ref="A1:M1"/><mergeCell ref="A2:M2"/><mergeCell ref="A3:M3"/></mergeCells>')
            . '<autoFilter ref="A7:M' . $lastRow . '"/>'
            . '</worksheet>';
    }

    private function cell(string $ref, string $value, int $style = 4): string
    {
        $value = htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
        return '<c r="' . $ref . '" s="' . $style . '" t="inlineStr"><is><t xml:space="preserve">' . $value . '</t></is></c>';
    }

    private function row(int $number, array $cells, ?string $merge = null): string
    {
        return '<row r="' . $number . '" ht="22" customHeight="1">' . implode('', $cells) . '</row>';
    }

    private function columnName(int $number): string
    {
        $name = '';
        while ($number > 0) {
            $mod = ($number - 1) % 26;
            $name = chr(65 + $mod) . $name;
            $number = intdiv($number - 1, 26);
        }
        return $name;
    }

    private function contentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            . '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            . '</Types>';
    }

    private function rootRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            . '</Relationships>';
    }

    private function workbook(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<fileVersion appName="xl" lastEdited="7" lowestEdited="7"/> <workbookPr/><bookViews><workbookView xWindow="0" yWindow="0" windowWidth="22000" windowHeight="12000"/></bookViews>'
            . '<sheets><sheet name="Laporan Kunjungan" sheetId="1" r:id="rId1"/></sheets></workbook>';
    }

    private function workbookRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }

    private function styles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<numFmts count="0"/><fonts count="4">'
            . '<font><sz val="11"/><name val="Aptos"/></font>'
            . '<font><b/><sz val="20"/><color rgb="FFFFFFFF"/><name val="Aptos Display"/></font>'
            . '<font><sz val="11"/><color rgb="FF64748B"/><name val="Aptos"/></font>'
            . '<font><b/><sz val="10"/><color rgb="FFFFFFFF"/><name val="Aptos"/></font>'
            . '</fonts><fills count="7">'
            . '<fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FF0F766E"/><bgColor indexed="64"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFF0FDFA"/><bgColor indexed="64"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FF0F172A"/><bgColor indexed="64"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFDFF7EC"/><bgColor indexed="64"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFEFF6FF"/><bgColor indexed="64"/></patternFill></fill>'
            . '</fills><borders count="3">'
            . '<border><left/><right/><top/><bottom/><diagonal/></border>'
            . '<border><left/><right/><top/><bottom style="thin" color="FFD9E2EC"/><diagonal/></border>'
            . '<border><left style="thin" color="FFE2E8F0"/><right style="thin" color="FFE2E8F0"/><top style="thin" color="FFE2E8F0"/><bottom style="thin" color="FFE2E8F0"/><diagonal/></border>'
            . '</borders><cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="10">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="2" borderId="0" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="3" fillId="4" borderId="2" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" applyAlignment="1"><alignment vertical="center" wrapText="1"/></xf>'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="0" fillId="3" borderId="2" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="0" fillId="5" borderId="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="0" fillId="5" borderId="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="0" fillId="6" borderId="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            . '</cellXfs><cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles></styleSheet>';
    }

    private function coreProps(): string
    {
        $now = now()->toIso8601String();
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . '<dc:title>Laporan Data Kunjungan - Buku Tamu Digital</dc:title><dc:creator>Buku Tamu Digital</dc:creator><dcterms:created xsi:type="dcterms:W3CDTF">' . $now . '</dcterms:created></cp:coreProperties>';
    }

    private function appProps(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes"><Application>Microsoft Excel Compatible</Application></Properties>';
    }
}
