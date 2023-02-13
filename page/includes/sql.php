<?php

function GetResultSetOfSingleColumn($column, $table) {
    $con = GetConnection();
    $sql = "SELECT DISTINCT " . $column . " FROM " . $table . ";";
    $resultset = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultset;
}

function GetArrayFromResultSet($resultset) {
    $array = array();
    while ($row = mysqli_fetch_row($resultset)) {
        array_push($array, $row[0]);
    }

    return $array;
}

function GetPublishers() {
    $resultset = GetResultSetOfSingleColumn("kiadonev", "konyvadatok.kiadok");
    return GetArrayFromResultSet($resultset);
}

function GetCoverTypes() {
    $resultset = GetResultSetOfSingleColumn("kotestipusnev", "konyvadatok.kotestipusok");
    return GetArrayFromResultSet($resultset);
}

function GetLanguages() {
    $resultset = GetResultSetOfSingleColumn("nyelvnev", "konyvadatok.nyelvek");
    return GetArrayFromResultSet($resultset);
}

function GetSerieses() {
    $resultset = GetResultSetOfSingleColumn("sorozatnev", "konyvadatok.konyvsorozatok");
    return GetArrayFromResultSet($resultset);
}

?>