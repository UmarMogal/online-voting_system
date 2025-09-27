<?php
session_start();
include 'db.php';

// Safety check
if (!isset($conn)) {
    die("❌ Database connection not found. Check db.php!");
}

// Check voter session
if (!isset($_SESSION['voter'])) {
    header("Location: login.php");
    exit();
}

$voter = $_SESSION['voter'];
$voter_id = $voter['id'];

// Handle vote submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['candidate_id'])) {
        $_SESSION['message'] = "❌ No candidate selected.";
        header("Location: dashbord.php");
        exit();
    }

    $candidate_id = (int)$_POST['candidate_id'];

    // Check if already voted
    $check = $conn->prepare("SELECT has_voted FROM voters WHERE id = ?");
    $check->bind_param("i", $voter_id);
    $check->execute();
    $result = $check->get_result();
    $voterData = $result->fetch_assoc();

    if ($voterData['has_voted'] == 1) {
        $_SESSION['message'] = "⚠️ You have already voted";
    } else {
        // Insert vote
        $stmt = $conn->prepare("INSERT INTO votes (voter_id, candidate_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $voter_id, $candidate_id);
        $executeVote = $stmt->execute();

        if ($executeVote) {
            // Update voter status
            $update = $conn->prepare("UPDATE voters SET has_voted = 1 WHERE id = ?");
            $update->bind_param("i", $voter_id);
            $update->execute();

            // Update candidate votes
            $updateCandidate = $conn->prepare("UPDATE candidates SET votes = votes + 1 WHERE id = ?");
            $updateCandidate->bind_param("i", $candidate_id);
            $updateCandidate->execute();

            $_SESSION['message'] = "✅ Your vote has been successfully cast!";
        } else {
            $_SESSION['message'] = "❌ Something went wrong. Try again.";
        }
    }

    header("Location: dashbord.php");
    exit();
}

// Fetch candidates for display
$candidatesQuery = $conn->query("SELECT * FROM candidates ORDER BY votes DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cast Vote</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-[#1d2b64] to-[#f8cdda] min-h-screen flex flex-col items-center justify-center p-4">

<div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-2xl">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">🗳️ Cast Your Vote</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="mb-4 p-3 text-center font-medium rounded 
            <?= str_contains($_SESSION['message'], '✅') ? 'bg-green-100 text-green-700' : (str_contains($_SESSION['message'], '⚠️') ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') ?>">
            <?= $_SESSION['message'] ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <?php while($candidate = $candidatesQuery->fetch_assoc()): ?>
            <div class="flex items-center gap-4 p-3 border rounded hover:bg-gray-50 cursor-pointer">
                <input type="radio" name="candidate_id" value="<?= $candidate['id'] ?>" id="cand<?= $candidate['id'] ?>" class="h-5 w-5 text-blue-600">
                <?php if (!empty($candidate['logo'])): ?>
                    <img src="../images/<?= htmlspecialchars($candidate['logo']) ?>" alt="Logo" class="w-12 h-12 object-contain rounded">
                <?php endif; ?>
                <label for="cand<?= $candidate['id'] ?>" class="flex flex-col">
                    <span class="font-semibold text-gray-800"><?= htmlspecialchars($candidate['name']) ?></span>
                    <span class="text-gray-600 text-sm"><?= htmlspecialchars($candidate['party']) ?></span>
                </label>
            </div>
        <?php endwhile; ?>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
            Submit Vote
        </button>
    </form>

    <div class="mt-4 text-center">
        <a href="dashbord.php" class="text-blue-600 font-medium hover:underline">⬅ Back to Dashboard</a>
    </div>
</div>

</body>
</html>
