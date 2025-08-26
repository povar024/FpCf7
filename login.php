<?php
session_start();
require_once 'db.php'; //Σύνδεση με τη βάση δεδομένων

//Δημιουργία CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

//Αρχικοποίηση μεταβλητών για τα μηνύματα
$error_message = '';

// Διαχείριση υποβολής φόρμας
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Έλεγχος αν το CSRF token είναι έγκυρο
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Μη έγκυρο CSRF token.");
    }

    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    //Έλεγχος αν ο χρήστης υπάρχει στη βάση
    $sql = "SELECT id, password_hash FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Σφάλμα κατά την προετοιμασία του query: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        //Επαλήθευση κωδικού πρόσβασης
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: notes.php");
            exit();
        } else {
            $error_message = "Λάθος κωδικός πρόσβασης.";
        }
    } else {
        $error_message = "Ο χρήστης δεν βρέθηκε.";
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Σύνδεση</title>
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
    </style>
</head>
<body>
    <div class="container login-section">
        <h2>Σύνδεση</h2>

        <?php if ($error_message): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label for="username">Όνομα Χρήστη:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Κωδικός Πρόσβασης:</label>
            <input type="password" id="password" name="password" required>

            <!--CSRF Token-->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <button type="submit">Σύνδεση</button>
        </form>
    </div>
</body>
</html>
