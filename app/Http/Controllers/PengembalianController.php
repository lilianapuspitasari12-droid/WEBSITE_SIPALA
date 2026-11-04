<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PengembalianController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Peminjaman::with(
            'user',
            'items.alat',
            'pengembalian'
        )->where('status', 'dipinjam');

        if (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $peminjamans = $query->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pengembalian.index', compact('peminjamans'));
    }

    /**
     * Detail pengembalian dan form pengajuan.
     */
    public function show(
        Request $request,
        Peminjaman $peminjaman
    ): View {
        $user = $request->user();

        abort_unless(
            $user->isAdmin() ||
            (int) $peminjaman->user_id === (int) $user->id,
            403,
            'Kamu tidak memiliki akses ke peminjaman ini.'
        );

        $peminjaman->load(
            'user',
            'items.alat',
            'pengembalian'
        );

        abort_unless(
            $peminjaman->status === 'dipinjam',
            404,
            'Peminjaman aktif tidak ditemukan.'
        );

        return view('pengembalian.show', compact('peminjaman'));
    }

    /**
     * Riwayat transaksi peminjaman.
     */
    public function riwayat(Request $request): View
    {
        $user = $request->user();

        $query = Peminjaman::with(
            'user',
            'items.alat',
            'pengembalian'
        );

        if (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('q')) {
            $kata = $request->query('q');

            $query->where(function ($q) use ($kata) {
                $q->where('kode', 'like', "%{$kata}%")
                    ->orWhereHas('items.alat', function ($alat) use ($kata) {
                        $alat->where('nama', 'like', "%{$kata}%")
                            ->orWhere('kode', 'like', "%{$kata}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $total = (clone $query)->count();

        $disetujui = (clone $query)
            ->whereIn('status', [
                'disetujui',
                'dipinjam',
                'dikembalikan'
            ])
            ->count();

        $selesai = (clone $query)
            ->where('status', 'dikembalikan')
            ->count();

        $peminjamans = $query->latest()
            ->paginate(10)
            ->withQueryString();

        return view('riwayat.index', compact(
            'peminjamans',
            'total',
            'disetujui',
            'selesai'
        ));
    }

    /**
     * Siswa atau guru mengajukan pengembalian.
     */
    public function store(
        Request $request,
        Peminjaman $peminjaman
    ): RedirectResponse {
        abort_unless(! $request->user()->isAdmin(), 403);

        abort_unless(
            (int) $request->user()->id === (int) $peminjaman->user_id,
            403,
            'Kamu tidak berhak mengajukan pengembalian ini.'
        );

        abort_unless(
            $peminjaman->status === 'dipinjam',
            422,
            'Peminjaman ini tidak sedang berjalan.'
        );

        $lama = Pengembalian::where(
            'peminjaman_id',
            $peminjaman->id
        )->first();

        abort_if(
            $lama && $lama->status !== 'ditolak',
            422,
            'Pengajuan pengembalian sudah ada atau sudah dikonfirmasi.'
        );

        $data = $request->validate([
            'bukti' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,mp4,mov,webm',
                'max:20480',
            ],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array'],
            'items.*.kondisi' => [
                'required',
                Rule::in(['baik', 'rusak', 'hilang']),
            ],
            'items.*.catatan' => ['nullable', 'string', 'max:255'],
        ], [
            'bukti.required' => 'Foto atau video bukti pengembalian wajib diunggah.',
            'bukti.mimes' => 'Bukti harus berupa JPG, JPEG, PNG, WEBP, MP4, MOV, atau WEBM.',
            'bukti.max' => 'Ukuran bukti maksimal 20 MB.',
            'items.*.kondisi.required' => 'Kondisi setiap alat wajib dipilih.',
            'items.*.kondisi.in' => 'Pilih kondisi baik, rusak, atau hilang.',
        ]);

        $items = $peminjaman->items()->get();
        $rincian = [];

        foreach ($items as $item) {
            $masuk = $data['items'][$item->id] ?? null;

            abort_if(
                ! $masuk,
                422,
                'Kondisi setiap alat wajib dipilih.'
            );

            $rincian[$item->id] = [
                'kondisi' => $masuk['kondisi'],
                'jumlah' => (int) $item->jumlah,
                'catatan' => $masuk['catatan'] ?? null,
            ];
        }

        $file = $request->file('bukti');
        $path = $file->store('bukti-pengembalian', 'public');

        if (! $path) {
            throw new \RuntimeException(
                'Bukti pengembalian gagal disimpan.'
            );
        }

        try {
            DB::transaction(function () use (
                $request,
                $peminjaman,
                $lama,
                $data,
                $rincian,
                $file,
                $path
            ) {
                $pengembalian = $lama
                    ? Pengembalian::whereKey($lama->id)
                        ->lockForUpdate()
                        ->firstOrFail()
                    : new Pengembalian();

                abort_if(
                    $pengembalian->exists &&
                    $pengembalian->status !== 'ditolak',
                    422,
                    'Pengajuan pengembalian sudah diproses.'
                );

                $pengembalian->fill([
                    'peminjaman_id' => $peminjaman->id,
                    'user_id' => $request->user()->id,
                    'bukti_path' => $path,
                    'bukti_mime' => $file->getMimeType(),
                    'catatan' => $data['catatan'] ?? null,
                    'detail_items' => $rincian,
                    'status' => 'menunggu',
                    'catatan_petugas' => null,
                    'diproses_oleh' => null,
                    'diproses_pada' => null,
                ])->save();
            });
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($path);
            throw $e;
        }

        if (
            $lama &&
            $lama->bukti_path &&
            $lama->bukti_path !== $path
        ) {
            Storage::disk('public')->delete($lama->bukti_path);
        }

        return redirect()
            ->route('pengembalian.show', $peminjaman)
            ->with(
                'sukses',
                'Bukti pengembalian berhasil dikirim. Tunggu pemeriksaan petugas.'
            );
    }

    /**
     * Admin mengonfirmasi pengembalian dan menyimpan denda.
     */
    public function konfirmasi(
        Request $request,
        Pengembalian $pengembalian
    ): RedirectResponse {
        abort_unless($request->user()->isAdmin(), 403);

        $data = $request->validate([
            'catatan_petugas' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use (
            $request,
            $pengembalian,
            $data
        ) {
            $pengembalian = Pengembalian::whereKey($pengembalian->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $pengembalian->status === 'menunggu',
                422,
                'Pengajuan ini sudah diproses atau tidak sedang menunggu.'
            );

            $peminjaman = Peminjaman::whereKey(
                $pengembalian->peminjaman_id
            )->lockForUpdate()->firstOrFail();

            abort_unless(
                $peminjaman->status === 'dipinjam',
                422,
                'Status peminjaman tidak sesuai untuk pengembalian.'
            );

            foreach ($peminjaman->items()->with('alat')->get() as $item) {
                $rincian = $pengembalian->detail_items ?? [];
                $detail = $rincian[$item->id] ?? null;

                abort_if(
                    ! is_array($detail),
                    422,
                    'Rincian kondisi barang tidak ditemukan.'
                );

                if (isset($detail['kondisi'])) {
                    $kondisi = $detail['kondisi'];
                    $jumlah = (int) ($detail['jumlah'] ?? $item->jumlah);

                    abort_unless(
                        in_array(
                            $kondisi,
                            ['baik', 'rusak', 'hilang'],
                            true
                        ) && $jumlah === (int) $item->jumlah,
                        422,
                        'Rincian kondisi barang tidak valid.'
                    );

                    $baik = $kondisi === 'baik' ? $jumlah : 0;
                    $rusak = $kondisi === 'rusak' ? $jumlah : 0;
                    $hilang = $kondisi === 'hilang' ? $jumlah : 0;
                } else {
                    $baik = (int) ($detail['jumlah_baik'] ?? 0);
                    $rusak = (int) ($detail['jumlah_rusak'] ?? 0);
                    $hilang = (int) ($detail['jumlah_hilang'] ?? 0);

                    abort_unless(
                        $baik >= 0 &&
                        $rusak >= 0 &&
                        $hilang >= 0 &&
                        ($baik + $rusak + $hilang) === (int) $item->jumlah,
                        422,
                        'Jumlah kondisi barang tidak sesuai dengan jumlah pinjaman.'
                    );
                }

                // Hanya alat berkondisi baik yang kembali ke stok.
                if ($baik > 0) {
                    $item->alat->tambahStok($baik);
                }

                $kondisiKembali = $baik === (int) $item->jumlah
                    ? 'baik'
                    : ($rusak === (int) $item->jumlah
                        ? 'rusak'
                        : ($hilang === (int) $item->jumlah
                            ? 'hilang'
                            : 'campuran'));

                $item->jumlah_kembali = $baik + $rusak;
                $item->kondisi_kembali = $kondisiKembali;
                $item->catatan = $detail['catatan'] ?? $item->catatan;
                $item->save();
            }

            // Simpan tanggal pengembalian terlebih dahulu.
            $peminjaman->tanggal_kembali = today();
            $peminjaman->status = 'dikembalikan';

            // Hitung denda berdasarkan tanggal jatuh tempo
            // dan tanggal pengembalian sebenarnya.
            $peminjaman->total_denda = $peminjaman->hitungDenda();

            $peminjaman->status_denda = $peminjaman->total_denda > 0
                ? 'belum_dibayar'
                : 'tidak_ada';

            $peminjaman->denda_dibayar_pada = null;
            $peminjaman->denda_dibayar_oleh = null;

            $peminjaman->catatan_petugas = $data['catatan_petugas'] ?? null;
            $peminjaman->diproses_oleh = $request->user()->id;

            $peminjaman->save();

            $pengembalian->status = 'dikonfirmasi';
            $pengembalian->catatan_petugas = $data['catatan_petugas'] ?? null;
            $pengembalian->diproses_oleh = $request->user()->id;
            $pengembalian->diproses_pada = now();
            $pengembalian->save();
        });

        return redirect()
            ->route('pengembalian.index')
            ->with(
                'sukses',
                'Pengembalian berhasil dikonfirmasi dan denda berhasil dihitung.'
            );
    }

    /**
     * Admin menolak pengajuan pengembalian.
     */
    public function tolak(
        Request $request,
        Pengembalian $pengembalian
    ): RedirectResponse {
        abort_unless($request->user()->isAdmin(), 403);

        $data = $request->validate([
            'catatan_petugas' => ['required', 'string', 'max:1000'],
        ], [
            'catatan_petugas.required' => 'Alasan penolakan wajib diisi.',
        ]);

        DB::transaction(function () use (
            $request,
            $pengembalian,
            $data
        ) {
            $pengembalian = Pengembalian::whereKey($pengembalian->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $pengembalian->status === 'menunggu',
                422,
                'Pengajuan ini sudah diproses atau tidak sedang menunggu.'
            );

            $peminjaman = Peminjaman::whereKey(
                $pengembalian->peminjaman_id
            )->lockForUpdate()->firstOrFail();

            abort_unless(
                $peminjaman->status === 'dipinjam',
                422,
                'Status peminjaman tidak sesuai.'
            );

            $pengembalian->status = 'ditolak';
            $pengembalian->catatan_petugas = $data['catatan_petugas'];
            $pengembalian->diproses_oleh = $request->user()->id;
            $pengembalian->diproses_pada = now();
            $pengembalian->save();

            $peminjaman->catatan_petugas = $data['catatan_petugas'];
            $peminjaman->save();
        });

        return redirect()
            ->route('pengembalian.index')
            ->with(
                'sukses',
                'Pengajuan pengembalian ditolak. Stok tidak diubah.'
            );
    }
}
