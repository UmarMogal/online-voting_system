<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['voter'])) {
    header("Location: login.php");
    exit();
}

$voter = $_SESSION['voter'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Dashboard</title>
  <link rel="shortcut icon" href="vote.jpg" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-[#1d2b64] to-[#f8cdda] flex items-center justify-center min-h-screen p-4">

    <div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-md sm:max-w-sm md:max-w-md">
        
        <!-- ✅ Flash Messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="mb-4 p-3 rounded text-center font-medium 
                        <?php if (str_contains($_SESSION['message'], '✅')): ?>
                            bg-green-100 text-green-700
                        <?php elseif (str_contains($_SESSION['message'], '⚠️')): ?>
                            bg-yellow-100 text-yellow-700
                        <?php else: ?>
                            bg-red-100 text-red-700
                        <?php endif; ?>">
                <?= $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <h2 class="text-2xl font-bold text-gray-800 mb-4 text-center">
            Welcome, <?= htmlspecialchars($voter['name']) ?> 👋
        </h2>
        <p class="text-gray-600 mb-6 text-center">
            You are logged in as <strong><?= htmlspecialchars($voter['email']) ?></strong>
        </p>

        <!-- Centered Button -->
        <div class="flex justify-center mb-6">
            <a href="vote.php" 
               class="bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg text-center hover:bg-blue-700 transition duration-200">
                🗳️ Cast Your Vote
            </a>
        </div>

        <div class="text-center">
            <a href="https://ovss.infinityfreeapp.com/logout.php" 
               class="text-red-600 font-medium hover:underline">Logout</a>
        </div>
    </div>

</body>
</html>
