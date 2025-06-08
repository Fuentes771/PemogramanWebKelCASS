<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT, 
        ['options' => ['min_range' => 1, 'max_range' => 5]]);
    $review = filter_input(INPUT_POST, 'review', FILTER_SANITIZE_STRING);

    if ($name && $rating !== false && $review) {
        try {
            $stmt = $pdo->prepare("INSERT INTO customer_reviews 
                                   (customer_name, review_text, rating) 
                                   VALUES (?, ?, ?)");
            $stmt->execute([$name, $review, $rating]);
            
            // Redirect back with success message
            header('Location: ../aboutus.php?review=success');
            exit();
        } catch(PDOException $e) {
            // Log error and redirect with error message
            error_log("Error saving review: " . $e->getMessage());
            header('Location: ../aboutus.php?review=error');
            exit();
        }
    } else {
        // Invalid input
        header('Location: ../aboutus.php?review=invalid');
        exit();
    }
} else {
    // Not a POST request
    header('Location: ../aboutus.php');
    exit();
}
?>