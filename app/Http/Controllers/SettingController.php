<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $password = Setting::where('key', 'public_asset_password')->value('value') ?? 'Mantup135';
        $telegramChats = Setting::where('key', 'authorized_telegram_chats')->value('value') ?? '';
        return view('settings.index', compact('password', 'telegramChats'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'public_asset_password' => 'required|string|min:4',
            'authorized_telegram_chats' => 'nullable|string'
        ]);

        Setting::updateOrCreate(
            ['key' => 'public_asset_password'],
            ['value' => $request->public_asset_password]
        );

        Setting::updateOrCreate(
            ['key' => 'authorized_telegram_chats'],
            ['value' => $request->authorized_telegram_chats]
        );

        return back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
