<?php
// Database connection settings
$host = 'localhost'; // XAMPP default host
$dbname = 'only_gains'; // Database name
$username = 'root'; // XAMPP default username
$password = ''; // XAMPP default password (empty by default)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch the report for editing
if (isset($_GET['id'])) {
    $reportId = $_GET['id'];
    
    // SQL query to fetch the report data
    $sql = "SELECT * FROM `loss_report` WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $reportId]);
    $report = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$report) {
        die("Report not found.");
    }
}

// Handling form submission for Update (Edit Report)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $week = $_POST['week'];
    $physical_damage_kg = $_POST['physical_damage_kg'];
    $spoilage_kg = $_POST['spoilage_kg'];
    $pest_infestation_kg = $_POST['pest_infestation_kg'];
    $over_ripening_kg = $_POST['over_ripening_kg'];
    $solution = $_POST['solution'];

    // SQL query to update data in the loss_report table
    $sql = "UPDATE `loss_report` SET `week` = :week, `physical_damage_kg` = :physical_damage_kg, 
            `spoilage_kg` = :spoilage_kg, `pest_infestation_kg` = :pest_infestation_kg, 
            `over_ripening_kg` = :over_ripening_kg, `solution` = :solution 
            WHERE `id` = :id";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':week' => $week,
            ':physical_damage_kg' => $physical_damage_kg,
            ':spoilage_kg' => $spoilage_kg,
            ':pest_infestation_kg' => $pest_infestation_kg,
            ':over_ripening_kg' => $over_ripening_kg,
            ':solution' => $solution,
            ':id' => $reportId
        ]);

        // Redirect to the report list page after successful update
        echo "<script>alert('Report updated successfully!');</script>";
        header("Location: admin_loss_cause_report.php");
        exit;
    } catch (PDOException $e) {
        // Handle errors
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
        header("Location: admin_loss_cause_report.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Loss Report - Admin</title>
    <link rel="stylesheet" href="../theme.css">
    <link rel="stylesheet" href="./admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Admin Panel</h2>
            <nav>
                <ul>
                    <li><a href="admin.html">Dashboard</a></li>
                    <li class="dropdown">
                        <a class="dropdown-btn">User Roles</a>
                        <ul class="dropdown-content">
                            <li><a href="../farm manager/farm_manager.html">Farm Manager</a></li>
                            <li><a href="../retailer/all_orders.html">Retailer</a></li>
                            <li><a href="../wholesaler/wholesaler.html">Wholesaler</a></li>
                            <li><a href="../qa officer/inspections.html">QA Officer</a></li>
                        </ul>
                    </li>
                    <li><a href="./loss_tracking_admin.html">Loss & Improvement Tracking</a></li>
                    <li><a href="./admin_loss_cause_report.html">Reports</a></li>
                    <li><a href="../login.html">Log Out</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="content">
            <header class="head">
                <h1>Edit Loss Report</h1>
            </header>

            <!-- Loss Report Form -->
            <section class="report-section">
            <style>
                    /* CSS for Report Section */
                    .report-section {
                        background-color: #f9f9f9;
                        padding: 20px;
                        border-radius: 8px;
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                        margin: 20px 0;
                        font-family: Arial, sans-serif;
                    }
                
                    .report-section h1 {
                        font-size: 1.8rem;
                        margin-bottom: 15px;
                        color: #333;
                        text-align: center;
                    }
                
                    .report-section form {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 15px;
                        justify-content: space-between;
                    }
                
                    .report-section label {
                        font-weight: bold;
                        margin-bottom: 5px;
                        color: #555;
                        width: 30%; /* Adjust the width of the labels */
                    }
                
                    .report-section input, 
                    .report-section textarea {
                        padding: 8px;
                        border: 1px solid #ccc;
                        border-radius: 4px;
                        font-size: 0.9rem;
                        width: 65%; /* Adjust the width of the input boxes */
                        box-sizing: border-box;
                    }
                
                    .report-section textarea {
                        resize: vertical;
                        min-height: 80px;
                        width: 65%; /* Ensure the textarea fits in the layout */
                    }
                
                    .report-section button {
                        background-color: #4CAF50;
                        color: white;
                        padding: 10px 15px;
                        border: none;
                        border-radius: 4px;
                        font-size: 1rem;
                        cursor: pointer;
                        transition: background-color 0.3s;
                        width: 100%; /* Button takes up full width at the bottom */
                    }
                
                    .report-section button:hover {
                        background-color: #45a049;
                    }
                </style>
                <form method="POST" action="edit_report.php?id=<?= $report['id'] ?>">
                    <label for="week">Week:</label>
                    <input type="text" id="week" name="week" value="<?= htmlspecialchars($report['week']) ?>" required><br><br>

                    <label for="physical_damage_kg">Physical Damage (kg):</label>
                    <input type="number" step="0.01" id="physical_damage_kg" name="physical_damage_kg" value="<?= htmlspecialchars($report['physical_damage_kg']) ?>" required><br><br>

                    <label for="spoilage_kg">Spoilage (kg):</label>
                    <input type="number" step="0.01" id="spoilage_kg" name="spoilage_kg" value="<?= htmlspecialchars($report['spoilage_kg']) ?>" required><br><br>

                    <label for="pest_infestation_kg">Pest Infestation (kg):</label>
                    <input type="number" step="0.01" id="pest_infestation_kg" name="pest_infestation_kg" value="<?= htmlspecialchars($report['pest_infestation_kg']) ?>" required><br><br>

                    <label for="over_ripening_kg">Over Ripening (kg):</label>
                    <input type="number" step="0.01" id="over_ripening_kg" name="over_ripening_kg" value="<?= htmlspecialchars($report['over_ripening_kg']) ?>" required><br><br>

                    <label for="solution">Solution:</label>
                    <textarea id="solution" name="solution" required><?= htmlspecialchars($report['solution']) ?></textarea><br><br>

                    <button type="submit">Update Report</button>
                </form>
            </section>

        </main>
    </div>

    <script>
        // JavaScript for form validation (same as in admin_loss_cause.php)
        document.addEventListener("DOMContentLoaded", () => {
            const form = document.querySelector(".report-section form");
            const inputs = form.querySelectorAll("input[type='number']");
            const weekInput = document.getElementById("week");

            // Function to ensure no negative values in numeric fields
            inputs.forEach(input => {
                input.addEventListener("input", () => {
                    if (input.value < 0) {
                        input.setCustomValidity("Value cannot be negative.");
                        input.reportValidity();
                    } else {
                        input.setCustomValidity("");
                    }
                });
            });

            // Week validation: must match "Week X" or "X" format
            weekInput.addEventListener("input", () => {
                const pattern = /^Week\s?\d+$/i;
                if (!pattern.test(weekInput.value)) {
                    weekInput.setCustomValidity("Please enter a valid week, e.g., 'Week 1' or '1'.");
                    weekInput.reportValidity();
                } else {
                    weekInput.setCustomValidity("");
                }
            });

            // Prevent form submission if invalid
            form.addEventListener("submit", (event) => {
                const invalidInputs = Array.from(inputs).filter(input => input.value < 0);
                if (invalidInputs.length > 0) {
                    alert("Please correct invalid input values before submitting.");
                    event.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
