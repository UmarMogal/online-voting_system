<!-- <?php
include '../db.php';

$candidates = $conn->query("SELECT id, name, party, votes FROM candidates");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate List</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-[#1d2b64] to-[#f8cdda] min-h-screen flex items-center justify-center p-4">
    <div class="bg-white shadow-2xl rounded-2xl p-6 w-full max-w-4xl">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">🗳️ Candidate List</h1>
        <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden">
            <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                <tr>
                    <th class="py-3 px-4 text-left border-b">ID</th>
                    <th class="py-3 px-4 text-left border-b">Name</th>
                    <th class="py-3 px-4 text-left border-b">Party</th>
                    <th class="py-3 px-4 text-left border-b">Votes</th>
                </tr>
            </thead>
            <tbody class="text-gray-800">
                <?php while($row = $candidates->fetch_assoc()): ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-2 px-4"><?= $row['id'] ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($row['name']) ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($row['party']) ?></td>
                    <td class="py-2 px-4 font-semibold"><?= $row['votes'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="mt-6 text-center">
            <a href="A-dashboard.php" class="text-blue-600 font-medium hover:underline">⬅ Back to Dashboard</a>
        </div>
    </div>
</body>
</html> -->
