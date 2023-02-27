<?php
print_r($_SESSION["user"]);
echo $_SESSION["user"]["id"];

if (!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]) { ?>
<script>
    window.location.href = window.location.origin;
</script>
<?php } ?>



<form action="/signout">
    <input type="submit" value="Kijelentkezés">
</form>