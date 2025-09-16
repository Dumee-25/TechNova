<?php

/*
    Last Updated: Sep 16, 2025
    Updated by: Sachith Dilshan Kaluwitharana   
*/

session_start();
include 'includes/config.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    die("No message selected.");
}

$messageId = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
$stmt->execute([$messageId]);
$message = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$message) {
    die("Message not found.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'])) {
    $replyMessage = trim($_POST['reply']);

    if (empty($replyMessage)) {
        $error = "Reply cannot be empty.";
    } else {
        $to = $message['email'];
        $subject = "Reply to your message from Tech Nova!";
        $headers = "From: no-reply@technews.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (mail($to, $subject, $replyMessage, $headers)) {
            
            $smsSent = false;
            if (!empty($message['phone'])) {
                $smsSent = sendSMSNotification($message['phone']);
            }
            
           
            $stmt = $pdo->prepare("UPDATE contact_messages SET replied = 1, reply_message = ?, replied_at = NOW() WHERE id = ?");
            $stmt->execute([$replyMessage, $messageId]);

            if ($smsSent) {
                $success = "Reply sent successfully! Email and SMS notification sent.";
            } else if (!empty($message['phone'])) {
                $success = "Reply sent successfully via email, but SMS notification failed.";
            } else {
                $success = "Reply sent successfully via email!";
            }
        } else {
            $error = "Failed to send email. Make sure your server supports PHP mail().";
        }
    }
}


function sendSMSNotification($phoneNumber) {
   
    $cleanedPhone = preg_replace('/[^0-9]/', '', $phoneNumber);
    
 
    if (strlen($cleanedPhone) !== 10 || !preg_match('/^0(7[0-9]|1[0-9])/', $cleanedPhone)) {
        error_log("Invalid phone number format: " . $cleanedPhone);
        return false;
    }
    
  
    $apiKey = '';
    $senderId = '';
    $url = '';
    

    $smsMessage = "You have received a reply from TechNova please check your email";
    
   
    $payload = [
        'recipient' => $cleanedPhone,
        'sender_id' => $senderId,
        'message' => $smsMessage
    ];
    
    
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n" .
                        "Accept: application/json\r\n" .
                        "Authorization: Bearer " . $apiKey . "\r\n",
            'content' => json_encode($payload),
            'ignore_errors' => true,
            'timeout' => 10 
        ]
    ];
    
    
    $context = stream_context_create($options);
    
    try {
        $response = @file_get_contents($url, false, $context);
        
        
        if ($response === FALSE) {
            error_log("SMS API request failed for phone: " . $cleanedPhone);
            return false;
        }
        
     
        $responseData = json_decode($response, true);
        
      
        if (isset($responseData['status']) && $responseData['status'] === 'success') {
            return true;
        } else {
            error_log("SMS sending failed. Response: " . $response);
            return false;
        }
    } catch (Exception $e) {
        error_log("SMS sending exception: " . $e->getMessage());
        return false;
    }
}
?>

<style>
.content-grid {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 8px;
}
textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid var(--border);
    border-radius: 4px;
    font-size: 16px;
    font-family: 'Roboto', sans-serif;
}
button {
    background: var(--primary);
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}
button:hover {
    background: var(--secondary);
}
a {
    margin-left: 10px;
    text-decoration: none;
    color: var(--accent);
}
a:hover {
    text-decoration: underline;
}
.alert {
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 15px;
}
.alert-success { background-color: #4caf50; color: #fff; }
.alert-error { background-color: #f44336; color: #fff; }
.sms-info {
    background-color: #e3f2fd;
    padding: 10px;
    border-radius: 4px;
    margin: 10px 0;
    border-left: 4px solid #2196f3;
}
.message-details {
    background-color: #f5f5f5;
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}
.message-details p {
    margin: 5px 0;
}
</style>

<div class="content-grid">
    <h2>Reply to Message</h2>
    
    <div class="message-details">
        <p><strong>From:</strong> <?php echo htmlspecialchars($message['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($message['email']); ?></p>
        <p><strong>Phone:</strong> <?php 
            if (!empty($message['phone'])) {
                $cleanedPhone = preg_replace('/[^0-9]/', '', $message['phone']);
                if (strlen($cleanedPhone) === 10) {
                    echo $cleanedPhone;
                } else {
                    echo htmlspecialchars($message['phone']);
                }
            } else {
                echo 'No phone number provided';
            }
        ?></p>
        <p><strong>Received:</strong> <?php echo date('M j, Y g:i A', strtotime($message['created_at'])); ?></p>
    </div>
    
    <div class="sms-info">
        <p><strong>SMS Notification:</strong> An SMS will be sent to notify about your reply if a valid phone number is provided.</p>
    </div>
    
    <p><strong>Original Message:</strong></p>
    <div style="background: #f9f9f9; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
        <?php echo nl2br(htmlspecialchars($message['message'])); ?>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="reply"><strong>Your Reply:</strong></label>
        <textarea name="reply" id="reply" rows="6" placeholder="Type your reply here..."><?php echo isset($_POST['reply']) ? htmlspecialchars($_POST['reply']) : ''; ?></textarea><br><br>
        <button type="submit">Send Reply (Email + SMS)</button>
        <a href="message-view.php">Cancel</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>