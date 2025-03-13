<?php

namespace App\Http\Controllers;

use App\Models\DBBackup;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DBBackupController extends Controller
{
    public function index()
    {
        $dbbackup = DBBackup::first();
        return Inertia::render('Setup/DBBackup', [
            'dbbackup' => $dbbackup
        ]);
    }
    // Update settings
    public function update(Request $request)
    {
        $request->validate([
            'backup_email' => 'required|email',
            'backup_frequency' => 'required|string',
        ]);

        $dbbackup = DBBackup::firstOrNew();
        $dbbackup->backup_email = $request->backup_email;
        $dbbackup->backup_frequency = $request->backup_frequency;
        $dbbackup->save();

        return redirect()->route('dbbackup.index')->with('success', 'Settings updated successfully.');
    }

    // Trigger database backup
    public function backup()
    {
        $dbbackup = DBBackup::first();

        // Perform database backup
        $backupPath = storage_path('app/backups/backup-' . date('Y-m-d_H:i:s') . '.sql');

        MySql::create()
            ->setDbName(env('DB_DATABASE'))
            ->setUserName(env('DB_USERNAME'))
            ->setPassword(env('DB_PASSWORD'))
            ->dumpToFile($backupPath);

        // Send the backup via email
        Mail::raw('Database backup attached.', function ($message) use ($dbbackup, $backupPath) {
            $message->to($dbbackup->backup_email)
                ->subject('Database Backup')
                ->attach($backupPath);
        });

        return redirect()->route('dbbackup.index')->with('success', 'Backup sent successfully!');
    }
}