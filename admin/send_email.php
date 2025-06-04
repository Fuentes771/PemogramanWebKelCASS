<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $to = $_POST['email'];
    $subject = "Your Subject Here";
    $message = "Your message here.";
    $headers = "From: beanscene@mail.com";

    if (mail($to, $subject, $message, $headers)) {
        echo "Email sent successfully to $to.";
    } else {
        echo "Failed to send email.";
    }
}
?>
