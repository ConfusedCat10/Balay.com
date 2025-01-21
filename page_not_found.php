<!DOCTYPE html>
<html lang="en">

<head>
  <title>Page Not Found - Balay.com</title>
  
  <?php include "php/head_tag.php"; ?>

    <style>

        body {
            background-color: black;
            padding-top: 220px;
        }

        .container {
            height: 100%;
            width: 100%;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .container img {
            height: 100px;
            width: auto;
        }

        .go-home-btn, .go-back-btn {
            padding: 20px;
            color: white;
            background-color: maroon;
            width: 100%;
            font-size: 14px;
            outline: none;
            border: none;
            border-radius: 40px;
            margin: 20px;
        }        

        .go-home-btn:hover, .go-back-btn:hover {
            background-color: #ffd700;
            color: black;
            cursor: pointer;
        }
    </style>
</head>

<body>
    
    <div class="container">
        <img src="/bookingapp/assets/site-logo/logo-text-white.png" alt="Balay.com Logo">
        <h1 style="margin-top: 20px;">YOU'RE LOST!</h1>
        <p>The page doesn't exist.</p>
        <div style="display: flex; padding: 10px; width: 50%">   
            <button class="go-back-btn" id="goBackButton"><i class="fa-classic fa-backward"></i> Go Back</button>
            <button class="go-home-btn" onclick="redirect('/bookingapp/index.php');"><i class="fa-solid fa-home"></i> Go to Home</button>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-bar">
            Copyright &copy; <span id="year"></span> College of Information and Computing Sciences &middot; Mindanao State University - Main Campus.
        </div>
    </footer>

    <script>

        function redirect(url) {
            window.location.href = url;
        }

        document.getElementById("goBackButton").addEventListener("click", function() {
            window.history.back();
        });

        // Set the current year in the footer dynamically
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>

</body>

</html>

