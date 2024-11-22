<?php
// Database configuration
define('DB_HOST', '{{DB_HOST}}');
define('DB_USER', '{{DB_USER}}');
define('DB_PASS', '{{DB_PASS}}');
define('DB_NAME', '{{DB_NAME}}');

// Security configurations
define('CSRF_TOKEN_SECRET', '{{CSRF_TOKEN_SECRET}}');
define('PASSWORD_PEPPER', '{{PASSWORD_PEPPER}}');
define('JWT_SECRET', '{{JWT_SECRET}}');

// Rate limiting
define('RATE_LIMIT_REQUESTS', 100);  // requests per window
define('RATE_LIMIT_WINDOW', 3600);   // window in seconds (1 hour)

// Email configuration for password reset
define('SMTP_HOST', '{{SMTP_HOST}}');
define('SMTP_USER', '{{SMTP_USER}}');
define('SMTP_PASS', '{{SMTP_PASS}}');
define('SMTP_PORT', {{SMTP_PORT}});
define('SMTP_FROM', '{{SMTP_FROM}}'); 