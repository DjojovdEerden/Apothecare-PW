<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>register</title>
    <link rel="stylesheet" href="css/login.register.css">
</head>

<body>
    <div class="container">
        <h1>Welcome Back</h1>

        <form action="klant/register.php" method="post">

        <a href="klant/login.php" id="btnRegisterLogin">Login</a>


        <div class="input-styling">
            <h3>Email</h3>
            <input type="email" name="email" id="email" placeholder="Enter a valid email"required>

            <h3>Password</h3>
            <input type="password" name="password" id="password" placeholder="Create Password" required>
        </div>



        <input type="submit" value="Create account" id="create-account">
        </form>






    </div><!-- end of container -->
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "login_system";  
    
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (empty($_POST['email']) || empty($_POST['password'])) {
            die("Error: Email and password are required.");
        }

        $email = $_POST['email'];
        $password = $_POST['password'];

       
     
        $sql = "INSERT INTO users (email, password) VALUES (:email, :password)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password); 

        if ($stmt->execute()) {
            echo "Account successfully created!";
        } else {
            echo "Error: Could not create account.";
        }

   
    } catch (PDOException $e) {
        die("Gefaald buurman: " . $e->getMessage());
    }
    
   
    
    echo "Record toegevoegd!";
    ?>
</body>

</html>
