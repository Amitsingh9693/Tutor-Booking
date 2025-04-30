<?php
session_start();

if (!isset($_SESSION['user_email']) || !isset($_SESSION['otp'])) {
    header("Location: mainlogin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    $entered_otp = '';
    for ($i = 1; $i <= 6; $i++) {
        $entered_otp .= $_POST['otp'.$i] ?? '';
    }
    
    if ($entered_otp == $_SESSION['otp']) {
        $_SESSION['verified'] = true;
        unset($_SESSION['otp']);
        header("Location: home.php");
        exit();
    } else {
        $error = "Invalid OTP. Please try again.";
    }
}

if (isset($_GET['resend'])) {
    $n=random_int(100000, 999999);
    $new_otp = $n;
    $_SESSION['otp'] = $new_otp;
    $i = "UPDATE userdata SET otp='$new_otp' WHERE email='{$_SESSION['user_email']}'";
    require_once 'sendmail.php';
    SendEmail($_SESSION['user_email'], $new_otp, $_SESSION['user_name']);
    $success = "New OTP has been sent to your email.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>TutorSphere - OTP Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #6B46C1;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            position: relative;
        }
        .container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 10;
        }
        .otp-input {
            width: 45px;
            height: 50px;
            text-align: center;
            font-size: 18px;
            border: 2px solid #E9D5FF;
            border-radius: 8px;
            margin: 0 5px;
            background-color: #FAF5FF;
        }
        .otp-input:focus {
            border-color: #8B5CF6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2);
        }
        .btn-verify {
            background-color: #6B46C1;
            transition: all 0.2s;
        }
        .btn-verify:hover {
            background-color: #5B3AA9;
            transform: translateY(-1px);
        }
        .title {
            color: #6B46C1;
        }
        .resend-link {
            color: #6B46C1;
            font-weight: 500;
        }
        .resend-link:hover {
            text-decoration: underline;
        }
        
        /* Star background styles */
        .stars {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            overflow: hidden;
        }
        .star {
            position: absolute;
            background-color: white;
            border-radius: 50%;
            animation: twinkle var(--duration) infinite ease-in-out;
            opacity: 0;
        }
        @keyframes twinkle {
            0%, 100% { opacity: 0; transform: scale(0.5); }
            50% { opacity: var(--opacity); transform: scale(1); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <!-- Star background -->
    <div class="stars" id="stars"></div>
    
    <div class="container p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="title text-3xl font-bold mb-2">TutorSphere</h1>
            <p class="text-gray-600">Unlock your learning potential</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-center">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-center">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <div class="text-center mb-6">
            <p class="text-gray-700 mb-2">We've sent a 6-digit code to</p>
            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
        </div>
        
        <form method="post" class="mb-6">
            <div class="flex justify-center mb-8">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <input type="text" name="otp<?php echo $i; ?>" maxlength="1" 
                           class="otp-input" oninput="moveToNext(this, <?php echo $i; ?>)" 
                           onkeydown="handleBackspace(this, <?php echo $i; ?>, event)">
                <?php endfor; ?>
            </div>
            
            <button type="submit" name="verify_otp" 
                    class="w-full btn-verify text-white py-3 px-4 rounded-lg font-medium">
                Verify OTP
            </button>
        </form>
        
        <div class="text-center text-gray-600">
            <p>Didn't receive code? <a href="?resend=1" class="resend-link">Resend OTP</a></p>
        </div>
    </div>

    <script>
        // Create animated stars
        function createStars() {
            const starsContainer = document.getElementById('stars');
            const starCount = 50;
            
            for (let i = 0; i < starCount; i++) {
                const star = document.createElement('div');
                star.classList.add('star');
                
                // Random properties
                const size = Math.random() * 3 + 1;
                const posX = Math.random() * 100;
                const posY = Math.random() * 100;
                const duration = Math.random() * 5 + 3;
                const delay = Math.random() * 5;
                const opacity = Math.random() * 0.7 + 0.3;
                
                star.style.width = `${size}px`;
                star.style.height = `${size}px`;
                star.style.left = `${posX}%`;
                star.style.top = `${posY}%`;
                star.style.setProperty('--duration', `${duration}s`);
                star.style.setProperty('--opacity', opacity);
                star.style.animationDelay = `${delay}s`;
                
                starsContainer.appendChild(star);
            }
        }
        
        // Initialize stars when page loads
        document.addEventListener('DOMContentLoaded', function() {
            createStars();
            document.querySelector('input[name="otp1"]').focus();
        });
        
        function moveToNext(input, currentIndex) {
            input.value = input.value.replace(/[^0-9]/g, '');
            if (input.value.length === 1 && currentIndex < 6) {
                document.querySelector('input[name="otp' + (currentIndex + 1) + '"]').focus();
            }
        }
        
        function handleBackspace(input, currentIndex, event) {
            if (event.key === 'Backspace' && input.value === '' && currentIndex > 1) {
                document.querySelector('input[name="otp' + (currentIndex - 1) + '"]').focus();
            }
        }
    </script>
</body>
</html>