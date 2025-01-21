<?php
require_once('tcpdf/tcpdf.php');

// Create new PDF document
$pdf = new TCPDF();
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 12);

// Add content to PDF
$html = "
    <h2 style='text-align: center;'>Balay.com Booking Receipt</h2>
    <p><strong>Tenant Name:</strong> $tenantName</p>
    <p><strong>Room ID:</strong> $roomId</p>
    <p><strong>Room Name:</strong> $roomName</p>
    <p><strong>Establishment ID:</strong> $establishmentId</p>
    <p><strong>Establishment Name:</strong> $establishmentName</p>
    <p><strong>Amount Paid:</strong> $pricePaid</p>
    <p><strong>Reserved Date:</strong> $reservedDate</p>
    <
";

$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('booking_receipt.pdf', 'D');
