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
        ]);

        Presensi::create($request->all());
        return redirect()->back()->with('success', 'Presensi berhasil!');
    }
}
