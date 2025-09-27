<?php
session_start();
include 'db.php';

// ---------- MESSAGE ----------
$message = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);

// ---------- LOGOUT ----------
if(isset($_GET['logout'])){
    session_destroy();
    header("Location:index.php"); 
    exit();
}

// ---------- CHECK ADMIN LOGIN ----------
if (!isset($_SESSION['admin'])) {
    header("Location:a-login.php");
    exit();
}

// ---------- ADD CANDIDATE ----------
if(isset($_POST['addCandidate'])){
    $name = $_POST['name'];
    $party = $_POST['party'];
    $logo = $_POST['logo_url'] ?? '';

    $stmt = $conn->prepare("INSERT INTO candidates (name, party, logo, votes) VALUES (?, ?, ?, 0)");
    $stmt->bind_param("sss", $name, $party, $logo);
    if($stmt->execute()){
        $_SESSION['msg'] = "✅ Candidate Added Successfully";
    } else {
        $_SESSION['msg'] = "❌ Database Error: " . $stmt->error;
    }
    header("Location: a-dashboard.php");
    exit();
}

// ---------- UPDATE CANDIDATE ----------
if(isset($_POST['updateCandidate'])){
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $party = $_POST['party'];
    $logo = $_POST['logo_url'] ?? $_POST['old_logo'];

    $stmt = $conn->prepare("UPDATE candidates SET name=?, party=?, logo=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $party, $logo, $id);
    $stmt->execute();

    $_SESSION['msg'] = "✏️ Candidate Updated Successfully";
    header("Location: a-dashboard.php");
    exit();
}

// ---------- DELETE CANDIDATE ----------
if(isset($_GET['deleteCandidate'])){
    $id = intval($_GET['deleteCandidate']);
    $conn->query("DELETE FROM candidates WHERE id=$id");
    $_SESSION['msg'] = "❌ Candidate Deleted";
    header("Location: a-dashboard.php");
    exit();
}

// ---------- RESET ALL VOTERS ----------
if(isset($_POST['resetVoters'])){
    $result = $conn->query("TRUNCATE TABLE voters");  
    $_SESSION['msg'] = $result ? "✅ All voters have been cleared" : "❌ Error resetting voters: " . $conn->error;
    header("Location: a-dashboard.php");
    exit();
}

// ---------- RESET ALL VOTES ----------
if(isset($_POST['resetVotes'])){
    $result = $conn->query("UPDATE candidates SET votes = 0");
    $_SESSION['msg'] = $result ? "✅ All votes have been reset to 0" : "❌ Error resetting votes: " . $conn->error;
    header("Location: a-dashboard.php");
    exit();
}

// ---------- FETCH DATA ----------
$candidates = $conn->query("SELECT * FROM candidates ORDER BY votes DESC");
$voters = $conn->query("SELECT * FROM voters");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<link rel="shortcut icon" href="vote.jpg" type="image/x-icon">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://widget.cloudinary.com/v2.0/global/all.js"></script>
</head>
<body class="bg-gradient-to-r from-[#1d2b64] to-[#f8cdda] min-h-screen font-sans">

<div class="max-w-7xl mx-auto p-4 sm:p-6">

    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white text-center sm:text-left drop-shadow-lg">Admin Dashboard</h1>
        <a href="?logout=1" class="bg-red-600 text-white px-5 py-2 rounded-xl shadow hover:bg-red-700 transition w-full sm:w-auto text-center">Logout</a>
    </div>

    <!-- Message -->
    <?php if($message): ?>
        <div class="mb-4 text-green-100 font-semibold bg-green-700 px-4 py-2 rounded-lg text-center sm:text-left shadow">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Toggle Buttons -->
    <div class="flex flex-wrap gap-4 mb-6 justify-center sm:justify-start">
        <button id="candidatesBtn" class="bg-white text-purple-700 px-4 py-2 rounded-xl shadow hover:bg-purple-100 transition w-full sm:w-auto">Candidates</button>
        <button id="votersBtn" class="bg-white text-purple-700 px-4 py-2 rounded-xl shadow hover:bg-purple-100 transition w-full sm:w-auto">Voters</button>
        <button id="resultsBtn" class="bg-white text-purple-700 px-4 py-2 rounded-xl shadow hover:bg-purple-100 transition w-full sm:w-auto">Results</button>
    </div>

    <!-- Candidates Section -->
    <div id="candidatesSection" class="bg-white p-6 rounded-2xl shadow-lg">
        <h2 class="text-2xl font-bold text-purple-700 mb-4">Candidates</h2>

        <!-- Reset All Votes -->
        <form method="POST" class="mb-4">
            <button type="submit" name="resetVotes" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Reset All Votes</button>
        </form>

        <!-- Add Candidate Form -->
        <form method="POST" class="mb-6 space-y-3 bg-purple-50 p-4 rounded-xl shadow-inner">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <input type="text" name="name" placeholder="Candidate Name" required class="border px-3 py-2 rounded-lg w-full focus:ring-2 focus:ring-purple-300">
                <input type="text" name="party" placeholder="Party Name" required class="border px-3 py-2 rounded-lg w-full focus:ring-2 focus:ring-purple-300">
                <div class="flex flex-col">
                    <button type="button" id="uploadWidgetBtn" class="bg-blue-600 text-white py-2 px-3 rounded-lg hover:bg-blue-700 transition">Upload Logo</button>
                    <input type="hidden" name="logo_url" id="logoUrl">
                    <img id="previewLogo" src="" class="mt-2 w-16 h-16 object-contain hidden rounded">
                </div>
            </div>
            <button type="submit" name="addCandidate" class="bg-green-600 text-white px-5 py-2 rounded-xl shadow hover:bg-green-700 transition w-full sm:w-auto">
                Add Candidate
            </button>
        </form>

        <!-- Candidate Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded overflow-hidden text-sm sm:text-base">
                <thead class="bg-purple-100 text-purple-800">
                    <tr>
                        <th class="px-4 py-2 border">Logo</th>
                        <th class="px-4 py-2 border">Name</th>
                        <th class="px-4 py-2 border">Party</th>
                        <th class="px-4 py-2 border">Votes</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $candidates->fetch_assoc()): ?>
                    <tr class="hover:bg-purple-50 transition">
                        <td class="px-4 py-2 border text-center">
                            <img src="<?= htmlspecialchars($row['logo'] ?: 'images/MIM.jpeg') ?>" class="w-12 h-12 sm:w-16 sm:h-16 object-contain rounded mx-auto">
                        </td>
                        <td class="px-4 py-2 border"><?= htmlspecialchars($row['name']) ?></td>
                        <td class="px-4 py-2 border"><?= htmlspecialchars($row['party']) ?></td>
                        <td class="px-4 py-2 border font-bold text-green-600"><?= $row['votes'] ?></td>
                        <td class="px-4 py-2 border flex flex-col sm:flex-row gap-2">
                            <button 
                                onclick="openEditModal(<?= $row['id'] ?>, '<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['party'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['logo']) ?>')" 
                                class="bg-blue-600 text-white px-3 py-1 rounded-xl shadow hover:bg-blue-700 transition w-full sm:w-auto">Edit</button>
                            <a href="?deleteCandidate=<?= $row['id'] ?>" 
                               onclick="return confirm('Delete this candidate?')" 
                               class="bg-red-600 text-white px-3 py-1 rounded-xl shadow hover:bg-red-700 transition w-full sm:w-auto text-center">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Voters Section -->
    <div id="votersSection" class="bg-white p-6 rounded-2xl shadow-lg mt-6 hidden">
        <h2 class="text-2xl font-bold text-purple-700 mb-4">Voters</h2>

        <!-- Reset All Voters Button -->
        <form method="POST" class="mb-4">
            <button type="submit" name="resetVoters" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 w-full sm:w-auto">
                Reset All Voters
            </button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded overflow-hidden text-sm sm:text-base">
                <thead class="bg-purple-100 text-purple-800">
                    <tr>
                        <th class="px-4 py-2 border">Name</th>
                        <th class="px-4 py-2 border">Email</th>
                        <th class="px-4 py-2 border">Phone</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $voters->fetch_assoc()): ?>
                    <tr class="hover:bg-purple-50">
                        <td class="px-4 py-2 border"><?= htmlspecialchars($row['name']) ?></td>
                        <td class="px-4 py-2 border"><?= htmlspecialchars($row['email']) ?></td>
                        <td class="px-4 py-2 border"><?= htmlspecialchars($row['phone']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Results Section -->
    <div id="resultsSection" class="bg-white p-6 rounded-2xl shadow-lg mt-6 hidden">
        <h2 class="text-2xl font-bold text-purple-700 mb-4">Election Results</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded overflow-hidden text-sm sm:text-base">
                <thead class="bg-purple-100 text-purple-800">
                    <tr>
                        <th class="px-4 py-2 border">Rank</th>
                        <th class="px-4 py-2 border">Candidate</th>
                        <th class="px-4 py-2 border">Party</th>
                        <th class="px-4 py-2 border">Votes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rank = 1;
                    $result = $conn->query("SELECT * FROM candidates ORDER BY votes DESC");
                    while($row = $result->fetch_assoc()):
                        $highlight = ($rank == 1 && $row['votes'] > 0) ? "bg-green-100 border-2 border-green-500" : "";
                    ?>
                    <tr class="hover:bg-purple-50 <?= $highlight ?>">
                        <td class="px-4 py-2 border font-bold"><?= $rank ?></td>
                        <td class="px-4 py-2 border flex items-center gap-3">
                            <img src="<?= htmlspecialchars($row['logo'] ?: 'images/MIM.jpeg') ?>" class="w-10 h-10 object-contain rounded">
                            <?= htmlspecialchars($row['name']) ?>
                            <?php if($rank == 1 && $row['votes'] > 0): ?>
                                <span class="ml-2 bg-green-600 text-white text-xs px-2 py-1 rounded">Winner 🏆</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-2 border"><?= htmlspecialchars($row['party']) ?></td>
                        <td class="px-4 py-2 border font-bold text-green-700"><?= $row['votes'] ?></td>
                    </tr>
                    <?php $rank++; endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Edit Candidate Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-lg w-full max-w-md p-6 relative mx-2 sm:mx-0">
        <button onclick="closeEditModal()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-lg font-bold">✖</button>
        <h3 class="text-xl font-semibold mb-4">Edit Candidate</h3>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="id" id="editId">
            <input type="hidden" name="old_logo" id="editOldLogo">
            <input type="hidden" name="logo_url" id="editLogoUrl">

            <div>
                <label class="block text-sm font-medium mb-1">Name</label>
                <input type="text" name="name" id="editName" required class="w-full border px-3 py-2 rounded">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Party</label>
                <input type="text" name="party" id="editParty" required class="w-full border px-3 py-2 rounded">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Logo (optional)</label>
                <button type="button" id="editUploadBtn" class="bg-blue-600 text-white py-2 px-3 rounded-lg hover:bg-blue-700 transition mb-2">Upload Logo</button>
                <img id="editPreviewLogo" src="" class="mt-2 w-16 h-16 object-contain hidden rounded">
            </div>

            <button type="submit" name="updateCandidate" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">
                Update Candidate
            </button>
        </form>
    </div>
</div>

<script>
// ---------- Cloudinary Add Candidate ----------
const myWidget = cloudinary.createUploadWidget({
    cloudName: 'Use your  name cloudinary',
    uploadPreset: 'Use your Preset name',
    multiple: false,
    folder: 'candidates'
}, (error, result) => {
    if (!error && result && result.event === "success") {
        document.getElementById('logoUrl').value = result.info.secure_url;
        const preview = document.getElementById('previewLogo');
        preview.src = result.info.secure_url;
        preview.classList.remove('hidden');
    }
});

document.getElementById("uploadWidgetBtn").addEventListener("click", () => myWidget.open());

// ---------- Cloudinary Edit Candidate ----------
const editWidget = cloudinary.createUploadWidget({
    cloudName: 'Use your  name cloudinary',
    uploadPreset: 'Use your Preset name',
    multiple: false,
    folder: 'candidates'
}, (error, result) => {
    if (!error && result && result.event === "success") {
        document.getElementById('editLogoUrl').value = result.info.secure_url;
        const preview = document.getElementById('editPreviewLogo');
        preview.src = result.info.secure_url;
        preview.classList.remove('hidden');
    }
});

document.getElementById("editUploadBtn").addEventListener("click", () => editWidget.open());

// ---------- Toggle Sections ----------
const candidatesBtn = document.getElementById('candidatesBtn');
const votersBtn = document.getElementById('votersBtn');
const resultsBtn = document.getElementById('resultsBtn');

const candidatesSection = document.getElementById('candidatesSection');
const votersSection = document.getElementById('votersSection');
const resultsSection = document.getElementById('resultsSection');

candidatesBtn.addEventListener('click', () => { candidatesSection.classList.remove('hidden'); votersSection.classList.add('hidden'); resultsSection.classList.add('hidden'); });
votersBtn.addEventListener('click', () => { votersSection.classList.remove('hidden'); candidatesSection.classList.add('hidden'); resultsSection.classList.add('hidden'); });
resultsBtn.addEventListener('click', () => { resultsSection.classList.remove('hidden'); candidatesSection.classList.add('hidden'); votersSection.classList.add('hidden'); });

// ---------- Modal ----------
function openEditModal(id, name, party, logo) {
    document.getElementById("editModal").classList.remove("hidden");
    document.getElementById("editModal").classList.add("flex");

    document.getElementById("editId").value = id;
    document.getElementById("editName").value = name;
    document.getElementById("editParty").value = party;
    document.getElementById("editOldLogo").value = logo;

    if(logo) {
        const preview = document.getElementById("editPreviewLogo");
        preview.src = logo;
        preview.classList.remove("hidden");
    }
}

function closeEditModal() {
    document.getElementById("editModal").classList.add("hidden");
    document.getElementById("editModal").classList.remove("flex");
}
</script>
</body>
</html>
