<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/login.register.css">
</head>

<body>
    <div class="container">
        <h1>Welcome Back</h1>


        <a href="klant/register.php" id="btnRegisterLogin">Register</a>

        <form action="login.php" method="post">
        <div class="input-styling">
            <h3>Email</h3>
            <input type="text" name="email" id="email" placeholder="Enter your email" required>

            <h3>Password</h3>
            <input type="password" name="password" id="password" placeholder="Enter Password" required>
        </div>
        <div class="button-textalign">
        <input type="checkbox" id="remember" style="vertical-align: middle;">Remember me
        </div>
    
        <input type="submit" value="Login" id="login">
</form>

        <a href="https://www.youtube.com/watch?v=xvFZjo5PgG0">Forgot your password?</a>





    </div><!-- end of container -->
    <?php

     
session_start();

//database connectie
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_system";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// kijk of de form is gechecktet ander else statement
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password']; 

    // query uitvoeren om uit de email te halen
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // kijk naar de password en of die juist is van de user
    if ($user && $password == $user['password']) {
        $_SESSION['user_id'] = $user['ID'];
        echo "Welcome back!!!, " ;
       
    } else {
        echo "Invalid email or password!";
    }
}
    ?>
</body>

</html>