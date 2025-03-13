<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
class SystemSettingController extends Controller
{

    public function checkRole()
    {
        $data = Role::join('users', 'users.role', 'roles.id')
            ->where('roles.system_setting', 1)
            ->where('users.id', Auth::id())
            ->first();
        if (!$data) {
            abort(403);
        }
    }
    public function index(Request $request)
    {
        $this->checkRole();
        $setting = SystemSetting::all();
        return Inertia::render('Setup/SystemSetting', ['setting' => $setting]);
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'application_name' => ['required', 'string', 'max:255'],
            'theme_color' => ['nullable', 'string', 'max:255'],
            'accent_color' => ['nullable', 'string', 'max:255'],
            'logo_small'       => ['nullable', 'image', 'mimes:png'],
            'logo_large'       => ['nullable', 'image', 'mimes:png'],
        ]);

        // Create a new setting instance
        $setting = new SystemSetting();
        $setting->application_name = $request->input('application_name');
        $setting->theme_color = $request->input('theme_color');
        $setting->accent_color = $request->input('accent_color');

        // If a small logo file was uploaded, store it and save the path
        if ($request->hasFile('logo_small')) {
            $path = $request->file('logo_small')->store('logos', 'public');
            // e.g. "logos/some_random_filename.png"
            $setting->logo_small = $path;

            $faviconPath = 'logos/favicon.png';
            $img = Image::read($request->file('logo_small'));
            // Make an Intervention Image instance from the uploaded file
        //    $img = Image::make($request->file('logo_small')->getRealPath());

            // Resize to 32x32 (keeping aspect ratio, adds white space if not square)
            // or you can force a crop if you want a perfect square
            $img->resize(32, 32, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            // Store it as a PNG (overwrites if already exists)
            $encoded = $img->toPng();
            Storage::disk('public')->put(
                'logos/favicon.png',
                $encoded
            );
            //Storage::disk('public')->put($faviconPath, (string) $img->encode('png'));
        }

        // If a large logo file was uploaded, store it and save the path
        if ($request->hasFile('logo_large')) {
            $path = $request->file('logo_large')->store('logos', 'public');
            $setting->logo_large = $path;
        }

        // Save the new SystemSetting record
        $setting->save();

        // Redirect back with a success message
        return redirect()->back()->with('message', 'Setting Created Successfully.');
    }
    public function update(Request $request)
    {
        // Validate input
        //    dd($request);
        $request->validate([
            'id' => 'required',
            'theme_color' => ['nullable', 'string', 'max:255'],
            'accent_color' => ['nullable', 'string', 'max:255'],
            'application_name' => 'required',
        ]);
        $setting = SystemSetting::find($request->id)->first();
        // If there's a new file for logo_small
        if ($request->hasFile('logo_small')) {
            // store the file in public disk: storage/app/public/logos
            $path = $request->file('logo_small')->store('logos', 'public');
            // e.g. 'logos/small_logo_123.png'

            // Save path in DB
            $setting->logo_small = $path; // store just 'logos/small_logo_123.png'
            $faviconPath = 'logos/favicon.png';
            $img = Image::read($request->file('logo_small'));
            // Make an Intervention Image instance from the uploaded file
        //    $img = Image::make($request->file('logo_small')->getRealPath());

            // Resize to 32x32 (keeping aspect ratio, adds white space if not square)
            // or you can force a crop if you want a perfect square
            $img->resize(32, 32, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            // Store it as a PNG (overwrites if already exists)
            $encoded = $img->toPng();
            Storage::disk('public')->put(
                'logos/favicon.png',
                $encoded
            );
        }

        // If there's a new file for logo_large
        if ($request->hasFile('logo_large')) {
            $path = $request->file('logo_large')->store('logos', 'public');
            $setting->logo_large = $path;
        }

        $setting->application_name = $request->application_name;
        $setting->theme_color = $request->theme_color;
        $setting->accent_color = $request->accent_color;
        $setting->update();

        return redirect()->back()->with('message', 'Setting updated!');
    }
}
