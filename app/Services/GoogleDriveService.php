<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class GoogleDriveService
{
    /**
     * Upload a file to Google Drive and return metadata including file ID and link.
     */
    public function uploadProcurementFile(UploadedFile $file, string $kategori, string $filename)
    {
        $year = date('Y');
        
        // RAKSA_ARSIP_PUSKESMAS / PENGADAAN_2026 / KATEGORI
        // We assume the root folder ID is set in .env as GOOGLE_DRIVE_FOLDER_ID
        // In Flysystem Google Drive, root folder ID means '/' is that folder.
        // So the path is just 'PENGADAAN_' . $year . '/' . strtoupper($kategori) . '/' . $filename
        
        $folderPath = 'PENGADAAN_' . $year . '/' . strtoupper($kategori);
        $fullPath = $folderPath . '/' . $filename;
        
        // Upload the file
        $mimeType = $file->getClientMimeType();
        $path = Storage::disk('google')->putFileAs($folderPath, $file, $filename, ['mimetype' => $mimeType]);
        
        return [
            'file_name' => $filename,
            'drive_file_id' => $path,
            'path_gdrive' => $path,
        ];
    }
}
