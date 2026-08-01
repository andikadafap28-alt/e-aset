<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = \App\Models\Room::withCount('assets')->latest()->get();
        return view('rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('rooms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Puskesmas,Pustu,Ponkesdes,Polindes,Ruangan',
            'penanggung_jawab' => 'nullable|string|max:255',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        \App\Models\Room::create($validated);
        return redirect()->route('rooms.index')->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function edit(\App\Models\Room $room)
    {
        return view('rooms.edit', compact('room'));
    }

    public function update(Request $request, \App\Models\Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Puskesmas,Pustu,Ponkesdes,Polindes,Ruangan',
            'penanggung_jawab' => 'nullable|string|max:255',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $room->update($validated);
        return redirect()->route('rooms.index')->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(\App\Models\Room $room)
    {
        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'Lokasi berhasil dihapus.');
    }
}
