
<?php
    $host =  'localhost';
    $db = 'appnotes_db';
    $user =  'root';
    $password = '';

 //Δημιουργία σύνδεσης με βάση δεδομένων
    $conn = new mysqli($host, $user, $password, $db);

//Έλεγχος σύνδεσης
    if ($conn->connect_error) {
        die("Η σύνδεση απέτυχε: " . $conn->connect_error);
    }
?>
