<?php


$DB_HOST = "sql111.infinityfree.com";
$DB_USER = "**********";
$DB_PASS = "**********";
$DB_NAME = "**********";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
header("Content-Type: application/json; charset=utf8mb4");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "فشل الاتصال بقاعدة البيانات"]);
    exit;
}
$conn->set_charset("utf8mb4");

$id = (int)($_POST["id"] ?? 0);
if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "id غير صحيح"]);
    exit;
}

$stmt = $conn->prepare("SELECT watched FROM movies WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(["success" => false, "message" => "الفيلم غير موجود"]);
    exit;
}

$newStatus = $row["watched"] == 1 ? 0 : 1;

$update = $conn->prepare("UPDATE movies SET watched = ? WHERE id = ?");
$update->bind_param("ii", $newStatus, $id);
$update->execute();
$update->close();
$conn->close();

echo json_encode([
    "success" => true,
    "id" => $id,
    "watched" => $newStatus
]);
?>
