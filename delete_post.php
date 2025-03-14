<?php
session_start();
include 'config.php';

if (!isset($_GET['id'])) {
    echo "Invalid request.";
    exit();
}

$post_id = intval($_GET['id']); // Sanitize input

// Retrieve the post to get the image filename
$query = "SELECT image FROM posts WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $post_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$post = mysqli_fetch_assoc($result);

if ($post) {
    // Delete the image file if it exists
    if (!empty($post['image'])) {
        $image_path = __DIR__ . "/uploads/" . $post['image'];
        if (file_exists($image_path)) {
            unlink($image_path); // Remove the file
        }
    }

    // Delete the post from the database
    $delete_query = "DELETE FROM posts WHERE id = ?";
    $delete_stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($delete_stmt, "i", $post_id);
    mysqli_stmt_execute($delete_stmt);
}

// Delete from contacts (if applicable)
$sql = "DELETE FROM contacts WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();

// Close connections
$stmt->close();
$conn->close();

// Redirect
header("Location: dashboard.php");
exit();
?>
