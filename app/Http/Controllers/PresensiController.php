<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'location' => 'required|string',
            'status' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'chekout' => 'nullable|date',
        ]);

        $data = $request->all();
        $data['checkout'] = null;
        $presensi = Presensi::create($data);

        return redirect()->back()->with(['success' => 'Presensi berhasil!', 'presensi_id' => $presensi->id, 'checkedIn' => "true"]);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'location' => 'required|string',
        ]);
        $presensi = Presensi::findOrFail($id);

        $presensi->update([
            'status' => 'pulang',
            'location' => $request->location,
            'checkout' => now()
        ]);

        return redirect()->back()->with(['success' => 'Presensi pulang berhasil!', 'presensi_id' => $presensi->id, 'checkedIn' => "false"]);
    }
}
