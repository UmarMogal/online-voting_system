<?php
include 'db.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO voters (name, email, phone, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssss", $name, $email, $phone, $password);
        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            $message = "Error: " . $stmt->error;
        }
    } else {
        $message = "Database error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="shortcut icon" href="../Backend/images/Vote.jpg" type="image/x-icon">
  <title>Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-[#1d2b64] to-[#f8cdda] min-h-screen flex items-center justify-center p-4">

  <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md sm:max-w-sm md:max-w-md">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Register to Vote</h2>

    <?php if (!empty($message)): ?>
      <div class="mb-4 text-center text-sm font-medium <?= strpos($message, 'success') !== false ? 'text-green-600' : 'text-red-600' ?>">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <form action="" method="POST" class="space-y-4">
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
        <input type="text" name="name" id="name" required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" id="email" required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      <div>
        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
        <input type="text" name="phone" id="phone" required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <input type="password" name="password" id="password" required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      <button type="submit"
              class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200">
        Register
      </button>
    </form>

    <p class="mt-4 text-sm text-center text-gray-500">
      Already have an account? 
      <a href="login.php" class="text-blue-600 hover:underline">Login</a>
    </p>
  </div>
</body>
</html>
