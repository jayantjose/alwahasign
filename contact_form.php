<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $project = isset($_POST['project']) ? trim($_POST['project']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $captcha = isset($_POST['captcha']) ? trim($_POST['captcha']) : '';

    // Validate required fields
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(["status" => "error", "message" => "Required fields are missing"]);
        exit;
    }

    // CAPTCHA validation (should match the answer from the frontend)
    if (!is_numeric($captcha)) {
        echo json_encode(["status" => "error", "message" => "Invalid CAPTCHA"]);
        exit;
    }
    
    // Email settings
    $to = "info@alwahasign.com";
    $subject_email = "New Contact Form Submission: " . $subject;
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // Email body
    $email_body = "
        <html>
        <head><title>Contact Form Submission</title></head>
        <body>
            <h2>New Contact Form Submission</h2>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Phone:</strong> $phone</p>
            <p><strong>Project:</strong> $project</p>
            <p><strong>Subject:</strong> $subject</p>
            <p><strong>Message:</strong> $message</p>
        </body>
        </html>
    ";

    // Save to CSV
    $csvFile = __DIR__ . "/contacts/contact.csv";
    $csvDir = dirname($csvFile);

    // Ensure directory exists
    if (!is_dir($csvDir)) {
        mkdir($csvDir, 0755, true);
    }

    // Open CSV file for appending
    $file = fopen($csvFile, "a");

    // Write data to CSV
    fputcsv($file, [$name, $email, $phone, $project, $subject, $message, date("Y-m-d H:i:s")]);

    // Close the file
    fclose($file);

    // Send Email
    if (mail($to, $subject_email, $email_body, $headers)) {
        echo json_encode(["status" => "success", "message" => "Message sent successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to send email"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>
