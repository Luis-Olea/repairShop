<?php
require_once __DIR__ . '/vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf();
// Ejecuta el código PHP para generar el contenido HTML
ob_start();
include 'templateTicket.php'; // Aquí debes poner la ruta al archivo que contiene el código PHP
$html = ob_get_clean();
// Agrega el código CSS desde un archivo externo
$stylesheet = file_get_contents('stylesheet/bill.css');
$mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
// Agrega el contenido HTML al archivo PDF
$mpdf->WriteHTML($html);
// Genera y muestra el archivo PDF
$mpdf->Output('Factura.pdf', 'I');
?>