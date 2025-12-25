<?php
/* Minimal embedded FPDF 1.8 wrapper (subset). For full features, replace with official library. */
class PDF {
    private $buffer='';
    function header($title){ $this->buffer = "%PDF-1.3\n"; $this->title=$title; }
    function text($content){ $this->content=$content; }
    function output(){
        // Very naive PDF generator for text only
        $text = stream_get_contents(fopen('php://memory','r+')); // placeholder
        $content = $this->content ?? '';
        $escaped = str_replace(['\\','(',')'],['\\\\','\\(','\\)'],$content);
        $objects = [];
        $objects[] = "1 0 obj<< /Type /Catalog /Pages 2 0 R >>endobj\n";
        $objects[] = "2 0 obj<< /Type /Pages /Kids [3 0 R] /Count 1 >>endobj\n";
        $stream = "BT /F1 12 Tf 72 760 Td (".$escaped.") Tj ET";
        $objects[] = "3 0 obj<< /Type /Page /Parent 2 0 R /Resources << /Font << /F1 4 0 R >> >> /MediaBox [0 0 595 842] /Contents 5 0 R >>endobj\n";
        $objects[] = "4 0 obj<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>endobj\n";
        $objects[] = "5 0 obj<< /Length ".strlen($stream)." >>stream\n$stream\nendstream endobj\n";
        $xrefPos = strlen($this->buffer);
        $offsets = [0];
        foreach ($objects as $obj){ $offsets[] = $xrefPos; $this->buffer .= $obj; $xrefPos = strlen($this->buffer); }
        $xrefStart = strlen($this->buffer);
        $this->buffer .= "xref\n0 ".(count($objects)+1)."\n0000000000 65535 f \n";
        for($i=1;$i<=count($objects);$i++){ $this->buffer .= sprintf("%010d 00000 n \n", $offsets[$i]); }
        $this->buffer .= "trailer<< /Size ".(count($objects)+1)." /Root 1 0 R >>\nstartxref\n$xrefStart\n%%EOF";
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="report.pdf"');
        echo $this->buffer;
    }
}
