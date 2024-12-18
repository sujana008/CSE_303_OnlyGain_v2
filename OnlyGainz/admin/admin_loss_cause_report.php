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

// Handling form submission for Create (Add Report)
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

        // Redirect to the same page after successful insertion
        echo "<script>alert('Report added successfully!');</script>";
        header("Location: admin_loss_cause_report.php");
        exit;
    } catch (PDOException $e) {
        // Handle errors and redirect with an alert
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
        header("Location: admin_loss_cause_report.php");
        exit;
    }
}

// Fetch all reports for Read functionality
$sql = "SELECT * FROM `loss_report` ORDER BY created_at DESC";
$stmt = $pdo->query($sql);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loss Causes - Admin</title>
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
                <h1>Loss Causes Overview</h1>
            </header>

            <!-- Loss Causes Boxes (Moved to Top) -->
            <section class="stats-section">
                <div class="stat-box">
                    <h3>Physical Damage</h3>
                    <p id="physical-damage-loss">50 kg</p>
                </div>
                <div class="stat-box">
                    <h3>Spoilage</h3>
                    <p id="spoilage-loss">40 kg</p>
                </div>
                <div class="stat-box">
                    <h3>Pest Infestation</h3>
                    <p id="pest-infestation-loss">30 kg</p>
                </div>
                <div class="stat-box">
                    <h3>Over Ripening</h3>
                    <p id="over-ripening-loss">20 kg</p>
                </div>
            </section>

            <!-- Loss Causes Graph -->
            <section class="graphs-section">
                <div class="graph">
                    <canvas id="lossCausesGraph"></canvas>
                </div>
            </section>

            <!-- Search Bar -->
            <div class="search-container" style="text-align: right; margin: 1rem 0;">
                <input type="text" id="searchInput" placeholder="Search report..." style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <!-- Loss Report Table -->
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
                
                
                
                
                <h1>Loss Reports</h1>
                <form method="POST" action="admin_loss_cause.php">
                    <label for="week">Week:</label>
                    <input type="text" id="week" name="week" required><br><br>

                    <label for="physical_damage_kg">Physical Damage (kg):</label>
                    <input type="number" step="0.01" id="physical_damage_kg" name="physical_damage_kg" required><br><br>

                    <label for="spoilage_kg">Spoilage (kg):</label>
                    <input type="number" step="0.01" id="spoilage_kg" name="spoilage_kg" required><br><br>

                    <label for="pest_infestation_kg">Pest Infestation (kg):</label>
                    <input type="number" step="0.01" id="pest_infestation_kg" name="pest_infestation_kg" required><br><br>

                    <label for="over_ripening_kg">Over Ripening (kg):</label>
                    <input type="number" step="0.01" id="over_ripening_kg" name="over_ripening_kg" required><br><br>

                    <label for="solution">Solution:</label>
                    <textarea id="solution" name="solution" required></textarea><br><br>

                    <button type="submit">Submit</button>
                </form>

                <!-- Display Reports -->
                <h2>Existing Reports</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Week</th>
                            <th>Physical Damage (kg)</th>
                            <th>Spoilage (kg)</th>
                            <th>Pest Infestation (kg)</th>
                            <th>Over Ripening (kg)</th>
                            <th>Solution</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($reports)): ?>
                            <?php foreach ($reports as $report): ?>
                                <tr>
                                    <td><?= htmlspecialchars($report['id']) ?></td>
                                    <td><?= htmlspecialchars($report['week']) ?></td>
                                    <td><?= htmlspecialchars($report['physical_damage_kg']) ?></td>
                                    <td><?= htmlspecialchars($report['spoilage_kg']) ?></td>
                                    <td><?= htmlspecialchars($report['pest_infestation_kg']) ?></td>
                                    <td><?= htmlspecialchars($report['over_ripening_kg']) ?></td>
                                    <td><?= htmlspecialchars($report['solution']) ?></td>
                                    <td>
                                        <a href="edit_report.php?id=<?= $report['id'] ?>">Edit</a> |
                                        <a href="delete_report.php?id=<?= $report['id'] ?>" onclick="return confirm('Are you sure you want to delete this report?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">No reports found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <script>
                    // JavaScript for Report Section Validation
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
            </section>
            
        </main>
    </div>

    <script>
        // Loss Causes Data (Graph Data)
        const weeks = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'];
        const physicalDamageData = [50, 55, 52, 48, 50];
        const spoilageData = [40, 42, 43, 39, 41];
        const pestInfestationData = [30, 32, 31, 29, 30];
        const overRipeningData = [20, 18, 22, 21, 20];

        // Loss Causes Graph
        const lossCausesCtx = document.getElementById('lossCausesGraph').getContext('2d');
        const lossCausesGraph = new Chart(lossCausesCtx, {
            type: 'line',
            data: {
                labels: weeks,
                datasets: [
                    {
                        label: 'Physical Damage (kg)',
                        data: physicalDamageData,
                        borderColor: 'rgba(58, 90, 64, 1)',
                        fill: false,
                        tension: 0.4
                    },
                    {
                        label: 'Spoilage (kg)',
                        data: spoilageData,
                        borderColor: 'rgba(231, 76, 60, 1)',
                        fill: false,
                        tension: 0.4
                    },
                    {
                        label: 'Pest Infestation (kg)',
                        data: pestInfestationData,
                        borderColor: 'rgba(46, 204, 113, 1)',
                        fill: false,
                        tension: 0.4
                    },
                    {
                        label: 'Over Ripening (kg)',
                        data: overRipeningData,
                        borderColor: 'rgba(241, 196, 15, 1)',
                        fill: false,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });

        // Table Data
        const reportTableBody = document.getElementById('reportTableBody');
        const addReportBtn = document.getElementById('add-report-btn');
        const searchInput = document.getElementById('searchInput');

        const reportData = [];

        function renderTable() {
            reportTableBody.innerHTML = '';
            reportData.forEach((row, index) => {
                reportTableBody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td><input type="number" value="${row.physical}" disabled /></td>
                        <td><input type="number" value="${row.spoilage}" disabled /></td>
                        <td><input type="number" value="${row.pest}" disabled /></td>
                        <td><input type="number" value="${row.ripening}" disabled /></td>
                        <td><input type="text" value="${row.solution}" disabled /></td>
                        <td>
                            <button onclick="editRow(${index}, this)">Edit</button>
                            <button onclick="deleteRow(${index})">Delete</button>
                        </td>
                    </tr>
                `;
            });
        }

        function editRow(index, btn) {
            const row = btn.closest('tr');
            row.querySelectorAll('input').forEach(input => input.disabled = false);
            btn.textContent = 'Save';
            btn.onclick = () => saveRow(index, btn);
        }

        function saveRow(index, btn) {
            const row = btn.closest('tr');
            const inputs = row.querySelectorAll('input');
            reportData[index] = {
                physical: inputs[0].value,
                spoilage: inputs[1].value,
                pest: inputs[2].value,
                ripening: inputs[3].value,
                solution: inputs[4].value
            };
            renderTable();
        }

        function deleteRow(index) {
            reportData.splice(index, 1);
            renderTable();
        }

        addReportBtn.addEventListener('click', () => {
            reportData.push({ physical: 0, spoilage: 0, pest: 0, ripening: 0, solution: '' });
            renderTable();
        });

        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase();
            const rows = reportTableBody.querySelectorAll('tr');
            rows.forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
            });
        });

        renderTable();


        //newly added

        // JavaScript for Report Section Validation
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
