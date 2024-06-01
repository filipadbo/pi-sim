<?php
session_start();
$connect = mysqli_connect("localhost", "root", "", "pi-sim");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE USERNAME = '$username' AND PASSWORD = '$password'";
    $result = mysqli_query($connect, $query);
    $row = mysqli_fetch_array($result);

    if ($row) {
        $_SESSION['user_id'] = $row['ID'];
        $_SESSION['user_type'] = $row['TYPE_USER'];

        switch ($row['TYPE_USER']) {
            case 'ADMIN':
                header("Location: estetica_admin.php");
                exit();
            case 'M':
                header("Location: medico.php");
                exit();
            case 'P':
                header("Location: paciente.php");
                exit();
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
<h2>Login</h2>
<FORM method="POST" action="">
    <p>Enter your username: <input type="text" name="username"></p>
    <p>Enter your password: <input type="password" name="password"></p>
    <p><input type="submit" name="login" value="Login"></p>
</FORM>

<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>
</body>
</html>
