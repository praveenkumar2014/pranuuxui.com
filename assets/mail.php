<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$to = 'praveenkumar.kanneganti@gmail.com';
$name = strip_tags(trim($_POST['name'] ?? ''));
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$subject_input = strip_tags(trim($_POST['subject'] ?? 'General Inquiry'));
$budget = strip_tags(trim($_POST['budget'] ?? 'Not specified'));
$message = strip_tags(trim($_POST['message'] ?? ''));

if ($name === '' || !$email || $message === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => 'Please fill in name, email, and message.']);
    exit;
}

$subject = 'Portfolio inquiry: ' . $subject_input;
$body = "
<div style='font-family:Inter,Arial,sans-serif;line-height:1.6;color:#222;'>
  <h2 style='color:#4770FF;margin-top:0;'>New message from pranuuxui.com</h2>
  <p><strong>Name:</strong> {$name}</p>
  <p><strong>Email:</strong> {$email}</p>
  <p><strong>Subject:</strong> {$subject_input}</p>
  <p><strong>Budget:</strong> {$budget}</p>
  <hr>
  <p><strong>Message</strong><br>" . nl2br($message) . "</p>
</div>";

$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type:text/html;charset=UTF-8\r\n";
$headers .= "From: Praveen Portfolio <noreply@pranuuxui.com>\r\n";
$headers .= "Reply-To: {$name} <{$email}>\r\n";

$sent = @mail($to, $subject, $body, $headers);

// On serverless hosts without mail(), log and still confirm to user.
if (!$sent) {
    $log_dir = dirname(__DIR__) . '/storage';
    if (!is_dir($log_dir)) {
        @mkdir($log_dir, 0755, true);
    }
    @file_put_contents(
        $log_dir . '/contact.log',
        date('c') . " | {$name} | {$email} | {$subject_input}\n",
        FILE_APPEND
    );
}

echo json_encode([
    'success' => true,
    'message' => "Thanks, {$name} — I'll reply within 24 hours.",
]);
