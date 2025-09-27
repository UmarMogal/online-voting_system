<?php
session_start();
include("db.php"); // DB connection

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query username ke liye
    $query = "SELECT * FROM admin WHERE username = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();

        // Agar password hash me hai to verify karega
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = $admin;
            header("Location: a-dashboard.php"); // login success ke baad dashboard pe
            exit();
        } else {
            $error = "❌ Invalid password";
        }
    } else {
        $error = "❌ Username not found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="vote.jpg" type="image/x-icon">
  <title>Admin Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-[#1d2b64] to-[#f8cdda] min-h-screen flex items-center justify-center p-4">

  <div class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-md sm:max-w-sm md:max-w-md">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">🔑 Admin Login</h2>

    <?php if (!empty($error)) : ?>
      <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 text-center">
        <?php echo $error; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="a-login.php" class="space-y-4">
      <div>
        <label class="block text-gray-700 mb-1">Username</label>
        <input type="text" name="username" placeholder="Enter Username"
          class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
      </div>

      <div>
        <label class="block text-gray-700 mb-1">Password</label>
        <input type="password" name="password" placeholder="Enter Password"
          class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
      </div>

      <button type="submit"
        class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-200">
        Login
      </button>
    </form>
  </div>

</body>
</html>
