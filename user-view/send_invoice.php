<?php
require '../vendor/autoload.php'; // PHPMailer autoload
include '../config.php'; // Database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendInvoice($email, $username, $orderDetails, $totalAmountWithShipping, $pesananID, $shippingCost) {
    $mail = new PHPMailer(true);
    try {
        // Konfigurasi SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com'; // Ganti dengan SMTP server Anda
        $mail->SMTPAuth = true;
        $mail->Username = 'rian.kenji727@gmail.com'; // Ganti dengan email Anda
        $mail->Password = 'darkpride77'; // Ganti dengan password email Anda
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Pengirim
        $mail->setFrom('rian.kenji727@gmail.com', 'FreshDay 1.0');

        // Penerima
        $mail->addAddress($email, $username);

        // Konten email
        $mail->isHTML(true);
        $mail->Subject = "Invoice Pesanan Anda - $pesananID";

        // Tabel detail pesanan
        $orderDetailsHtml = '';
        foreach ($orderDetails as $order) {
            $orderDetailsHtml .= "
                <tr>
                    <td><img src='../img/" . htmlspecialchars($order['gambar']) . "' alt='Menu Image' style='width:50px;height:50px;object-fit:cover;'></td>
                    <td>" . htmlspecialchars($order['namaMenu']) . "</td>
                    <td>" . htmlspecialchars($order['jumlah']) . "</td>
                    <td>RP " . htmlspecialchars(number_format($order['totalHarga'], 0, ',', '.')) . "</td>
                </tr>";
        }

        // Isi email
        $mail->Body = "
            <h2>Terima kasih atas pesanan Anda, $username!</h2>
            <p>Berikut adalah detail pesanan Anda:</p>
            <table border='1' cellpadding='10' cellspacing='0' style='width:100%;border-collapse:collapse;'>
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Nama Menu</th>
                        <th>Jumlah</th>
                        <th>Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    $orderDetailsHtml
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan='3' style='text-align:right;'>Subtotal</td>
                        <td>RP " . number_format($totalAmountWithShipping - $shippingCost, 0, ',', '.') . "</td>
                    </tr>
                    <tr>
                        <td colspan='3' style='text-align:right;'>Ongkir</td>
                        <td>RP " . number_format($shippingCost, 0, ',', '.') . "</td>
                    </tr>
                    <tr>
                        <td colspan='3' style='text-align:right;'><strong>Total</strong></td>
                        <td><strong>RP " . number_format($totalAmountWithShipping, 0, ',', '.') . "</strong></td>
                    </tr>
                </tfoot>
            </table>
            <p>Pesanan Anda akan segera diproses. Terima kasih telah berbelanja di toko kami!</p>
        ";

        // Kirim email
        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Gagal mengirim invoice: {$mail->ErrorInfo}";
    }
}
?>
