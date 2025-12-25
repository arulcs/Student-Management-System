<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/fpdf.php';

$stmt = $pdo->query('SELECT s.id, u.name, u.email, s.roll_no, s.class FROM students s LEFT JOIN users u ON s.user_id=u.id ORDER BY s.id');
$rows = $stmt->fetchAll();
$lines = ["Students Report", str_repeat('=', 30), ''];
foreach($rows as $r){ $lines[] = sprintf('#%d %s | %s | Roll:%s | Class:%s',$r['id'],$r['name'],$r['email'],$r['roll_no'],$r['class']); }
$pdf = new PDF();
$pdf->header('Students Report');
$pdf->text(implode("\n", $lines));
$pdf->output();
