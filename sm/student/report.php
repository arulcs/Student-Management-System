<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('student');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/fpdf.php';
$user = current_user();

$stmt = $pdo->prepare('SELECT s.*, u.name, u.email FROM students s LEFT JOIN users u ON s.user_id=u.id WHERE s.user_id=?');
$stmt->execute([$user['id']]);
$stu = $stmt->fetch();

$stmt = $pdo->prepare('SELECT a.att_date, a.status, sub.name subject FROM attendance a JOIN subjects sub ON a.subject_id=sub.id WHERE a.student_id=? ORDER BY a.att_date');
$stmt->execute([$stu['id']]);
$attendance = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT m.exam, sub.name subject, m.max_marks, m.obtained_marks, m.exam_date FROM marks m JOIN subjects sub ON m.subject_id=sub.id WHERE m.student_id=? ORDER BY sub.name, m.exam');
$stmt->execute([$stu['id']]);
$marks = $stmt->fetchAll();

$lines=[];
$lines[]='Student Report';
$lines[]=str_repeat('=',30);
$lines[]='Name: '.$stu['name'].' | Email: '.$stu['email'];
$lines[]='Roll: '.$stu['roll_no'].' | Class: '.$stu['class'];
$lines[]='';
$lines[]='Attendance:';
foreach($attendance as $a){ $lines[]=$a['att_date'].' - '.$a['subject'].' - '.ucfirst($a['status']); }
$lines[]='';
$lines[]='Marks:';
foreach($marks as $m){ $lines[]=$m['subject'].' - '.$m['exam'].' : '.$m['obtained_marks'].'/'.$m['max_marks'].' ('.($m['exam_date']?:'').')'; }
$pdf = new PDF();
$pdf->header('Student Report');
$pdf->text(implode("\n", $lines));
$pdf->output();
