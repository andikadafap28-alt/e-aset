<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $password = Setting::where('key', 'public_asset_password')->value('value') ?? 'Mantup135';
        return view('settings.index', compact('password'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'public_asset_password' => 'required|string|min:4'
        ]);

        Setting::updateOrCreate(
            ['key' => 'public_asset_password'],
            ['value' => $request->public_asset_password]
        );

        return back()->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
