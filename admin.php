<?php session_start();
if ($_SESSION['user_type'] != 'ADMIN') {
    header("Location: login.php");
    exit();
}

$connect = mysqli_connect("localhost", "root", "", "pi-sim");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $user_type = $_POST['user_type'];

    $stmt = $connect->prepare("INSERT INTO users (NAME, USERNAME, PASSWORD, TYPE_USER) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $username, $password, $user_type);
    $stmt->execute();
    $stmt->close();
}

$users = $connect->query("SELECT ID, NAME, USERNAME, TYPE_USER FROM users");
?>

<!DOCTYPE html>
<html lang="en">

<h2 class="text-center">Gestão de Utilizadores</h2>
<div class="card my-4">
    <div class="card-header">
        <h4>Criar Novo Utilizador</h4>
    </div>
    <div class="card-body">
        <form method="post" action="">
            <div class="mb-3">
                <label for="name" class="form-label">Nome:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="user_type" class="form-label">Tipo de Utilizador:</label>
                <select class="form-select" id="user_type" name="user_type" required>
                    <option value="Adm">Administrador</option>
                    <option value="M">Médico</option>
                    <option value="P">Paciente</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Criar Utilizador</button>
        </form>
    </div>
</div>

<h3 class="text-center">Lista de Utilizadores</h3>
<table class="table table-striped">
    <thead>
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Username</th>
        <th>Tipo</th>
    </tr>
    </thead>
    <tbody>
    <?php while($user = $users->fetch_assoc()): ?>
        <tr>
            <td><?= $user['ID'] ?></td>
            <td><?= $user['NAME'] ?></td>
            <td><?= $user['USERNAME'] ?></td>
            <td><?= $user['TYPE_USER'] ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

</html>