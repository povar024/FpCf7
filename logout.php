<?php
    session_start();
    
    //Καταστροφή όλων των δεδομένων της συνεδρίας
    session_unset();
    session_destroy();
    
    //Ανακατεύθυνση στη σελίδα σύνδεσης
    header("Location: login.php");
    exit();
?>
