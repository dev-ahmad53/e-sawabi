<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Show admin dashboard configuration panel.
     */
    public function index()
    {
        $settings = [
            'photo_x' => Setting::getVal('photo_x', 275),
            'photo_y' => Setting::getVal('photo_y', 435),
            'photo_size' => Setting::getVal('photo_size', 270),
            'name_x' => Setting::getVal('name_x', 410),
            'name_y' => Setting::getVal('name_y', 750),
            'font_size' => Setting::getVal('font_size', 28),
            'font_color' => Setting::getVal('font_color', '#ffffff'),
            'font_family' => Setting::getVal('font_family', 'Arial-Bold.ttf'),
            'base_poster' => Setting::getVal('base_poster', 'base_poster.jpg'),
        ];

        return view('admin', compact('settings'));
    }

    /**
     * Save configuration options.
     */
    public function save(Request $request)
    {
        $request->validate([
            'base_poster' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'photo_x' => 'required|integer|min:0',
            'photo_y' => 'required|integer|min:0',
            'photo_size' => 'required|integer|min:10',
            'name_x' => 'required|integer|min:0',
            'name_y' => 'required|integer|min:0',
            'font_size' => 'required|integer|min:6',
            'font_color' => 'required|string|regex:/^#[a-fA-F0-9]{3,6}$/',
        ]);

        if ($request->hasFile('base_poster')) {
            $file = $request->file('base_poster');
            
            // Delete old poster if it isn't the default one
            $oldPoster = Setting::getVal('base_poster');
            if ($oldPoster && $oldPoster !== 'base_poster.jpg') {
                $oldPath = storage_path('app/public/' . $oldPoster);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $fileName = 'base_poster_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(storage_path('app/public'), $fileName);
            Setting::setVal('base_poster', $fileName);
        }

        Setting::setVal('photo_x', $request->input('photo_x'));
        Setting::setVal('photo_y', $request->input('photo_y'));
        Setting::setVal('photo_size', $request->input('photo_size'));
        Setting::setVal('name_x', $request->input('name_x'));
        Setting::setVal('name_y', $request->input('name_y'));
        Setting::setVal('font_size', $request->input('font_size'));
        Setting::setVal('font_color', $request->input('font_color'));

        return redirect()->route('admin.index')->with('success', 'Configuration updated successfully!');
    }
}
