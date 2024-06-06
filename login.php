<?php
session_start();
$connect = mysqli_connect("localhost", "root", "", "pi-sim");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = hash("sha256", $_POST['password']);

    // Usar prepared statements para evitar SQL Injection
    $query = "SELECT * FROM users WHERE USERNAME = ? AND PASSWORD = ?";
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_array($result);

    if ($row) { // Verifica se a consulta retornou um usuário
        $_SESSION['user_id'] = $row['ID'];
        $_SESSION['user_type'] = $row['USER_TYPE'];


        // Redirecionamento baseado no tipo de usuário
        switch ($row['USER_TYPE']) {
            case 'ADMIN':
                header("Location: admin.php");
                exit();
            case 'M':
                header("Location: medico.php");
                exit();
            case 'P':
                header("Location: paciente.php");
                exit();
            default:
                $error = "Invalid user type.";
        }
    } else {
        $error = "Invalid username or password.";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($connect);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
<h2>Login</h2>
<form method="POST" action="">
    <p>Enter your username: <input type="text" name="username" required></p>
    <p>Enter your password: <input type="password" name="password" required></p>
    <p><input type="submit" name="login" value="Login"></p>
</form>
<p>Ainda não tem uma conta? <a href="registration.php">Registe-se</a></p>

<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>
</body>
</html>
