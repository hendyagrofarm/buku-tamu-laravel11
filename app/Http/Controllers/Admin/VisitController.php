<?php

namespace App\Http\Controllers\Admin;

use App\Exports\VisitExcelExporter;
use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class VisitController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DATA KUNJUNGAN
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $location = $this->selectedLocation($request);

        $visits = $this->applyFilters(
            Visit::with([
                'visitor',
                'employee.division',
                'location',
            ]),
            $request,
            $location
        )
            ->latest('check_in_at')
            ->paginate(20)
            ->withQueryString();

        $locations = auth()->user()->isAdmin()
            ? Location::where('is_active', true)
                ->orderBy('name')
                ->get()
            : collect();

        return view(
            'admin.visits.index',
            compact(
                'visits',
                'locations',
                'location'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EXPORT EXCEL
    |--------------------------------------------------------------------------
    */

    public function export(Request $request)
    {
        $location = $this->selectedLocation($request);

        /*
         * Export menggunakan filter yang SAMA
         * dengan tabel Data Kunjungan.
         */
        $visits = $this->applyFilters(
            Visit::with([
                'visitor',
                'employee.division',
                'location',
            ]),
            $request,
            $location
        )
            ->latest('check_in_at')
            ->get();


        $summary = [
            'total' => $visits->count(),

            'surveyed' => $visits
                ->whereNotNull('satisfaction_rating')
                ->count(),

            'pending' => $visits
                ->whereNull('satisfaction_rating')
                ->count(),

            'average' => round(
                (float) $visits
                    ->whereNotNull('satisfaction_rating')
                    ->avg('satisfaction_rating'),
                1
            ),

            'location' =>
                $location?->name
                ?? 'Semua Lokasi',

            /*
             * Kirim periode ke exporter.
             */
            'date_start' =>
                $request->input('date_start'),

            'date_end' =>
                $request->input('date_end'),
        ];


        [$path, $filename] =
            (new VisitExcelExporter())
                ->download(
                    $visits,
                    $summary
                );


        return response()
            ->download(
                $path,
                $filename,
                [
                    'Content-Type' =>
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]
            )
            ->deleteFileAfterSend(true);
    }


    /*
    |--------------------------------------------------------------------------
    | DETAIL KUNJUNGAN
    |--------------------------------------------------------------------------
    */

    public function show(
        Visit $visit
    ): View {
        $this->ensureVisitCanBeAccessed(
            $visit
        );

        $visit->load([
            'visitor',
            'employee.division',
            'creator',
            'location',
        ]);

        return view(
            'admin.visits.show',
            compact('visit')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FOTO IDENTITAS
    |--------------------------------------------------------------------------
    */

    public function identity(
        Visit $visit
    ): BinaryFileResponse {
        $this->ensureVisitCanBeAccessed(
            $visit
        );

        abort_if(
            empty($visit->identity_photo),
            404,
            'Foto identitas tidak tersedia.'
        );

        abort_unless(
            Storage::disk('local')
                ->exists(
                    $visit->identity_photo
                ),
            404,
            'File foto identitas tidak ditemukan.'
        );

        $path = Storage::disk('local')
            ->path(
                $visit->identity_photo
            );

        $mimeType =
            mime_content_type($path)
            ?: 'image/jpeg';

        return response()->file(
            $path,
            [
                'Content-Type' =>
                    $mimeType,

                'Content-Disposition' =>
                    'inline',

                'Cache-Control' =>
                    'private, no-store, no-cache, must-revalidate',

                'Pragma' =>
                    'no-cache',

                'Expires' =>
                    '0',
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CEK HAK AKSES KUNJUNGAN
    |--------------------------------------------------------------------------
    */

    private function ensureVisitCanBeAccessed(
        Visit $visit
    ): void {
        $user = auth()->user();

        /*
         * Admin boleh membuka semua lokasi.
         */
        if ($user->isAdmin()) {
            return;
        }

        /*
         * Petugas hanya boleh membuka
         * kunjungan lokasi miliknya.
         */
        abort_unless(
            $user->location
            && (int) $visit->location_id
                === (int) $user->location->id,
            404
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PILIH LOKASI
    |--------------------------------------------------------------------------
    */

    private function selectedLocation(
        Request $request
    ): ?Location {
        $user = auth()->user();

        /*
         * Petugas biasa otomatis menggunakan
         * lokasi yang dimiliki user.
         */
        if (!$user->isAdmin()) {
            return $user->location;
        }

        /*
         * Admin bisa memilih semua lokasi
         * atau salah satu lokasi.
         */
        if ($request->filled('location_id')) {
            return Location::find(
                $request->integer(
                    'location_id'
                )
            );
        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | FILTER DATA
    |--------------------------------------------------------------------------
    |
    | Fungsi ini digunakan oleh:
    |
    | - halaman Data Kunjungan
    | - Export Excel
    |
    | Jadi hasil tabel dan Excel akan sama.
    |
    */

    private function applyFilters(
        $query,
        Request $request,
        ?Location $location
    ) {

        /*
        |--------------------------------------------------------------------------
        | LOKASI
        |--------------------------------------------------------------------------
        */

        if ($location) {
            $query->where(
                'location_id',
                $location->id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PENCARIAN
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = trim(
                (string) $request->input(
                    'search'
                )
            );

            $query->where(
                function ($subQuery) use ($search) {
                    $subQuery
                        ->where(
                            'visit_number',
                            'like',
                            '%' . $search . '%'
                        )
                        ->orWhereHas(
                            'visitor',
                            function ($visitor) use ($search) {
                                $visitor
                                    ->where(
                                        'name',
                                        'like',
                                        '%' . $search . '%'
                                    )
                                    ->orWhere(
                                        'company',
                                        'like',
                                        '%' . $search . '%'
                                    )
                                    ->orWhere(
                                        'phone',
                                        'like',
                                        '%' . $search . '%'
                                    );
                            }
                        );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            /*
             * Belum Survey
             */
            if (
                $request->input('status')
                === 'active'
            ) {
                $query->whereNull(
                    'satisfaction_rating'
                );
            }

            /*
             * Sudah Survey
             */
            if (
                $request->input('status')
                === 'completed'
            ) {
                $query->whereNotNull(
                    'satisfaction_rating'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | AMBIL TANGGAL DARI FORM
        |--------------------------------------------------------------------------
        */

        $dateStart = $request->input(
            'date_start'
        );

        $dateEnd = $request->input(
            'date_end'
        );


        /*
        |--------------------------------------------------------------------------
        | TANGGAL AWAL + TANGGAL AKHIR
        |--------------------------------------------------------------------------
        |
        | Contoh:
        |
        | 04/09/2026 s/d 04/09/2026
        |
        | berarti:
        |
        | 2026-09-04 00:00:00
        | sampai
        | 2026-09-04 23:59:59
        |
        */

        if ($dateStart && $dateEnd) {

            try {
                $start = Carbon::createFromFormat(
                    'Y-m-d',
                    $dateStart
                )->startOfDay();

                $end = Carbon::createFromFormat(
                    'Y-m-d',
                    $dateEnd
                )->endOfDay();


                /*
                 * Jika tanggal terbalik,
                 * otomatis kita tukar.
                 */
                if ($start->greaterThan($end)) {
                    $oldStart = $start->copy();

                    $start = Carbon::createFromFormat(
                        'Y-m-d',
                        $dateEnd
                    )->startOfDay();

                    $end = Carbon::createFromFormat(
                        'Y-m-d',
                        $dateStart
                    )->endOfDay();
                }


                $query->whereBetween(
                    'check_in_at',
                    [
                        $start,
                        $end,
                    ]
                );

            } catch (\Throwable $e) {
                /*
                 * Jika tanggal tidak valid,
                 * jangan membuat halaman error.
                 */
            }
        }


        /*
        |--------------------------------------------------------------------------
        | HANYA TANGGAL AWAL
        |--------------------------------------------------------------------------
        */

        elseif ($dateStart) {

            try {
                $start =
                    Carbon::createFromFormat(
                        'Y-m-d',
                        $dateStart
                    )->startOfDay();

                $query->where(
                    'check_in_at',
                    '>=',
                    $start
                );

            } catch (\Throwable $e) {
                //
            }
        }


        /*
        |--------------------------------------------------------------------------
        | HANYA TANGGAL AKHIR
        |--------------------------------------------------------------------------
        */

        elseif ($dateEnd) {

            try {
                $end =
                    Carbon::createFromFormat(
                        'Y-m-d',
                        $dateEnd
                    )->endOfDay();

                $query->where(
                    'check_in_at',
                    '<=',
                    $end
                );

            } catch (\Throwable $e) {
                //
            }
        }


        return $query;
    }
}