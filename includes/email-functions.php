<?php
// Email notification functions for the messaging system

/**
 * Send email notification to user when admin replies
 */
function sendAdminReplyNotification($userEmail, $userName, $messageId, $messageSubject, $adminReply) {
    $to = $userEmail;
    $subject = "New Reply to Your Message - EventBooking Cameroon";
    
    // Create HTML email content
    $htmlContent = createAdminReplyEmailHTML($userName, $messageId, $messageSubject, $adminReply);
    
    // Create plain text version
    $textContent = createAdminReplyEmailText($userName, $messageId, $messageSubject, $adminReply);
    
    // Send email
    return sendHTMLEmail($to, $subject, $htmlContent, $textContent);
}

/**
 * Send email notification when user sends a new message
 */
function sendNewMessageNotification($messageId, $userName, $userEmail, $subject, $message, $inquiryType) {
    $adminEmail = "nkumbelarry@gmail.com"; // Admin email
    $emailSubject = "New Message Received - EventBooking Cameroon";
    
    // Create HTML email content for admin
    $htmlContent = createNewMessageEmailHTML($messageId, $userName, $userEmail, $subject, $message, $inquiryType);
    
    // Create plain text version
    $textContent = createNewMessageEmailText($messageId, $userName, $userEmail, $subject, $message, $inquiryType);
    
    // Send email to admin
    return sendHTMLEmail($adminEmail, $emailSubject, $htmlContent, $textContent);
}

/**
 * Create HTML email template for admin reply notification
 */
function createAdminReplyEmailHTML($userName, $messageId, $messageSubject, $adminReply) {
    $siteUrl = "http://localhost:8080"; // Update this to your actual domain
    $loginUrl = $siteUrl . "/login.php";
    $messagesUrl = $siteUrl . "/user/messages.php";
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>New Reply from EventBooking Cameroon</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
            .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 30px; text-align: center; }
            .header h1 { margin: 0; font-size: 24px; }
            .content { padding: 30px; }
            .message-info { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #667eea; }
            .reply-box { background: #e7f3ff; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #007bff; }
            .button { display: inline-block; background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; margin: 10px 5px; }
            .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 14px; }
            .logo { font-size: 28px; font-weight: bold; margin-bottom: 10px; }
            @media (max-width: 600px) { .container { margin: 10px; } .content { padding: 20px; } }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <div class='logo'>🎪 EventBooking Cameroon</div>
                <h1>New Reply to Your Message</h1>
            </div>
            
            <div class='content'>
                <p>Hello <strong>" . htmlspecialchars($userName) . "</strong>,</p>
                
                <p>Great news! Our support team has replied to your message. Here are the details:</p>
                
                <div class='message-info'>
                    <h3>📧 Your Original Message</h3>
                    <p><strong>Message ID:</strong> #" . htmlspecialchars($messageId) . "</p>
                    <p><strong>Subject:</strong> " . htmlspecialchars($messageSubject) . "</p>
                </div>
                
                <div class='reply-box'>
                    <h3>💬 Our Reply</h3>
                    <p>" . nl2br(htmlspecialchars($adminReply)) . "</p>
                </div>
                
                <p>You can view the complete conversation and reply if needed by logging into your account:</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='" . $messagesUrl . "' class='button'>📱 View Messages</a>
                    <a href='" . $loginUrl . "' class='button'>🔐 Login to Account</a>
                </div>
                
                <div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                    <p><strong>💡 Quick Tip:</strong> You can reply directly through your message dashboard to continue the conversation with our support team.</p>
                </div>
                
                <p>If you have any questions or need further assistance, feel free to reply to this conversation or contact us directly.</p>
                
                <p>Best regards,<br>
                <strong>EventBooking Cameroon Support Team</strong></p>
            </div>
            
            <div class='footer'>
                <p><strong>EventBooking Cameroon</strong><br>
                Avenue Kennedy, Plateau District<br>
                Yaoundé, Cameroon<br>
                📞 +237 652 731 798 | 📧 nkumbelarry@gmail.com</p>
                
                <p style='margin-top: 20px; font-size: 12px; color: #999;'>
                    This email was sent because you have an active conversation with our support team. 
                    If you believe this was sent in error, please contact us.
                </p>
            </div>
        </div>
    </body>
    </html>";
}

/**
 * Create plain text email for admin reply notification
 */
function createAdminReplyEmailText($userName, $messageId, $messageSubject, $adminReply) {
    $siteUrl = "http://localhost:8080";
    $messagesUrl = $siteUrl . "/user/messages.php";
    
    return "
EventBooking Cameroon - New Reply to Your Message

Hello " . $userName . ",

Great news! Our support team has replied to your message.

Your Original Message:
- Message ID: #" . $messageId . "
- Subject: " . $messageSubject . "

Our Reply:
" . $adminReply . "

You can view the complete conversation and reply if needed by visiting:
" . $messagesUrl . "

If you have any questions or need further assistance, feel free to reply to this conversation.

Best regards,
EventBooking Cameroon Support Team

---
EventBooking Cameroon
Avenue Kennedy, Plateau District
Yaoundé, Cameroon
Phone: +237 652 731 798
Email: nkumbelarry@gmail.com
";
}

/**
 * Create HTML email template for new message notification (to admin)
 */
function createNewMessageEmailHTML($messageId, $userName, $userEmail, $subject, $message, $inquiryType) {
    $siteUrl = "http://localhost:8080";
    $adminUrl = $siteUrl . "/admin/messages.php";
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>New Message - EventBooking Cameroon</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
            .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(135deg, #dc3545, #6f42c1); color: white; padding: 30px; text-align: center; }
            .header h1 { margin: 0; font-size: 24px; }
            .content { padding: 30px; }
            .message-info { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #dc3545; }
            .message-content { background: #fff3cd; padding: 20px; border-radius: 8px; margin: 20px 0; }
            .button { display: inline-block; background: linear-gradient(135deg, #dc3545, #6f42c1); color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; margin: 10px 5px; }
            .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 14px; }
            .logo { font-size: 28px; font-weight: bold; margin-bottom: 10px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <div class='logo'>🛡️ Admin Alert</div>
                <h1>New Message Received</h1>
            </div>
            
            <div class='content'>
                <p>A new message has been received through the contact form:</p>
                
                <div class='message-info'>
                    <h3>📧 Message Details</h3>
                    <p><strong>Message ID:</strong> #" . htmlspecialchars($messageId) . "</p>
                    <p><strong>From:</strong> " . htmlspecialchars($userName) . "</p>
                    <p><strong>Email:</strong> " . htmlspecialchars($userEmail) . "</p>
                    <p><strong>Type:</strong> " . htmlspecialchars(ucfirst($inquiryType)) . "</p>
                    <p><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>
                </div>
                
                <div class='message-content'>
                    <h3>💬 Message Content</h3>
                    <p>" . nl2br(htmlspecialchars($message)) . "</p>
                </div>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='" . $adminUrl . "' class='button'>🛡️ View in Admin Panel</a>
                </div>
                
                <p>Please log into the admin panel to respond to this message.</p>
            </div>
            
            <div class='footer'>
                <p><strong>EventBooking Cameroon Admin System</strong></p>
            </div>
        </div>
    </body>
    </html>";
}

/**
 * Create plain text email for new message notification (to admin)
 */
function createNewMessageEmailText($messageId, $userName, $userEmail, $subject, $message, $inquiryType) {
    $siteUrl = "http://localhost:8080";
    $adminUrl = $siteUrl . "/admin/messages.php";
    
    return "
EventBooking Cameroon - New Message Received

A new message has been received through the contact form:

Message Details:
- Message ID: #" . $messageId . "
- From: " . $userName . "
- Email: " . $userEmail . "
- Type: " . ucfirst($inquiryType) . "
- Subject: " . $subject . "

Message Content:
" . $message . "

Please log into the admin panel to respond:
" . $adminUrl . "

---
EventBooking Cameroon Admin System
";
}

/**
 * Send HTML email using PHP mail function
 */
function sendHTMLEmail($to, $subject, $htmlContent, $textContent = '') {
    $headers = array();
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-Type: text/html; charset=UTF-8";
    $headers[] = "From: EventBooking Cameroon <nkumbelarry@gmail.com>";
    $headers[] = "Reply-To: nkumbelarry@gmail.com";
    $headers[] = "X-Mailer: PHP/" . phpversion();
    
    $headerString = implode("\r\n", $headers);
    
    // For development, we'll log the email instead of sending
    if (isDevelopmentMode()) {
        logEmailForDevelopment($to, $subject, $htmlContent);
        return true;
    }
    
    // In production, use actual mail function
    return mail($to, $subject, $htmlContent, $headerString);
}

/**
 * Check if we're in development mode
 */
function isDevelopmentMode() {
    return (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
            strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
            strpos($_SERVER['HTTP_HOST'], 'dev.') === 0);
}

/**
 * Log email for development purposes
 */
function logEmailForDevelopment($to, $subject, $content) {
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logFile = $logDir . '/email_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    
    $logEntry = "
=== EMAIL SENT ===
Date: $timestamp
To: $to
Subject: $subject
Content: 
$content

=====================================

";
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}
?>
