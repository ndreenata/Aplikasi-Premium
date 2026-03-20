<?php
require_once __DIR__ . '/../includes/koneksi.php';
session_destroy();
header('Location: ' . BASE_URL . '/index.php');
exit;
