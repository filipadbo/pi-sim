<?php session_start();
if ($_SESSION['user_type'] != 'M') {
    header("Location: login.php");
    exit();
}

$connect = new mysqli("localhost", "root", "", "pi-sim");

$patients = $connect->query("SELECT p.ID, u.NAME, u.USERNAME FROM patients p JOIN users u ON p.ID = u.ID");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['start_treatment'])) {
    $patient_id = $_POST['patient_id'];
    $stmt = $connect->prepare("UPDATE patients SET START_DATE = NOW() WHERE ID = ?");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<h2 class="text-center">Gestão de Pacientes</h2>
<h3 class="text-center">Pacientes Ativos</h3>
<table class="table table-striped">
    <thead>
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Username</th>
    </tr>
    </thead>
    <tbody>
    <?php while($patient = $patients->fetch_assoc()): ?>
        <tr>
            <td><?= $patient['id'] ?></td>
            <td><?= $patient['name'] ?></td>
            <td><?= $patient['username'] ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<div class="card my-4">
    <div class="card-header">
        <h4>Iniciar Tratamento</h4>
    </div>
    <div class="card-body">
        <form method="post" action="">
            <div class="mb-3">
                <label for="patient_id" class="form-label">Selecionar Paciente:</label>
                <select class="form-select" id="patient_id" name="patient_id">
                    <?php
                    $all_patients = $connect->query("SELECT p.ID, u.NAME FROM patients p JOIN users u ON p.ID = u.ID");
                    while($p = $all_patients->fetch_assoc()):
                        ?>
                        <option value="<?= $p['ID'] ?>"><?= $p['NAME'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" name="start_treatment">Iniciar Tratamento</button>
        </form>
    </div>
</div>

</html>
