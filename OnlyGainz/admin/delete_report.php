<?php
// Include the database connection
// Database connection settings
$host = 'localhost'; 
$dbname = 'only_gains'; 
$username = 'root'; 
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and execute the SQL to delete the record
    $sql = "DELETE FROM `loss_report` WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([':id' => $id]);

        // Redirect to the reports page after deletion
        header("Location: admin_loss_cause_report.php");
        exit;
    } catch (PDOException $e) {
        // If there's an error, show a message
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid report ID.";
    exit;
}
?>
