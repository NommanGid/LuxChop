<?php
require_once 'includes/config.php';
require_once 'includes/PHPMailer/PHPMailer.php';

use PHPMailer\PHPMailer\PHPMailer;

class PasswordReset {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function requestReset($email) {
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email address');
        }
        
        // Check if email exists
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Email not found');
        }
        
        // Generate token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Save token
        $stmt = $this->conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $token, $expires);
        $stmt->execute();
        
        // Send email
        $this->sendResetEmail($email, $token);
        
        return true;
    }
    
    private function sendResetEmail($email, $token) {
        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT;
            
            $mail->setFrom(SMTP_FROM, 'LuxChop Admin');
            $mail->addAddress($email);
            
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = $this->getResetEmailTemplate($token);
            
            $mail->send();
        } catch (Exception $e) {
            Logger::log("Email send failed: {$mail->ErrorInfo}", 'ERROR');
            throw new Exception('Failed to send reset email');
        }
    }
    
    public function resetPassword($token, $newPassword) {
        // Validate token
        $stmt = $this->conn->prepare("
            SELECT email FROM password_resets 
            WHERE token = ? AND expires_at > NOW() AND used = 0
        ");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Invalid or expired token');
        }
        
        $email = $result->fetch_assoc()['email'];
        
        // Update password
        $hashedPassword = Security::hashPassword($newPassword);
        $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to update password');
        }
        
        // Mark token as used
        $stmt = $this->conn->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        
        return true;
    }
    
    private function getResetEmailTemplate($token) {
        $resetUrl = "https://yourdomain.com/reset-password.php?token=" . $token;
        
        return "
            <h2>Password Reset Request</h2>
            <p>You requested a password reset for your LuxChop Admin account.</p>
            <p>Click the button below to reset your password:</p>
            <p>
                <a href='{$resetUrl}' 
                   style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>
                    Reset Password
                </a>
            </p>
            <p>This link will expire in 1 hour.</p>
            <p>If you didn't request this reset, please ignore this email.</p>
        ";
    }
}
