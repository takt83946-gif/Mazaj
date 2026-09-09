<?php
include 'db.php';

$action = $_POST['action'] ?? '';

if ($action == 'add') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $desc = $_POST['desc'];

    $stmt = $conn->prepare("INSERT INTO products (name, category, price, desc_text) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $name, $category, $price, $desc);
    $stmt->execute();
    echo json_encode(["status" => "success"]);
} 
elseif ($action == 'edit') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $desc = $_POST['desc'];

    $stmt = $conn->prepare("UPDATE products SET name=?, category=?, price=?, desc_text=? WHERE id=?");
    $stmt->bind_param("ssdsi", $name, $category, $price, $desc, $id);
    $stmt->execute();
    echo json_encode(["status" => "updated"]);
} 
elseif ($action == 'delete') {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo json_encode(["status" => "deleted"]);
}

$conn->close();
?>
