<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    
    <form method="post" action="action.php">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
            var email = localStorage.getItem('email');
            if (email) {
                document.getElementById('email').value = email;
                setTimeout(function() {
                localStorage.removeItem('email');
                alert('Email removed from local storage after 5 seconds.');
                }, 5000); // 40 seconds
            }
            });
        </script>
        <input type="submit" value="Register">
    </form>
</body>
</html>

<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "demo";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if email already exists
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "Email already registered.";
       
        echo "<script>
        if (localStorage.getItem('email')) {
            localStorage.removeItem('email');
        }
          </script>";
    } else {
        echo "<script>
            if (localStorage.getItem('email')) {
                localStorage.removeItem('email');
            }
            localStorage.setItem('email', '$email');
              </script>";
              echo "<br><a href='https://gmail.com' target='_blank'><button>Go to Gmail</button></a>";
    }
}

$conn->close();
?>
