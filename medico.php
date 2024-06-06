<?php session_start();
if ($_SESSION['user_type'] != 'M') {
    header("Location: login.php");
    exit();
}

$connect = mysqli_connect("localhost", "root", "", "sim");
$query = "SELECT users.ID, NAME, USERNAME FROM patients JOIN users ON users.ID = patients.ID";
$patients = mysqli_query($connect,$query);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['start_treatment'])) {
    $patient_id = $_POST['patient_id'];
    $stmt = $connect->prepare("UPDATE patients SET treatment_start = NOW() WHERE id = ?");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'header.html'; ?>
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
                    $all_patients = $connect->query("SELECT p.id, u.name FROM patients p JOIN users u ON p.user_id = u.id");
                    while($p = $all_patients->fetch_assoc()):
                    ?>
                    <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" name="start_treatment">Iniciar Tratamento</button>
        </form>
    </div>
</div>
<?php include 'footer.html'; ?>
</html>