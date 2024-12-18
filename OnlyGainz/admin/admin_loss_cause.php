<?php
// Database connection settings
$host = 'localhost'; // XAMPP default host
$dbname = 'only_gains'; // Database name
$username = 'root'; // XAMPP default username
$password = ''; // XAMPP default password (empty by default)

// Establishing connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful!<br>";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handling form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $week = $_POST['week'];
    $physical_damage_kg = $_POST['physical_damage_kg'];
    $spoilage_kg = $_POST['spoilage_kg'];
    $pest_infestation_kg = $_POST['pest_infestation_kg'];
    $over_ripening_kg = $_POST['over_ripening_kg'];
    $solution = $_POST['solution'];

    // SQL query to insert data into the loss_report table
    $sql = "INSERT INTO `loss_report` (`week`, `physical_damage_kg`, `spoilage_kg`, `pest_infestation_kg`, 
            `over_ripening_kg`, `solution`, `created_at`) 
            VALUES (:week, :physical_damage_kg, :spoilage_kg, :pest_infestation_kg, :over_ripening_kg, :solution, NOW())";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':week' => $week,
            ':physical_damage_kg' => $physical_damage_kg,
            ':spoilage_kg' => $spoilage_kg,
            ':pest_infestation_kg' => $pest_infestation_kg,
            ':over_ripening_kg' => $over_ripening_kg,
            ':solution' => $solution
        ]);

        echo "<script>alert('Account created successfully!');</script>";
        header("Location: admin_loss_cause_report.php");
        exit;
    }catch (PDOException $e) {
       
        echo "Error: " . $e->getMessage();
    
       
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
    
       
        header("Location: admin_loss_cause_report.php");
        exit;
    }
}
?>