<?php
// Define base path
define('BASE_PATH', dirname(__DIR__));

// Directory structure
$directories = [
    'uploads' => [
        'permissions' => 0755,
        'subdirs' => ['images', 'temp']
    ],
    'logs' => [
        'permissions' => 0755,
        'subdirs' => []
    ],
    'backups' => [
        'permissions' => 0755,
        'subdirs' => []
    ]
];

// Create directories and set permissions
foreach ($directories as $dir => $config) {
    $path = BASE_PATH . '/' . $dir;
    
    if (!file_exists($path)) {
        if (mkdir($path, $config['permissions'], true)) {
            echo "Created directory: $path\n";
        } else {
            echo "Failed to create directory: $path\n";
        }
    }
    
    chmod($path, $config['permissions']);
    
    // Create subdirectories
    foreach ($config['subdirs'] as $subdir) {
        $subpath = $path . '/' . $subdir;
        if (!file_exists($subpath)) {
            if (mkdir($subpath, $config['permissions'], true)) {
                echo "Created subdirectory: $subpath\n";
            } else {
                echo "Failed to create subdirectory: $subpath\n";
            }
        }
        chmod($subpath, $config['permissions']);
    }
}

// Create .htaccess files for security
$htaccess_content = "Options -Indexes\nDeny from all";
file_put_contents(BASE_PATH . '/logs/.htaccess', $htaccess_content);
file_put_contents(BASE_PATH . '/backups/.htaccess', $htaccess_content);

// Create uploads .htaccess to allow only image files
$uploads_htaccess = "Options -Indexes\n
<FilesMatch \"(?i)\.(php|php3|php4|php5|phtml|pl|py|jsp|asp|htm|html|shtml|sh|cgi)$\">\nDeny from all\n</FilesMatch>\n
<FilesMatch \"(?i)\.(jpg|jpeg|png|gif|webp)$\">\nAllow from all\n</FilesMatch>";
file_put_contents(BASE_PATH . '/uploads/.htaccess', $uploads_htaccess);

echo "Setup completed successfully!\n"; 