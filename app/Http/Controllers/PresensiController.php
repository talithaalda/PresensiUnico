<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'location' => 'required|string',
            'status' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'image_datang' => 'required',
        ],);
        if ($request->image_datang) {
            $image_datang_parts = explode(";base64,", $validatedData['image_datang']);
            $image_datang_base64 = base64_decode($image_datang_parts[1]);
            $fileName_datang = uniqid() . '_datang.jpg';
            $file_datang = 'absensi-image/' . $fileName_datang;
            Storage::disk('public')->put($file_datang, $image_datang_base64);
            $validatedData['image_datang'] = $file_datang;
        }
        $presensi = Presensi::create($validatedData);
        $user = $presensi->user;
        $user->update(['checkedIn' => true]);
        return redirect()->back()->with(['success' => 'Presensi berhasil!']);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'location' => 'required|string',
            'image_pulang' => 'required',

        ],);
        if ($request->image_pulang) {

            if ($request->oldImagePulang) {
                Storage::delete($request->oldImagePulang);
            }

            if ($request->image_pulang) {
                $image_pulang_parts = explode(";base64,", $validatedData['image_pulang']);
                $image_pulang_base64 = base64_decode($image_pulang_parts[1]);
                $fileName_pulang = uniqid() . '_pulang.jpg';
                $file_pulang = 'absensi-image/' . $fileName_pulang;
                Storage::disk('public')->put($file_pulang, $image_pulang_base64);
                $validatedData['image_pulang'] = $file_pulang;
            }
        }
        $presensi = Presensi::findOrFail($id);

        $presensi->update([
            'status' => 'pulang',
            'location' => $validatedData['location'],
            'checkout' => now(),
            'image_pulang' => $validatedData['image_pulang'] ?? $request->oldImagePulang
        ]);
        $user = $presensi->user;
        $user->update(['checkedIn' => false]);
        return redirect()->back()->with(['success' => 'Presensi pulang berhasil!']);
    }
}
