<?php 
require '../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$generate_reports_for = isset($_GET['generate_report_for']) ? $_GET['generate_report_for'].'.php' : false;

if ($generate_reports_for) {
    ob_start();
    include($generate_reports_for);
    $html = ob_get_clean();

    $dompdf->loadHtml($html);

    $dompdf->setPaper('A4', 'portrait');

    $dompdf->render();

    $dompdf->stream('EARIST Repository - '.ucwords($_GET['generate_report_for']).".pdf", [
        "Attachment" => false,
        "Compress" => 1,
        "DeflateLevel" => 9
    ]);
} else {
    echo "No report type specified";
}
?>