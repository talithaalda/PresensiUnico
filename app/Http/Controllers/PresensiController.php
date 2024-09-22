<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'location' => 'required|string',
            'status' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);

        $presensi = Presensi::create($request->all());
        $user = $presensi->user;
        $user->update(['checkedIn' => true]);
        return redirect()->back()->with(['success' => 'Presensi berhasil!']);
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
        $user = $presensi->user;
        $user->update(['checkedIn' => false]);
        return redirect()->back()->with(['success' => 'Presensi pulang berhasil!']);
    }
}
