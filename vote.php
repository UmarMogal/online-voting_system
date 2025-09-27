<?php
// vote.php
session_start();
include 'db.php';

// Check voter session
if (!isset($_SESSION['voter'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="vote.jpg" type="image/x-icon">
<title>Vote Now</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-[#1d2b64] to-[#f8cdda] min-h-screen flex items-center justify-center p-4">

<div class="bg-white shadow-2xl rounded-2xl p-6 sm:p-10 w-full max-w-lg">
    <h1 class="text-3xl sm:text-4xl font-bold text-center text-gray-800 mb-8">🗳️ Vote Now</h1>

    <?php
    $result = $conn->query("SELECT id, name, party, logo FROM candidates ORDER BY votes DESC");
    if ($result->num_rows > 0): ?>
        <form method="POST" action="submit_vote.php" class="space-y-4">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="flex items-center space-x-4 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="radio" id="candidate<?= $row['id'] ?>" name="candidate_id" value="<?= $row['id'] ?>" class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500">
                    
                    <?php if(!empty($row['logo'])): ?>
                        <img src="<?= htmlspecialchars($row['logo']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="w-12 h-12 object-contain rounded-full">
                    <?php else: ?>
                        <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 text-xs">No Logo</div>
                    <?php endif; ?>
                    
                    <label for="candidate<?= $row['id'] ?>" class="text-gray-700 font-semibold text-sm sm:text-lg">
                        <?= htmlspecialchars($row['name']) ?> <span class="text-gray-500 text-sm sm:text-base">(<?= htmlspecialchars($row['party']) ?>)</span>
                    </label>
                </div>
            <?php endwhile; ?>

            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg mt-6 hover:bg-blue-700 transition font-bold text-lg">
                Submit Vote
            </button>
        </form>
    <?php else: ?>
        <p class="text-center text-red-600 font-semibold mt-4">No candidates available for voting.</p>
    <?php endif; ?>
</div>

</body>
</html>
