<?php
include 'db.php';
session_start();

$message = "";

// Cloudinary details
$cloud_name = "Use your cloudinary name"; // Replace with your Cloudinary cloud name
$upload_preset = "Use your Preset name"; // Replace with your unsigned preset name

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_candidate'])) {
    if (!empty($_POST['name']) && !empty($_POST['party'])) {
        $name = $_POST['name'];
        $party = $_POST['party'];
        $logo_url = null;

        // Handle logo upload to Cloudinary
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
            $file = $_FILES['logo']['tmp_name'];

            // cURL request to Cloudinary unsigned upload
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/$cloud_name/image/upload");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_POST, TRUE);

            $data = [
                "file" => new CURLFile($file),
                "upload_preset" => $upload_preset
            ];

            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);

            if (isset($result['secure_url'])) {
                $logo_url = $result['secure_url']; // Cloudinary hosted URL
            } else {
                $message = "❌ Logo upload failed!";
            }
        }

        // Insert candidate into DB
        if (empty($message)) {
            $stmt = $conn->prepare("INSERT INTO candidates (name, party, votes, logo) VALUES (?, ?, 0, ?)");
            $stmt->bind_param("sss", $name, $party, $logo_url);
            if ($stmt->execute()) {
                $_SESSION['msg'] = "✅ Candidate added successfully!";
                header("Location: a-dashboard.php");
                exit();
            } else {
                $message = "❌ Database error: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $message = "❗ Both name and party fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Candidate</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-[#1d2b64] to-[#f8cdda] min-h-screen flex items-center justify-center">

<div class="bg-white shadow-xl rounded-xl p-8 w-full max-w-md">
  <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Add New Candidate</h2>

  <?php if (!empty($message)): ?>
    <div class="mb-4 text-center text-red-600 font-semibold"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <form method="POST" action="" enctype="multipart/form-data" class="space-y-5">
    <input type="text" name="name" placeholder="Candidate Name" required class="w-full px-4 py-2 border rounded-lg">
    <input type="text" name="party" placeholder="Party Name" required class="w-full px-4 py-2 border rounded-lg">
    <input type="file" name="logo" accept="image/*" class="w-full py-2">
    <button type="submit" name="add_candidate" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">Add Candidate</button>
  </form>
</div>

</body>
</html>
