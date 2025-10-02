<?php
require 'vendor/autoload.php';
use PhpOffice\PhpWord\TemplateProcessor;

$template = new TemplateProcessor('Absensi IP(P)NU Terik.docx');

$variables = $template->getVariables();

echo "<h3>Daftar Placeholder dalam Template:</h3><ul>";
foreach ($variables as $var) {
    echo "<li>{{" . htmlspecialchars($var) . "}}</li>";
}
echo "</ul>";
?>
