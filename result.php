<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location:login.php");
    exit();
}

include 'db.php';

// Fetch results
$sql = "
    SELECT c.id, c.name, c.party, COUNT(v.id) as total_votes
    FROM candidates c
    LEFT JOIN votes v ON c.id = v.candidate_id
    GROUP BY c.id, c.name, c.party
    ORDER BY total_votes DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Results - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-[#1d2b64] to-[#f8cdda] flex items-center justify-center min-h-screen p-4">

    <div class="bg-white shadow-xl rounded-2xl p-6 w-full max-w-3xl overflow-x-auto">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">📊 Voting Results</h2>

        <table class="w-full border-collapse border border-gray-300 text-sm sm:text-base">
            <thead>
                <tr class="bg-gray-200 text-gray-700">
                    <th class="border border-gray-300 px-3 py-2">Candidate</th>
                    <th class="border border-gray-300 px-3 py-2">Party</th>
                    <th class="border border-gray-300 px-3 py-2">Total Votes</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="text-center hover:bg-gray-50">
                        <td class="border border-gray-300 px-3 py-2"><?= htmlspecialchars($row['name']) ?></td>
                        <td class="border border-gray-300 px-3 py-2"><?= htmlspecialchars($row['party']) ?></td>
                        <td class="border border-gray-300 px-3 py-2 font-semibold"><?= $row['total_votes'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="mt-6 text-center">
            <a href="a-dashboard.php" class="text-blue-600 font-medium hover:underline">⬅ Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
