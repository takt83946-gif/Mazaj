<?php
$host = "sql105.infinityfree.com";
$user = "if0_42865798";
$pass = "KYpzJYfYFxg"; 
$dbname = "if0_42865798_mazaj";

$conn = @new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    // يتجاهل الخطأ لكي لا يوقف تحميل تصميم الصفحة
} else {
    $conn->set_charset("utf8mb4");
}
?>
