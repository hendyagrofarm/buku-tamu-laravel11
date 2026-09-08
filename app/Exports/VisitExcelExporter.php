<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use ZipArchive;

class VisitExcelExporter
{
    public function download(
        Collection $visits,
        array $summary
    ): array {
        if (!class_exists(ZipArchive::class)) {
            throw new \RuntimeException(
                'Extension PHP ZipArchive belum aktif.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | NAMA FILE
        |--------------------------------------------------------------------------
        */

        $filename = $this->makeFilename($summary);

        $directory = storage_path('app/exports');

        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $path = $directory
            . DIRECTORY_SEPARATOR
            . $filename;


        /*
        |--------------------------------------------------------------------------
        | BUAT FILE XLSX
        |--------------------------------------------------------------------------
        */

        $zip = new ZipArchive();

        $result = $zip->open(
            $path,
            ZipArchive::CREATE | ZipArchive::OVERWRITE
        );

        if ($result !== true) {
            throw new \RuntimeException(
                'File Excel tidak dapat dibuat. Kode: ' . $result
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILE WAJIB XLSX
        |--------------------------------------------------------------------------
        */

        $zip->addFromString(
            '[Content_Types].xml',
            $this->contentTypes()
        );

        $zip->addFromString(
            '_rels/.rels',
            $this->rootRelationships()
        );

        $zip->addFromString(
            'docProps/core.xml',
            $this->coreProperties()
        );

        $zip->addFromString(
            'docProps/app.xml',
            $this->appProperties()
        );

        $zip->addFromString(
            'xl/workbook.xml',
            $this->workbook()
        );

        $zip->addFromString(
            'xl/_rels/workbook.xml.rels',
            $this->workbookRelationships()
        );

        $zip->addFromString(
            'xl/styles.xml',
            $this->styles()
        );

        $zip->addFromString(
            'xl/worksheets/sheet1.xml',
            $this->worksheet(
                $visits,
                $summary
            )
        );

        $zip->close();


        /*
        |--------------------------------------------------------------------------
        | VALIDASI HASIL
        |--------------------------------------------------------------------------
        */

        if (
            !file_exists($path)
            || filesize($path) <= 0
        ) {
            throw new \RuntimeException(
                'File Excel gagal dibuat.'
            );
        }


        /*
         * Tes lagi apakah hasilnya benar-benar ZIP/XLSX.
         */

        $testZip = new ZipArchive();

        if ($testZip->open($path) !== true) {
            throw new \RuntimeException(
                'File Excel yang dibuat tidak valid.'
            );
        }

        $testZip->close();


        return [
            $path,
            $filename,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | WORKSHEET
    |--------------------------------------------------------------------------
    */

    private function worksheet(
        Collection $visits,
        array $summary
    ): string {
        $rows = [];


        /*
        |--------------------------------------------------------------------------
        | JUDUL
        |--------------------------------------------------------------------------
        */

        $rows[] = $this->row(
            1,
            [
                $this->stringCell(
                    'A1',
                    'BUKU TAMU DIGITAL',
                    1
                ),
            ],
            32
        );


        $rows[] = $this->row(
            2,
            [
                $this->stringCell(
                    'A2',
                    'Laporan Data Kunjungan - '
                    . ($summary['location'] ?? 'Semua Lokasi'),
                    2
                ),
            ]
        );


        $rows[] = $this->row(
            3,
            [
                $this->stringCell(
                    'A3',
                    'Periode: '
                    . $this->periodText($summary),
                    2
                ),
            ]
        );


        $rows[] = $this->row(
            4,
            [
                $this->stringCell(
                    'A4',
                    'Dicetak: '
                    . now()->format('d/m/Y H:i')
                    . ' WIB',
                    2
                ),
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | RINGKASAN
        |--------------------------------------------------------------------------
        */

        $rows[] = $this->row(
            6,
            [
                $this->stringCell(
                    'A6',
                    'TOTAL KUNJUNGAN',
                    3
                ),

                $this->numberCell(
                    'B6',
                    (int) ($summary['total'] ?? 0),
                    6
                ),

                $this->stringCell(
                    'D6',
                    'SUDAH SURVEY',
                    3
                ),

                $this->numberCell(
                    'E6',
                    (int) ($summary['surveyed'] ?? 0),
                    6
                ),

                $this->stringCell(
                    'G6',
                    'BELUM SURVEY',
                    3
                ),

                $this->numberCell(
                    'H6',
                    (int) ($summary['pending'] ?? 0),
                    6
                ),

                $this->stringCell(
                    'J6',
                    'RATA-RATA RATING',
                    3
                ),

                $this->stringCell(
                    'K6',
                    number_format(
                        (float) ($summary['average'] ?? 0),
                        1
                    ) . ' / 5',
                    6
                ),
            ],
            24
        );


        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $headers = [
            'No',
            'Nomor Kunjungan',
            'Lokasi',
            'Nama Tamu',
            'Instansi / Perusahaan',
            'Pegawai Tujuan',
            'Divisi',
            'Keperluan',
            'Jumlah Orang',
            'Check In',
            'Survey',
            'Rating',
            'Status',
        ];


        $headerCells = [];

        foreach (
            $headers as $index => $header
        ) {
            $column = $this->columnName(
                $index + 1
            );

            $headerCells[] =
                $this->stringCell(
                    $column . '8',
                    $header,
                    3
                );
        }

        $rows[] = $this->row(
            8,
            $headerCells,
            28
        );


        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $rowNumber = 9;


        foreach (
            $visits as $index => $visit
        ) {
            $rating = $visit->satisfaction_rating
                ? str_repeat(
                    '★',
                    (int) $visit->satisfaction_rating
                )
                : '-';


            $status = $visit->satisfaction_rating
                ? 'Sudah Survey'
                : 'Belum Survey';


            $rows[] = $this->row(
                $rowNumber,
                [
                    $this->numberCell(
                        'A' . $rowNumber,
                        $index + 1,
                        5
                    ),

                    $this->stringCell(
                        'B' . $rowNumber,
                        $visit->visit_number ?? '-',
                        4
                    ),

                    $this->stringCell(
                        'C' . $rowNumber,
                        $visit->location?->name ?? '-',
                        4
                    ),

                    $this->stringCell(
                        'D' . $rowNumber,
                        $visit->visitor?->name ?? '-',
                        4
                    ),

                    $this->stringCell(
                        'E' . $rowNumber,
                        $visit->visitor?->company ?? '-',
                        4
                    ),

                    $this->stringCell(
                        'F' . $rowNumber,
                        $visit->employee_name
                            ?: (
                                $visit->employee?->name
                                ?? '-'
                            ),
                        4
                    ),

                    $this->stringCell(
                        'G' . $rowNumber,
                        $visit
                            ->employee
                            ?->division
                            ?->name
                        ?? '-',
                        4
                    ),

                    $this->stringCell(
                        'H' . $rowNumber,
                        $visit->purpose ?? '-',
                        4
                    ),

                    $this->numberCell(
                        'I' . $rowNumber,
                        (int) (
                            $visit->number_of_people
                            ?? 1
                        ),
                        5
                    ),

                    $this->stringCell(
                        'J' . $rowNumber,
                        $visit
                            ->check_in_at
                            ?->format('d/m/Y H:i')
                        ?? '-',
                        4
                    ),

                    $this->stringCell(
                        'K' . $rowNumber,
                        $visit
                            ->surveyed_at
                            ?->format('d/m/Y H:i')
                        ?? '-',
                        4
                    ),

                    $this->stringCell(
                        'L' . $rowNumber,
                        $rating,
                        $visit->satisfaction_rating
                            ? 7
                            : 5
                    ),

                    $this->stringCell(
                        'M' . $rowNumber,
                        $status,
                        $visit->satisfaction_rating
                            ? 8
                            : 9
                    ),
                ],
                25
            );

            $rowNumber++;
        }


        /*
        |--------------------------------------------------------------------------
        | DATA KOSONG
        |--------------------------------------------------------------------------
        */

        if ($visits->isEmpty()) {
            $rows[] = $this->row(
                9,
                [
                    $this->stringCell(
                        'A9',
                        'Tidak ada data kunjungan.',
                        4
                    ),
                ],
                30
            );
        }


        $lastRow = max(
            9,
            $rowNumber - 1
        );


        /*
        |--------------------------------------------------------------------------
        | MERGE CELL
        |--------------------------------------------------------------------------
        */

        $merges = [
            'A1:M1',
            'A2:M2',
            'A3:M3',
            'A4:M4',
        ];

        if ($visits->isEmpty()) {
            $merges[] = 'A9:M9';
        }


        $mergeXml = '';

        foreach ($merges as $merge) {
            $mergeXml .=
                '<mergeCell ref="'
                . $merge
                . '"/>';
        }


        /*
        |--------------------------------------------------------------------------
        | XML WORKSHEET
        |--------------------------------------------------------------------------
        |
        | PENTING:
        |
        | autoFilter harus berada SEBELUM mergeCells.
        |
        */

        return
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'

            . '<worksheet '
            . 'xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'

            /*
             * Dimension
             */
            . '<dimension ref="A1:M'
            . $lastRow
            . '"/>'

            /*
             * View
             */
            . '<sheetViews>'

            . '<sheetView workbookViewId="0">'

            . '<pane '
            . 'ySplit="8" '
            . 'topLeftCell="A9" '
            . 'activePane="bottomLeft" '
            . 'state="frozen"/>'

            . '</sheetView>'

            . '</sheetViews>'

            /*
             * Format
             */
            . '<sheetFormatPr '
            . 'defaultRowHeight="20"/>'

            /*
             * Columns
             */
            . '<cols>'

            . '<col min="1" max="1" width="7" customWidth="1"/>'

            . '<col min="2" max="2" width="24" customWidth="1"/>'

            . '<col min="3" max="3" width="18" customWidth="1"/>'

            . '<col min="4" max="4" width="24" customWidth="1"/>'

            . '<col min="5" max="5" width="26" customWidth="1"/>'

            . '<col min="6" max="6" width="24" customWidth="1"/>'

            . '<col min="7" max="7" width="20" customWidth="1"/>'

            . '<col min="8" max="8" width="38" customWidth="1"/>'

            . '<col min="9" max="9" width="14" customWidth="1"/>'

            . '<col min="10" max="10" width="20" customWidth="1"/>'

            . '<col min="11" max="11" width="20" customWidth="1"/>'

            . '<col min="12" max="12" width="18" customWidth="1"/>'

            . '<col min="13" max="13" width="18" customWidth="1"/>'

            . '</cols>'

            /*
             * Data
             */
            . '<sheetData>'

            . implode('', $rows)

            . '</sheetData>'

            /*
             * FILTER HARUS SEBELUM MERGE
             */
            . '<autoFilter ref="A8:M'
            . $lastRow
            . '"/>'

            /*
             * Merge
             */
            . '<mergeCells count="'
            . count($merges)
            . '">'

            . $mergeXml

            . '</mergeCells>'

            /*
             * Print setup
             */
            . '<pageMargins '
            . 'left="0.3" '
            . 'right="0.3" '
            . 'top="0.5" '
            . 'bottom="0.5" '
            . 'header="0.2" '
            . 'footer="0.2"/>'

            . '<pageSetup '
            . 'orientation="landscape" '
            . 'fitToWidth="1" '
            . 'fitToHeight="0"/>'

            . '</worksheet>';
    }


    /*
    |--------------------------------------------------------------------------
    | STRING CELL
    |--------------------------------------------------------------------------
    */

    private function stringCell(
        string $ref,
        ?string $value,
        int $style = 4
    ): string {
        $value = (string) ($value ?? '');


        /*
         * Hapus karakter kontrol ilegal XML.
         */

        $value = preg_replace(
            '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u',
            '',
            $value
        ) ?? '';


        $value = htmlspecialchars(
            $value,
            ENT_XML1 | ENT_QUOTES,
            'UTF-8'
        );


        return
            '<c r="'
            . $ref
            . '" s="'
            . $style
            . '" t="inlineStr">'

            . '<is>'

            . '<t xml:space="preserve">'

            . $value

            . '</t>'

            . '</is>'

            . '</c>';
    }


    /*
    |--------------------------------------------------------------------------
    | NUMBER CELL
    |--------------------------------------------------------------------------
    */

    private function numberCell(
        string $ref,
        int|float $value,
        int $style = 5
    ): string {
        return
            '<c r="'
            . $ref
            . '" s="'
            . $style
            . '">'

            . '<v>'
            . $value
            . '</v>'

            . '</c>';
    }


    /*
    |--------------------------------------------------------------------------
    | ROW
    |--------------------------------------------------------------------------
    */

    private function row(
        int $number,
        array $cells,
        int $height = 22
    ): string {
        return
            '<row r="'
            . $number
            . '" ht="'
            . $height
            . '" customHeight="1">'

            . implode('', $cells)

            . '</row>';
    }


    /*
    |--------------------------------------------------------------------------
    | COLUMN NAME
    |--------------------------------------------------------------------------
    */

    private function columnName(
        int $number
    ): string {
        $name = '';

        while ($number > 0) {
            $mod = ($number - 1) % 26;

            $name =
                chr(65 + $mod)
                . $name;

            $number = intdiv(
                $number - 1,
                26
            );
        }

        return $name;
    }


    /*
    |--------------------------------------------------------------------------
    | FILE NAME
    |--------------------------------------------------------------------------
    */

    private function makeFilename(
        array $summary
    ): string {
        $start =
            $summary['date_start']
            ?? null;

        $end =
            $summary['date_end']
            ?? null;


        if ($start && $end) {
            return
                'laporan-kunjungan-'
                . $start
                . '-sampai-'
                . $end
                . '.xlsx';
        }


        if ($start) {
            return
                'laporan-kunjungan-mulai-'
                . $start
                . '.xlsx';
        }


        if ($end) {
            return
                'laporan-kunjungan-sampai-'
                . $end
                . '.xlsx';
        }


        return
            'laporan-kunjungan-'
            . now()->format('Ymd-His')
            . '.xlsx';
    }


    /*
    |--------------------------------------------------------------------------
    | PERIOD TEXT
    |--------------------------------------------------------------------------
    */

    private function periodText(
        array $summary
    ): string {
        $start =
            $summary['date_start']
            ?? null;

        $end =
            $summary['date_end']
            ?? null;


        if ($start && $end) {
            return
                $this->formatDate($start)
                . ' s/d '
                . $this->formatDate($end);
        }


        if ($start) {
            return
                'Mulai '
                . $this->formatDate($start);
        }


        if ($end) {
            return
                'Sampai '
                . $this->formatDate($end);
        }


        return 'Semua Tanggal';
    }


    private function formatDate(
        string $date
    ): string {
        try {
            return \Carbon\Carbon::parse(
                $date
            )->format('d/m/Y');

        } catch (\Throwable $e) {
            return $date;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CONTENT TYPES
    |--------------------------------------------------------------------------
    */

    private function contentTypes(): string
    {
        return
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'

            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'

            . '<Default '
            . 'Extension="rels" '
            . 'ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'

            . '<Default '
            . 'Extension="xml" '
            . 'ContentType="application/xml"/>'

            . '<Override '
            . 'PartName="/xl/workbook.xml" '
            . 'ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'

            . '<Override '
            . 'PartName="/xl/worksheets/sheet1.xml" '
            . 'ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'

            . '<Override '
            . 'PartName="/xl/styles.xml" '
            . 'ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'

            . '<Override '
            . 'PartName="/docProps/core.xml" '
            . 'ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'

            . '<Override '
            . 'PartName="/docProps/app.xml" '
            . 'ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'

            . '</Types>';
    }


    /*
    |--------------------------------------------------------------------------
    | ROOT RELATIONSHIP
    |--------------------------------------------------------------------------
    */

    private function rootRelationships(): string
    {
        return
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'

            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'

            . '<Relationship '
            . 'Id="rId1" '
            . 'Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" '
            . 'Target="xl/workbook.xml"/>'

            . '<Relationship '
            . 'Id="rId2" '
            . 'Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" '
            . 'Target="docProps/core.xml"/>'

            . '<Relationship '
            . 'Id="rId3" '
            . 'Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" '
            . 'Target="docProps/app.xml"/>'

            . '</Relationships>';
    }


    /*
    |--------------------------------------------------------------------------
    | WORKBOOK
    |--------------------------------------------------------------------------
    */

    private function workbook(): string
    {
        return
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'

            . '<workbook '
            . 'xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'

            . '<bookViews>'

            . '<workbookView/>'

            . '</bookViews>'

            . '<sheets>'

            . '<sheet '
            . 'name="Laporan Kunjungan" '
            . 'sheetId="1" '
            . 'r:id="rId1"/>'

            . '</sheets>'

            . '</workbook>';
    }


    /*
    |--------------------------------------------------------------------------
    | WORKBOOK RELATIONSHIP
    |--------------------------------------------------------------------------
    */

    private function workbookRelationships(): string
    {
        return
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'

            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'

            . '<Relationship '
            . 'Id="rId1" '
            . 'Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" '
            . 'Target="worksheets/sheet1.xml"/>'

            . '<Relationship '
            . 'Id="rId2" '
            . 'Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" '
            . 'Target="styles.xml"/>'

            . '</Relationships>';
    }


    /*
    |--------------------------------------------------------------------------
    | STYLE
    |--------------------------------------------------------------------------
    */

    private function styles(): string
    {
        return
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'

            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'

            /*
             * Fonts
             */
            . '<fonts count="4">'

            . '<font>'
            . '<sz val="11"/>'
            . '<name val="Arial"/>'
            . '</font>'

            . '<font>'
            . '<b/>'
            . '<sz val="20"/>'
            . '<color rgb="FFFFFFFF"/>'
            . '<name val="Arial"/>'
            . '</font>'

            . '<font>'
            . '<sz val="11"/>'
            . '<color rgb="FF64748B"/>'
            . '<name val="Arial"/>'
            . '</font>'

            . '<font>'
            . '<b/>'
            . '<sz val="10"/>'
            . '<color rgb="FFFFFFFF"/>'
            . '<name val="Arial"/>'
            . '</font>'

            . '</fonts>'


            /*
             * Fills
             */
            . '<fills count="7">'

            . '<fill>'
            . '<patternFill patternType="none"/>'
            . '</fill>'

            . '<fill>'
            . '<patternFill patternType="gray125"/>'
            . '</fill>'

            . '<fill>'
            . '<patternFill patternType="solid">'
            . '<fgColor rgb="FF0F766E"/>'
            . '<bgColor indexed="64"/>'
            . '</patternFill>'
            . '</fill>'

            . '<fill>'
            . '<patternFill patternType="solid">'
            . '<fgColor rgb="FFF0FDFA"/>'
            . '<bgColor indexed="64"/>'
            . '</patternFill>'
            . '</fill>'

            . '<fill>'
            . '<patternFill patternType="solid">'
            . '<fgColor rgb="FF0F172A"/>'
            . '<bgColor indexed="64"/>'
            . '</patternFill>'
            . '</fill>'

            . '<fill>'
            . '<patternFill patternType="solid">'
            . '<fgColor rgb="FFDFF7EC"/>'
            . '<bgColor indexed="64"/>'
            . '</patternFill>'
            . '</fill>'

            . '<fill>'
            . '<patternFill patternType="solid">'
            . '<fgColor rgb="FFEFF6FF"/>'
            . '<bgColor indexed="64"/>'
            . '</patternFill>'
            . '</fill>'

            . '</fills>'


            /*
             * Borders
             */
            . '<borders count="3">'

            . '<border>'
            . '<left/>'
            . '<right/>'
            . '<top/>'
            . '<bottom/>'
            . '<diagonal/>'
            . '</border>'

            . '<border>'
            . '<left/>'
            . '<right/>'
            . '<top/>'
            . '<bottom style="thin">'
            . '<color rgb="FFE2E8F0"/>'
            . '</bottom>'
            . '<diagonal/>'
            . '</border>'

            . '<border>'
            . '<left style="thin">'
            . '<color rgb="FFE2E8F0"/>'
            . '</left>'
            . '<right style="thin">'
            . '<color rgb="FFE2E8F0"/>'
            . '</right>'
            . '<top style="thin">'
            . '<color rgb="FFE2E8F0"/>'
            . '</top>'
            . '<bottom style="thin">'
            . '<color rgb="FFE2E8F0"/>'
            . '</bottom>'
            . '<diagonal/>'
            . '</border>'

            . '</borders>'


            /*
             * Style XFS
             */
            . '<cellStyleXfs count="1">'

            . '<xf '
            . 'numFmtId="0" '
            . 'fontId="0" '
            . 'fillId="0" '
            . 'borderId="0"/>'

            . '</cellStyleXfs>'


            /*
             * Cell XFS
             *
             * Index 0 s/d 9
             */
            . '<cellXfs count="10">'

            /*
             * 0 normal
             */
            . '<xf '
            . 'numFmtId="0" '
            . 'fontId="0" '
            . 'fillId="0" '
            . 'borderId="0"/>'

            /*
             * 1 title
             */
            . '<xf '
            . 'numFmtId="0" '
            . 'fontId="1" '
            . 'fillId="2" '
            . 'borderId="0" '
            . 'applyAlignment="1">'
            . '<alignment '
            . 'horizontal="center" '
            . 'vertical="center"/>'
            . '</xf>'

            /*
             * 2 subtitle
             */
            . '<xf '
            . 'numFmtId="0" '
            . 'fontId="2" '
            . 'fillId="0" '
            . 'borderId="0" '
            . 'applyAlignment="1">'
            . '<alignment '
            . 'horizontal="center" '
            . 'vertical="center"/>'
            . '</xf>'

            /*
             * 3 header
             */
            . '<xf '
            . 'numFmtId="0" '
            . 'fontId="3" '
            . 'fillId="4" '
            . 'borderId="2" '
            . 'applyAlignment="1">'
            . '<alignment '
            . 'horizontal="center" '
            . 'vertical="center" '
            . 'wrapText="1"/>'
            . '</xf>'

            /*
             * 4 normal data
             */
            . '<xf '
            . 'numFmtId="0" '
            . 'fontId="0" '
            . 'fillId="0" '
            . 'borderId="1" '
            . 'applyAlignment="1">'
            . '<alignment '
            . 'vertical="center" '
            . 'wrapText="1"/>'
            . '</xf>'

            /*
             * 5 center
             */
            . '<xf '
            . 'numFmtId="0" '
            . 'fontId="0" '
            . 'fillId="0" '
            . 'borderId="1" '
            . 'applyAlignment="1">'
            . '<alignment '
            . 'horizontal="center" '
            . 'vertical="center"/>'
            . '</xf>'

            /*
             * 6 summary
             */
            . '<xf '
            . 'numFmtId="0" '
            . 'fontId="0" '
            . 'fillId="3" '
            . 'borderId="2" '
            . 'applyAlignment="1">'
            . '<alignment '
            . 'horizontal="center" '
            . 'vertical="center"/>'
            . '</xf>'

            /*
             * 7 rating
             */
            . '<xf '
            . 'numFmtId="0" '
            . 'fontId="0" '
            . 'fillId="5" '
            . 'borderId="1" '
            . 'applyAlignment="1">'
            . '<alignment '
            . 'horizontal="center" '
            . 'vertical="center"/>'
            . '</xf>'

            /*
             * 8 completed
             */
            . '<xf '
            . 'numFmtId="0" '
            . 'fontId="0" '
            . 'fillId="5" '
            . 'borderId="1" '
            . 'applyAlignment="1">'
            . '<alignment '
            . 'horizontal="center" '
            . 'vertical="center"/>'
            . '</xf>'

            /*
             * 9 pending
             */
            . '<xf '
            . 'numFmtId="0" '
            . 'fontId="0" '
            . 'fillId="6" '
            . 'borderId="1" '
            . 'applyAlignment="1">'
            . '<alignment '
            . 'horizontal="center" '
            . 'vertical="center"/>'
            . '</xf>'

            . '</cellXfs>'


            /*
             * Cell styles
             */
            . '<cellStyles count="1">'

            . '<cellStyle '
            . 'name="Normal" '
            . 'xfId="0" '
            . 'builtinId="0"/>'

            . '</cellStyles>'

            . '</styleSheet>';
    }


    /*
    |--------------------------------------------------------------------------
    | CORE PROPERTIES
    |--------------------------------------------------------------------------
    */

    private function coreProperties(): string
    {
        $date = now()
            ->utc()
            ->format('Y-m-d\TH:i:s\Z');


        return
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'

            . '<cp:coreProperties '
            . 'xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" '
            . 'xmlns:dc="http://purl.org/dc/elements/1.1/" '
            . 'xmlns:dcterms="http://purl.org/dc/terms/" '
            . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'

            . '<dc:title>'
            . 'Laporan Data Kunjungan'
            . '</dc:title>'

            . '<dc:creator>'
            . 'Buku Tamu Digital'
            . '</dc:creator>'

            . '<dcterms:created '
            . 'xsi:type="dcterms:W3CDTF">'

            . $date

            . '</dcterms:created>'

            . '</cp:coreProperties>';
    }


    /*
    |--------------------------------------------------------------------------
    | APP PROPERTIES
    |--------------------------------------------------------------------------
    */

    private function appProperties(): string
    {
        return
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'

            . '<Properties '
            . 'xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" '
            . 'xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">'

            . '<Application>'
            . 'Microsoft Excel'
            . '</Application>'

            . '</Properties>';
    }
}