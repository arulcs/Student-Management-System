<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="students.csv"');
$out = fopen('php://output', 'w');
fputcsv($out, ['ID','Name','Email','Roll','Class']);
$stmt = $pdo->query('SELECT s.id, u.name, u.email, s.roll_no, s.class FROM students s LEFT JOIN users u ON s.user_id=u.id ORDER BY s.id');
while($r=$stmt->fetch()) { fputcsv($out, $r); }
fclose($out);
