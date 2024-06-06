<?php
session_start();
if ($_SESSION['user_type'] != 'P') {
    header("Location: login.php");
    exit();
}

$connect = mysqli_connect("localhost", "root", "", "sim");
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

$patient_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ph = $_POST['ph'];
    $temperature = $_POST['temperature'];
    $conductivity = $_POST['conductivity'];
    $visual = $_POST['visual'];
    $odor = $_POST['odor'];

    $stmt = $connect->prepare("INSERT INTO measurements (patient_id, ph, temperature, conductivity, visual, odor) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idddds", $patient_id, $ph, $temperature, $conductivity, $visual, $odor);
    $stmt->execute();
    $stmt->close();
}

// Fetch existing measurements for the patient
$measurements = [];
$stmt = $connect->prepare("SELECT * FROM measurements WHERE patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $measurements[] = $row;
}
$stmt->close();
$connect->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 80%;
            margin: 0 auto;
        }
        h2 {
            text-align: center;
        }
        form {
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
        form input, form select {
            margin-bottom: 10px;
            padding: 10px;
            width: calc(100% - 22px);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Bem-vindo ao Painel do Paciente</h2>

    <form method="post" action="">
        <h3>Insira suas medições</h3>
        <label for="ph">pH:</label>
        <input type="number" id="ph" name="ph" step="0.01" required>

        <label for="temperature">Temperatura (°C):</label>
        <input type="number" id="temperature" name="temperature" step="0.1" required>

        <label for="conductivity">Condutividade:</label>
        <input type="number" id="conductivity" name="conductivity" step="0.01" required>

        <label for="visual">Visual:</label>
        <input type="text" id="visual" name="visual" required>

        <label for="odor">Odor:</label>
        <input type="text" id="odor" name="odor" required>

        <button type="submit">Enviar Medições</button>
    </form>

    <h3>Suas medições anteriores</h3>
    <table>
        <thead>
        <tr>
            <th>Data</th>
            <th>pH</th>
            <th>Temperatura (°C)</th>
            <th>Condutividade</th>
            <th>Visual</th>
            <th>Odor</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($measurements)): ?>
            <?php foreach ($measurements as $measurement): ?>
                <tr>
                    <td><?php echo $measurement['created_at']; ?></td>
                    <td><?php echo $measurement['ph']; ?></td>
                    <td><?php echo $measurement['temperature']; ?></td>
                    <td><?php echo $measurement['conductivity']; ?></td>
                    <td><?php echo $measurement['visual']; ?></td>
                    <td><?php echo $measurement['odor']; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">Nenhuma medição registrada.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
