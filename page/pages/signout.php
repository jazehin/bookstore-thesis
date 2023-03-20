<?php
    session_start();
    unset($_SESSION["user"]);
    $_SESSION["logged_in"] = false;
?>

<script>
    window.location.href = window.location.origin;
</script>