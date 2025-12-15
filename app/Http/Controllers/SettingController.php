<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\StorageHelper;

class SettingController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $setting = Setting::getSettings();

        return view('settings.index', compact('setting'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $setting = Setting::getSettings();

        $data = $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico|max:1024',
            'top_link' => 'nullable|string|max:255',
            'site_title' => 'required|string|max:255',
            'site_description' => 'required|string|max:500',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string',
            'whatsapp' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|url|max:500',
            'twitter_url' => 'nullable|url|max:500',
            'instagram_url' => 'nullable|url|max:500',
            'youtube_url' => 'nullable|url|max:500',
            'google_verification_code' => 'nullable|string|max:255',
            'analytics_code' => 'nullable|string',
            'google_map' => 'nullable|string',
        ]);

        // Logo yükleme
        if ($request->hasFile('logo')) {
            if ($setting->logo) {
                StorageHelper::deleteFromBoth($setting->logo);
            }
            $data['logo'] = StorageHelper::storeAndCopy($request->file('logo'), 'settings');
        }

        // Favicon yükleme
        if ($request->hasFile('favicon')) {
            if ($setting->favicon) {
                StorageHelper::deleteFromBoth($setting->favicon);
            }
            $data['favicon'] = StorageHelper::storeAndCopy($request->file('favicon'), 'settings');
        }

        $setting->update($data);

        return redirect()->route('settings.index')
            ->with('success', 'Ayarlar başarıyla güncellendi.');
    }
}
