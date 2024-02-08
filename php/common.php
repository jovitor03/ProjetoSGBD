<?php

global $current_page;
$current_page = get_site_url().'/'.basename(get_permalink());

function get_enum_values($connection, $table, $column)
{
    $query = " SHOW COLUMNS FROM `$table` LIKE '$column' ";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_array($result, MYSQLI_NUM);
    #extract the values
    #the values are enclosed in single quotes
    #and separated by commas
    $regex = "/'(.*?)'/";
    preg_match_all($regex, $row[1], $enum_array);
    $enum_fields = $enum_array[1];
    return ($enum_fields);
}

function get_back()
{
    echo "<script type='text/javascript'>document.write(\"<a href='javascript:history.back()' class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>\");</script>
    <noscript>
    <a href='".$_SERVER['HTTP_REFERER']."‘ class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>
    </noscript>";
}

function connect()
{
    $servername = "localhost";
    $username = "root";
    $password = "sgbdE1";
    $dbName = "bitnami_wordpress";

// Create connection
    $connection = new mysqli($servername, $username, $password, $dbName);

// Check connection
    if ($connection->connect_error) {
        die("Database connection failed: " . $connection->connect_error);
    }
}

function validateDate($date, $format = 'Y-m-d'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

?>