<?php
// index.php - Homepage
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="vote.jpg" type="image/x-icon">
<title>Online Voting System</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-screen flex flex-col overflow-hidden">

<!-- 🌐 Navbar -->
<nav class="bg-white shadow-md z-10 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex justify-between items-center">
        <!-- Left -->
        <div class="flex items-center space-x-3">
            <img src="vote.jpg" alt="Logo" class="h-10 w-10 sm:h-12 sm:w-12 object-contain rounded-full">
            <h1 class="text-xl sm:text-2xl font-bold text-blue-600">OVS</h1>
        </div>
        <!-- Right -->
        <div class="space-x-4 sm:space-x-6 text-sm sm:text-base">
            <a href="login.php" class="text-gray-700 hover:text-blue-600 font-medium">Voter Login</a>
            <a href="a-login.php" class="text-gray-700 hover:text-red-600 font-medium">Admin</a>
        </div>
    </div>
</nav>

<!-- 🎉 Hero Section Fullscreen -->
<section class="relative flex-1 flex items-center justify-center min-h-[70vh] sm:min-h-[75vh] md:min-h-[80vh]">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <img src="vote2.jpg" alt="Voting Image" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/40"></div> <!-- Dark overlay -->
    </div>

    <!-- Centered Text -->
    <div class="relative text-center px-4 sm:px-8 md:px-20 max-w-4xl">
        <h2 class="text-2xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-4 leading-snug">
            Welcome to Online Voting System
        </h2>
        <p class="text-sm sm:text-base md:text-lg text-white/90 mb-6">
            A secure and transparent way to cast your valuable vote online.
        </p>
        <!-- Optional Register Button -->
        <!-- <a href="/OVS/Backend/register.php" 
           class="inline-block bg-blue-600 text-white px-5 sm:px-6 py-2 sm:py-3 rounded-lg hover:bg-blue-700 transition text-sm sm:text-base">
            Register Now
        </a> -->
    </div>
</section>

<!-- ⚡ Footer -->
<footer class="bg-white shadow-inner py-3 sm:py-4 text-center text-gray-600 text-sm sm:text-base">
    © <?= date("Y") ?> Online Voting System. All Rights Reserved.
</footer>

</body>
</html>
