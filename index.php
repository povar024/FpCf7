<?php
    session_start(); //Ξεκινάει η συνεδρία
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /*CSS Styling*/

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            background-color: #fff;
        }

        .sidebar {
            width: 250px;
            background-color: #2c2c2c;
            color: #f4f4f4;
            display: flex;
            flex-direction: column;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.3);
            height: 100%;
        }

        .sidebar a {
            color: #f4f4f4;
            text-decoration: none;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: burlywood;
            color: #000;
        }

        .sidebar a i {
            margin-right: 10px;
            font-size: 20px;
        }

        .main-content {
            flex: 1;
            padding: 40px;
            background: url('https://images.pexels.com/photos/3944422/pexels-photo-3944422.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1') center/cover no-repeat;
            color: #000;
            overflow-y: auto;
        }

        .main-content h1 {
            font-size: 36px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }

        .main-content p, .main-content ul {
            font-size: 18px;
            line-height: 1.8;
            margin-bottom: 20px;
            color: black;
        }

        .main-content ul li {
            margin-bottom: 10px;
        }

        iframe {
            width: 100%;
            height: 600px;
            border: none;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            .main-content {
                padding: 20px;
            }
        }

        footer {
            background-color: #2c2c2c;
            color: #f4f4f4;
            text-align: center;
            padding: 10px 0;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
        
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="register.php"><i class="fas fa-user-plus"></i> Εγγραφή</a>
        <a href="login.php"><i class="fas fa-sign-in-alt"></i> Σύνδεση</a>
    </div>
    <div class="main-content">
        <h1>Καλωσήρθατε στην εφαρμογή Σημειώσεων!</h1>
        <?php
        //Σύνδεση με τη βάση δεδομένων
        $dsn = 'mysql:host=localhost;dbname=appnotes_db;charset=utf8mb4';
        $dbUsername = 'root'; 
        $dbPassword = '';

        try {
            $pdo = new PDO($dsn, $dbUsername, $dbPassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Σφάλμα σύνδεσης στη βάση δεδομένων: " . $e->getMessage());
        }

        if (isset($_SESSION['user_id'])) {
            //Ανάκτηση του username με βάση το user_id από τη συνεδρία
            $stmt = $pdo->prepare("SELECT username FROM users WHERE id = :user_id");
            $stmt->execute(['user_id' => $_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if ($user) {
                //Εμφανίζουμε το username
                echo "<p>Καλώς ήρθατε, " . htmlspecialchars($user['username']) . "!</p>";
            } else {
                //Σε περίπτωση που ο χρήστης δεν βρεθεί (π.χ. διαγράφηκε)
                echo "<p>Σφάλμα: Ο χρήστης δεν βρέθηκε.</p>";
            }
        } else {
            //Αν ο χρήστης δεν είναι συνδεδεμένος, εμφανίζουμε τα bullets
            echo "<p>Είτε επιθυμείτε να αποτυπώσετε τις ιδέες και τις σκέψεις σας ή απλά να σημειώσετε τις υπενθυμίσεις σας, μπορείτε να:</p>";
            echo "<ul>
                    <li>Δημιουργήστε και αποθηκεύστε σημειώσεις με ευκολία.</li>
                    <li>Επεξεργαστείτε και διαγράψτε σημειώσεις.</li>
                    <li>Έχετε πρόσβαση στις σημειώσεις σας οποιαδήποτε στιγμή.</li>
                  </ul>";
        }
        ?>
    </div>
    <footer>
        © 2025 Notes App. All rights reserved.
    </footer>
</body>
</html>
