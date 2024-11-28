<?php
require_once '../includes/config.php';

// Create daily backup
Backup::createBackup();

// Clean old backups (keep last 30 days)
$files = glob(BACKUP_DIR . '*.sql');
$now = time();

foreach ($files as $file) {
    if ($now - filemtime($file) > 30 * 24 * 60 * 60) {
        unlink($file);
        Logger::log("Deleted old backup: $file");
    }
} 