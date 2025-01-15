<?php 
require '../../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$generate_reports_for = isset($_GET['full_details']) ? $_GET['full_details'].'.php' : false;
$archive_id = isset($_GET['archiveID']) ? $_GET['archiveID'] : false;

if ($archive_id && $generate_reports_for) {
    ob_start();
    include($generate_reports_for);
    $html = ob_get_clean();

    $dompdf->loadHtml($html);

    $dompdf->setPaper('A4', 'portrait');

    $dompdf->render();

    $dompdf->stream('EARIST Repository - '.$archive_id.".pdf", [
        "Attachment" => false,
        "Compress" => 1,
        "DeflateLevel" => 9
    ]);
} else {
    echo "No report type specified";
}
?>