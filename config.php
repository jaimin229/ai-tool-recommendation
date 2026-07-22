<?php
// ── Phase 1: Global session guard ───────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Database connection ──────────────────────────────────────
$host     = "localhost";
$db_user  = "root";
$db_pass  = "";
$database = "ai_tool_portal";

$conn = new mysqli($host, $db_user, $db_pass, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
?>