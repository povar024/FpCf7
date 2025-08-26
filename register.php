<?php
    session_start();
    require_once 'db.php'; //Σύνδεση με τη βάση δεδομένων

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Δημιουργία CSRF token 
    }

//Αρχικοποίηση μεταβλητών για τα μηνύματα
$error_message = '';
$success_message = '';

//Έλεγχος αν το CSRF token είναι έγκυρο κατά την υποβολή της φόρμας
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Μη έγκυρο CSRF token.");
    }

    //Λήψη δεδομένων από τη φόρμα
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];

    //Έλεγχος αν οι κωδικοί ταιριάζουν
    if ($password !== $confirmPassword) {
        $error_message = "Οι κωδικοί πρόσβασης δεν ταιριάζουν.";
    } else {
        //Κρυπτογράφηση του κωδικού πρόσβασης
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        //Έλεγχος αν υπάρχει ήδη ο χρήστης στη βάση
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = "Αυτό το όνομα χρήστη είναι ήδη κατειλημμένο.";
        } else {
            // Εισαγωγή νέου χρήστη στη βάση δεδομένων
            $stmt = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashedPassword);

            if ($stmt->execute()) {
                $success_message = "Η εγγραφή ολοκληρώθηκε επιτυχώς! Μπορείτε να συνδεθείτε.";
                header("Location: login.php"); //Ανακατεύθυνση στην σελίδα σύνδεσης μετά την εγγραφή
                exit();
            } else {
                $error_message = "Σφάλμα κατά την εγγραφή: " . $stmt->error;
            }
        }
        //Κλείσιμο σύνδεσης
        $stmt->close();
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Εγγραφή</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>

        body {
            font-family: Arial, sans-serif;
            background-image: url('https://images.pexels.com/photos/3944422/pexels-photo-3944422.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: burlywood;
            color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #d2a679;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .success {
            color: green;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
    </style>
</head>
<body>
    <div class="container">
        <h2>Εγγραφή</h2>

        <?php if ($error_message): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <label for="username">Όνομα Χρήστη:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Κωδικός Πρόσβασης:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm-password">Επιβεβαίωση Κωδικού:</label>
            <input type="password" id="confirm-password" name="confirm-password" required>

            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <button type="submit">Εγγραφή</button>
        </form>
    </div>
</body>
</html>
