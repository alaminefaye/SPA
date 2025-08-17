<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class QrCodeController extends Controller
{
    /**
     * Display the QR code generation form
     */
    public function index()
    {
        return view('admin.qrcode.index');
    }

    /**
     * Generate a QR code based on form input
     */
    public function generate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'link' => 'required|url',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $name = $request->input('name');
        $link = $request->input('link');
        $filename = Str::slug($name) . '-' . time() . '.png';
        $path = 'qrcodes/' . $filename;

        // Generate QR code
        $qrCode = QrCode::format('png')
                ->size(500)
                ->errorCorrection('H')
                ->margin(1)
                ->generate($link);

        // Store the QR code
        Storage::disk('public')->put($path, $qrCode);
        $qrCodePath = Storage::disk('public')->path($path);

        // If logo is provided, merge it with the QR code
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoPath = $logo->getPathname();
            
            // Open QR code image
            $qrImage = Image::make($qrCodePath);
            
            // Open logo and resize it
            $logoImage = Image::make($logoPath);
            $logoImage->resize(100, 100);
            
            // Insert logo in center of QR code
            $qrImage->insert($logoImage, 'center');
            $qrImage->save($qrCodePath);
        }

        return view('admin.qrcode.show', [
            'name' => $name,
            'link' => $link,
            'qrCodePath' => $path,
        ]);
    }

    /**
     * Download the generated QR code
     */
    public function download($filename)
    {
        $path = 'qrcodes/' . $filename;
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->download($path);
        }
        
        return back()->with('error', 'QR code non trouvé');
    }
}
