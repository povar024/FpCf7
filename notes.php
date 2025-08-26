<?php
session_start();
require_once 'db.php'; //Σύνδεση με τη βάση δεδομένων

//Έλεγχος αν ο χρήστης είναι συνδεδεμένος
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

//Δημιουργία CSRF token 
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

//Επεξεργασία σημείωσης
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_note'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Μη έγκυρο CSRF token.");
    }
    // Έλεγχος CSRF
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Μη έγκυρο αίτημα.");
    }

    $note_id = $_POST['note_id'];
    $new_content = htmlspecialchars($_POST['content']); // Καθαρισμός περιεχομένου
    $user_id = $_SESSION['user_id'];

    //Ενημέρωση σημείωσης
    $stmt = $conn->prepare("UPDATE notes SET content = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $new_content, $note_id, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: notes.php");
    exit();
}

//Διαγραφή σημείωσης
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
    if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Μη έγκυρο CSRF token.");
    }

    $note_id = $_GET['delete_id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $note_id, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: notes.php");
    exit();
}

//Δημιουργία νέας σημείωσης
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_note'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Μη έγκυρο CSRF token.");
    }

    $content = htmlspecialchars($_POST['note']); // Καθαρισμός περιεχομένου
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO notes (content, user_id) VALUES (?, ?)");
    $stmt->bind_param("si", $content, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: notes.php");
    exit();
}

//Ανάκτηση σημειώσεων του χρήστη
$stmt = $conn->prepare("SELECT id, content FROM notes WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Οι Σημειώσεις σας</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* CSS Styling */

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

        .note-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .note-form button {
            background-color: #2c2c2c;
            color: #f4f4f4;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .note-form button:hover {
            background-color: burlywood;
            color: #000;
        }

        .notes-list li {
            margin-bottom: 20px;
            background: rgba(255, 255, 255, 0.8);
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .notes-list textarea {
            width: 100%;
            margin-bottom: 10px;
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
        <a href="notes.php"><i class="fas fa-sticky-note"></i> Οι Σημειώσεις</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Έξοδος</a>
    </div>
    <div class="main-content">
        <h1>Οι Σημειώσεις σας</h1>

        <!--Δημιουργία νέας σημείωσης -->
        <div class="note-form">
            <form method="POST" action="notes.php">
                <textarea name="note" required></textarea>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <button type="submit" name="save_note">Αποθήκευση Σημείωσης</button>
            </form>
        </div>

        <h2>Προβολή σημειώσεων</h2>
        <ul class="notes-list">
            <?php while ($row = $result->fetch_assoc()): ?>
                <li>
                    <form method="POST" action="notes.php">
                        <textarea name="content" required><?php echo htmlspecialchars($row['content']); ?></textarea>
                        <input type="hidden" name="note_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <button type="submit" name="edit_note">Επεξεργασία</button>
                    </form>
                    <form method="GET" action="notes.php" style="display:inline;">
                        <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <button type="submit">Διαγραφή</button>
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
    <footer>
        © 2025 Notes App. All rights reserved.
    </footer>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
