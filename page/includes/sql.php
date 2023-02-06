<?php

function proba() {
    $con = GetConnection();
    $sql = "SELECT * FROM probadb.probatabla;";
    $res = mysqli_query($con, $sql);
    mysqli_close($con);
    return $res;
}

?>