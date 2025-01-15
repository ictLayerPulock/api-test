<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Username Form</title>
    <link
      href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"
      rel="stylesheet"
    />
  </head>
  <body class="bg-gray-900 flex items-center justify-center h-screen">
    <div class="max-w-md mx-auto mt-10">
      <label for="username" class="block text-sm font-medium text-gray-300"
        >Username</label
      >
      <input
        type="text"
        id="username"
        name="username"
        class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        placeholder="Enter username"
        oninput="checkUsername(this.value)"
      />
      <!-- Error message -->
      <p id="username-error" class="mt-2 text-sm text-red-500 hidden">
        This username is not available.
      </p>
      <!-- Success message -->
      <p id="username-valid" class="mt-2 text-sm text-green-500 hidden">
        This username is available.
      </p>
    </div>

    <script>
      function checkUsername(username) {
        if (username.length === 0) {
          document.getElementById("username-error").classList.add("hidden");
          document.getElementById("username-valid").classList.add("hidden");
          return;
        }

        console.log("Checking username:", username);

        fetch("action.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ username: username }),
        })
          .then((response) => {
            console.log("Response status:", response.status);
            return response.json();
          })
          .then((data) => {
            console.log("Response data:", data);

            const errorMessage = document.getElementById("username-error");
            const validMessage = document.getElementById("username-valid");

            if (data.exists) {
              errorMessage.classList.remove("hidden");
              validMessage.classList.add("hidden");
            } else {
              validMessage.classList.remove("hidden");
              errorMessage.classList.add("hidden");
            }
          })
          .catch((error) => console.error("Error:", error));
      }
    </script>
  </body>
</html>

//action.php
<?php
header("Content-Type: application/json");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "demo";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['username'])) {
    $username = trim($data['username']);

    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    echo json_encode(['exists' => $count > 0, 'username' => $username]);
    exit;
}

echo json_encode(['exists' => false]);
?>
