<?php
$conn = mysqli_connect('localhost', 'root', '', 'content-center');
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}