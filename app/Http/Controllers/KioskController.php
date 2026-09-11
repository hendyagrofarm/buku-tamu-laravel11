<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class KioskController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | BERANDA KIOSK
    |--------------------------------------------------------------------------
    */

    public function home(): View|RedirectResponse
    {
        $location = $this->location();

        if (!$location) {
            return $this->locationPicker();
        }

        $today = now()->toDateString();

        $base = Visit::where(
            'location_id',
            $location->id
        );

        $stats = [
            'today' => (clone $base)
                ->whereDate(
                    'check_in_at',
                    $today
                )
                ->count(),

            'pending_survey' => (clone $base)
                ->whereDate(
                    'check_in_at',
                    $today
                )
                ->whereNull(
                    'satisfaction_rating'
                )
                ->count(),

            'surveyed' => (clone $base)
                ->whereDate(
                    'check_in_at',
                    $today
                )
                ->whereNotNull(
                    'satisfaction_rating'
                )
                ->count(),
        ];

        $recentVisits = Visit::with([
                'visitor',
                'location',
                'employee',
            ])
            ->where(
                'location_id',
                $location->id
            )
            ->whereDate(
                'check_in_at',
                $today
            )
            ->latest('check_in_at')
            ->limit(5)
            ->get();

        return view(
            'kiosk.home',
            compact(
                'stats',
                'recentVisits',
                'location'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PILIH LOKASI
    |--------------------------------------------------------------------------
    */

    public function locationPicker(): View
    {
        $locations = Location::where(
                'is_active',
                true
            )
            ->orderBy('name')
            ->get();

        return view(
            'kiosk.location-picker',
            compact('locations')
        );
    }


    public function selectLocation(
        Location $location
    ): RedirectResponse
    {
        abort_unless(
            $location->is_active,
            404
        );

        session([
            'kiosk_location_id' =>
                $location->id
        ]);

        return redirect()
            ->route('kiosk.home');
    }


    /*
    |--------------------------------------------------------------------------
    | FORM CHECK IN
    |--------------------------------------------------------------------------
    */

    public function create(): View|RedirectResponse
    {
        $location = $this->location();

        if (!$location) {
            return redirect()
                ->route(
                    'kiosk.location.picker'
                );
        }

        return view(
            'kiosk.register',
            compact('location')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN CHECK IN TAMU
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request
    ): RedirectResponse
    {
        $location = $this->location();

        if (!$location) {
            return redirect()
                ->route(
                    'kiosk.location.picker'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'phone' => [
                    'required',
                    'string',
                    'max:30',
                ],

                'company' => [
                    'required',
                    'string',
                    'max:150',
                ],

                'employee_name' => [
                    'required',
                    'string',
                    'max:150',
                ],

                'purpose' => [
                    'required',
                    'string',
                    'max:500',
                ],

                'number_of_people' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:20',
                ],

                'identity_photo' => [
                    'required',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:5120',
                ],

                'privacy' => [
                    'accepted',
                ],
            ],
            [
                'name.required' =>
                    'Nama lengkap wajib diisi.',

                'phone.required' =>
                    'Nomor telepon wajib diisi.',

                'company.required' =>
                    'Asal perusahaan atau instansi wajib diisi.',

                'employee_name.required' =>
                    'Nama pegawai yang ditemui wajib diisi.',

                'purpose.required' =>
                    'Keperluan kunjungan wajib diisi.',

                'number_of_people.required' =>
                    'Jumlah orang wajib diisi.',

                'identity_photo.required' =>
                    'Silakan ambil foto KTP atau SIM terlebih dahulu.',

                'identity_photo.image' =>
                    'Foto identitas harus berupa gambar.',

                'identity_photo.mimes' =>
                    'Format foto harus JPG, JPEG, PNG, atau WEBP.',

                'identity_photo.max' =>
                    'Ukuran foto identitas maksimal 5 MB.',

                'privacy.accepted' =>
                    'Silakan menyetujui penggunaan data untuk keperluan kunjungan.',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | SIMPAN FOTO IDENTITAS
        |--------------------------------------------------------------------------
        */

        $identityPhotoPath = null;

        try {

            $identityPhotoPath = $request
                ->file('identity_photo')
                ->store(
                    'identity-documents',
                    'local'
                );


            /*
            |--------------------------------------------------------------------------
            | SIMPAN DATA DATABASE
            |--------------------------------------------------------------------------
            */

            $visit = DB::transaction(
                function () use (
                    $validated,
                    $location,
                    $identityPhotoPath
                ) {

                    /*
                     * Cari / buat data tamu
                     * berdasarkan nomor telepon.
                     */

                    $visitor = Visitor::updateOrCreate(
                        [
                            'phone' =>
                                $validated['phone'],
                        ],
                        [
                            'name' =>
                                $validated['name'],

                            'company' =>
                                $validated['company'],
                        ]
                    );


                    /*
                     * Buat nomor kunjungan unik.
                     */

                    do {

                        $visitNumber =
                            'BT-' .
                            now()->format('Ymd') .
                            '-' .
                            Str::upper(
                                Str::random(4)
                            );

                    } while (
                        Visit::where(
                            'visit_number',
                            $visitNumber
                        )->exists()
                    );


                    /*
                     * Simpan kunjungan.
                     *
                     * satisfaction_rating masih NULL
                     * karena survey dilakukan saat
                     * tamu akan pulang.
                     */

                    return Visit::create(
                        [
                            'visit_number' =>
                                $visitNumber,

                            'visitor_id' =>
                                $visitor->id,

                            'location_id' =>
                                $location->id,

                            'employee_id' =>
                                null,

                            'employee_name' =>
                                $validated[
                                    'employee_name'
                                ],

                            'purpose' =>
                                $validated[
                                    'purpose'
                                ],

                            'number_of_people' =>
                                $validated[
                                    'number_of_people'
                                ],

                            'identity_photo' =>
                                $identityPhotoPath,

                            'check_in_at' =>
                                now(),

                            /*
                             * Belum survey.
                             */
                            'status' =>
                                'active',
                        ]
                    );
                }
            );

        } catch (Throwable $e) {

            /*
             * Kalau database gagal,
             * foto yang sudah tersimpan
             * dihapus kembali.
             */

            if (
                $identityPhotoPath &&
                Storage::disk('local')
                    ->exists(
                        $identityPhotoPath
                    )
            ) {
                Storage::disk('local')
                    ->delete(
                        $identityPhotoPath
                    );
            }

            throw $e;
        }


        /*
        |--------------------------------------------------------------------------
        | PENTING
        |--------------------------------------------------------------------------
        |
        | Setelah Check In TIDAK lagi masuk
        | langsung ke halaman Survey.
        |
        | Sekarang masuk ke halaman sukses.
        |
        */

        return redirect()
            ->route(
                'kiosk.visit.success',
                $visit
            )
            ->with(
                'checkin_success',
                true
            );
    }


    /*
    |--------------------------------------------------------------------------
    | STATUS KUNJUNGAN
    |--------------------------------------------------------------------------
    */

    public function status(): View|RedirectResponse
    {
        $location = $this->location();

        if (!$location) {
            return redirect()
                ->route(
                    'kiosk.location.picker'
                );
        }

        $visits = Visit::with([
                'visitor',
                'location',
                'employee',
            ])
            ->where(
                'location_id',
                $location->id
            )
            ->whereDate(
                'check_in_at',
                today()
            )
            ->latest('check_in_at')
            ->get();

        return view(
            'kiosk.status',
            compact(
                'visits',
                'location'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DAFTAR TAMU YANG BELUM SURVEY
    |--------------------------------------------------------------------------
    |
    | Digunakan saat tamu akan pulang.
    |
    */

    public function surveyIndex(): View|RedirectResponse
    {
        $location = $this->location();

        if (!$location) {
            return redirect()
                ->route(
                    'kiosk.location.picker'
                );
        }

        $visits = Visit::with([
                'visitor',
                'location',
            ])
            ->where(
                'location_id',
                $location->id
            )
            ->whereDate(
                'check_in_at',
                today()
            )
            ->whereNull(
                'satisfaction_rating'
            )
            ->latest('check_in_at')
            ->get();

        return view(
            'kiosk.survey-index',
            compact(
                'visits',
                'location'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FORM SURVEY
    |--------------------------------------------------------------------------
    */

    public function survey(
        Visit $visit
    ): View|RedirectResponse
    {
        $location = $this->location();

        if (!$location) {
            return redirect()
                ->route(
                    'kiosk.location.picker'
                );
        }


        /*
         * Pastikan kunjungan berasal
         * dari lokasi kiosk yang aktif.
         */

        abort_unless(
            (int) $visit->location_id ===
            (int) $location->id,
            404
        );


        /*
         * Kalau sudah pernah survey,
         * tidak boleh survey lagi.
         */

        if (
            $visit->satisfaction_rating !== null
        ) {
            return redirect()
                ->route(
                    'kiosk.visit.success',
                    $visit
                );
        }


        $visit->load([
            'visitor',
            'location',
        ]);


        return view(
            'kiosk.survey',
            compact('visit')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN SURVEY
    |--------------------------------------------------------------------------
    */

    public function submitSurvey(
        Request $request,
        Visit $visit
    ): RedirectResponse
    {
        $location = $this->location();

        if (!$location) {
            return redirect()
                ->route(
                    'kiosk.location.picker'
                );
        }


        /*
         * Pastikan lokasi sama.
         */

        abort_unless(
            (int) $visit->location_id ===
            (int) $location->id,
            404
        );


        /*
         * Jangan simpan survey dua kali.
         */

        if (
            $visit->satisfaction_rating !== null
        ) {
            return redirect()
                ->route(
                    'kiosk.visit.success',
                    $visit
                );
        }


        /*
         * Validasi rating.
         */

        $validated = $request->validate(
            [
                'rating' => [
                    'required',
                    'integer',
                    'between:1,5',
                ],
            ],
            [
                'rating.required' =>
                    'Silakan pilih rating bintang terlebih dahulu.',

                'rating.between' =>
                    'Rating harus antara 1 sampai 5 bintang.',
            ]
        );


        /*
         * Simpan survey.
         */

        $visit->update(
            [
                'satisfaction_rating' =>
                    $validated['rating'],

                'surveyed_at' =>
                    now(),

                'status' =>
                    'completed',
            ]
        );


        /*
         * Setelah survey selesai,
         * tampilkan halaman Terima Kasih.
         */

        return redirect()
            ->route(
                'kiosk.visit.success',
                $visit
            )
            ->with(
                'survey_success',
                true
            );
    }


    /*
    |--------------------------------------------------------------------------
    | HALAMAN SUKSES
    |--------------------------------------------------------------------------
    |
    | Halaman ini dipakai untuk:
    |
    | 1. Setelah Check In
    | 2. Setelah Survey
    |
    | Tampilan dibedakan berdasarkan
    | satisfaction_rating.
    |
    */

    public function success(
        Visit $visit
    ): View|RedirectResponse
    {
        $location = $this->location();

        if (!$location) {
            return redirect()
                ->route(
                    'kiosk.location.picker'
                );
        }


        abort_unless(
            (int) $visit->location_id ===
            (int) $location->id,
            404
        );


        $visit->load([
            'visitor',
            'location',
        ]);


        return view(
            'kiosk.success',
            compact('visit')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | AMBIL LOKASI KIOSK DARI SESSION
    |--------------------------------------------------------------------------
    */

    private function location(): ?Location
    {
        $id = session(
            'kiosk_location_id'
        );


        return $id
            ? Location::where(
                'id',
                $id
            )
                ->where(
                    'is_active',
                    true
                )
                ->first()
            : null;
    }
}