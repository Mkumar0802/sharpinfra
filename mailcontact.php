<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Only process POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form inputs, set to empty string if not provided
    $name = htmlspecialchars($_POST['contact-name'] ?? '', ENT_QUOTES);
    $email = filter_var($_POST['contact-email'] ?? '', FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars($_POST['contact-phone'] ?? '', ENT_QUOTES);
    $subject = htmlspecialchars($_POST['contact-subject'] ?? '', ENT_QUOTES);
    $message = nl2br(htmlspecialchars($_POST['contact-message'] ?? '', ENT_QUOTES));

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email address."]);
        exit;
    }

    // Set up email details
    $to = "mkumar0802@gmail.com";
    $from = 'noreply@sharpinfraprojects.com'; 
    $email_subject = !empty($subject) ? $subject : "New Contact Form Submission";
    
    // HTML email body
    $email_body = "
    <html>
    <head>
      <title>$email_subject</title>
    </head>
    <body>
      <h2>Contact Form Submission</h2>
      <p><strong>Name:</strong> $name</p>
      <p><strong>Email:</strong> $email</p>
      <p><strong>Phone:</strong> $phone</p>
      <p><strong>Subject:</strong> $subject</p>
      <p><strong>Message:</strong></p>
      <p style='color:red;'>$message</p>
    </body>
    </html>
    ";

    // Set headers for HTML email
    $headers = "From: noreply@sharpinfraprojects.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";

    // Attempt to send the email
    if (mail($to, $email_subject, $email_body, $headers)) {
        echo json_encode(["status" => "success", "message" => "Email sent successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Email failed to send."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method Not Allowed - Only POST requests accepted"]);
}
?>
