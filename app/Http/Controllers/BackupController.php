<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    public function showBackupPage()
    {
        return view('backup.page');
    }

    public function store()
    {
        // Run backup
        Artisan::call('backup:run');

        // Get the latest backup file
        $backupDir = storage_path('app/Laravel');
        $files = File::allFiles($backupDir);

        if (empty($files)) {
            return back()->with('error', 'No backup found.');
        }

        // Get most recent file
        $latestBackup = collect($files)->sortByDesc->getMTime()->first();

        return response()->download($latestBackup->getRealPath());
    }
}

