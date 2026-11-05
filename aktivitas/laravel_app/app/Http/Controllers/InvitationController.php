<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function index(): View
    {
        $acara = DB::table('acara')->where('id', 1)->first();

        abort_if(!$acara, 404, 'Data acara belum ada. Periksa tabel acara di phpMyAdmin.');

        $acara = (array) $acara;
        $ucapanList = DB::table('rsvp')
            ->select('nama', 'ucapan')
            ->where('acara_id', 1)
            ->where('ucapan', '<>', '')
            ->orderByDesc('id')
            ->limit(20)
            ->get()
            ->map(fn ($item): array => (array) $item)
            ->all();

        $bulanIndonesia = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];
        $hariIndonesia = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $timestampTanggal = strtotime($acara['tanggal']);
        $nomorBulan = (int) date('n', $timestampTanggal);

        $tanggalLengkap = $hariIndonesia[(int) date('w', $timestampTanggal)]
            . ', ' . date('j', $timestampTanggal)
            . ' ' . $bulanIndonesia[$nomorBulan]
            . ' ' . date('Y', $timestampTanggal);
        $tanggalPendek = date('d · m · Y', $timestampTanggal);
        $tanggalHighlight = strtoupper(
            date('j', $timestampTanggal)
            . ' ' . $bulanIndonesia[$nomorBulan]
            . ' ' . date('Y', $timestampTanggal)
        );

        return view('undangan', [
            'acara' => $acara,
            'ucapanList' => $ucapanList,
            'flash' => session('flash'),
            'tanggalLengkap' => $tanggalLengkap,
            'tanggalPendek' => $tanggalPendek,
            'tanggalHighlight' => $tanggalHighlight,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'kehadiran' => ['required', 'in:hadir,tidak'],
            'jumlah' => ['nullable', 'integer', 'min:1', 'max:5'],
            'ucapan' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validated['kehadiran'] === 'hadir' && empty($validated['jumlah'])) {
            return back()->withErrors(['jumlah' => 'Isi jumlah tamu antara 1 dan 5.'])->withInput();
        }

        DB::table('rsvp')->insert([
            'acara_id' => 1,
            'nama' => $validated['nama'],
            'kehadiran' => $validated['kehadiran'],
            'jumlah' => $validated['kehadiran'] === 'hadir' ? (int) $validated['jumlah'] : 0,
            'ucapan' => $validated['ucapan'] ?? '',
        ]);

        return redirect()->route('invitation.index')
            ->with('flash', 'Terima kasih! RSVP berhasil disimpan.')
            ->withFragment('rsvp');
    }
}
