<?php
require 'vendor/autoload.php';

// Konfigurasi Google Sheets
define('GOOGLE_SHEETS_CREDENTIALS_PATH', 'credentials.json');
define('SPREADSHEET_ID', 'MASUKKAN_SPREADSHEET_ID_ANDA');

// Konfigurasi Toko
$CONFIG_TOKO = [
    'nama' => 'Nama Toko Anda',
    'alamat' => 'Alamat Toko',
    'telepon' => '08123456789',
    'npwp' => '12.345.678.9-123.000'
];

// Proses penyimpanan ke Google Sheets
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $client = new Google_Client();
        $client->setAuthConfig(GOOGLE_SHEETS_CREDENTIALS_PATH);
        $client->setScopes([Google\Service\Sheets::SPREADSHEETS]);
        $service = new Google\Service\Sheets($client);
        
        $values = [
            [
                $_POST['no_invoice'],
                $_POST['tanggal'],
                $_POST['pelanggan'],
                $_POST['alamat'],
                json_encode($_POST['items']),
                json_encode($_POST['qty']),
                json_encode($_POST['price'])
            ]
        ];

        $body = new Google\Service\Sheets\ValueRange(['values' => $values]);
        $result = $service->spreadsheets_values->append(
            SPREADSHEET_ID,
            'Invoice!A:Z',
            $body,
            ['valueInputOption' => 'RAW']
        );
        
        // Tambahkan debug info
        $response = [
            'success' => true,
            'debug' => [
                'values' => $values,
                'spreadsheet_id' => SPREADSHEET_ID,
                'result' => $result
            ]
        ];
        
        echo json_encode($response);
    } catch (Exception $e) {
        // Tampilkan error jika ada
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generator Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .invoice-form { max-width: 800px; margin: 20px auto; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="invoice-form">
            <h2 class="mb-4">Buat Invoice</h2>
            
            <form id="invoiceForm" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>No Invoice</label>
                            <input type="text" class="form-control" name="no_invoice" required>
                        </div>
                        <div class="mb-3">
                            <label>Tanggal</label>
                            <input type="date" class="form-control" name="tanggal" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Nama Pelanggan</label>
                            <input type="text" class="form-control" name="pelanggan" required>
                        </div>
                        <div class="mb-3">
                            <label>Alamat</label>
                            <textarea class="form-control" name="alamat"></textarea>
                        </div>
                    </div>
                </div>

                <div class="items-container">
                    <!-- Item rows will be added here -->
                </div>

                <button type="button" class="btn btn-info no-print" onclick="addItem()">+ Tambah Item</button>
                <button type="submit" class="btn btn-primary no-print">Simpan & Cetak</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    function addItem() {
        const itemRow = `
            <div class="row item-row mb-2">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="items[]" placeholder="Nama Item" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control qty" name="qty[]" placeholder="Qty" required>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control price" name="price[]" placeholder="Harga" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">Hapus</button>
                </div>
            </div>
        `;
        $('.items-container').append(itemRow);
    }

    function removeItem(btn) {
        $(btn).closest('.item-row').remove();
    }

    $('#invoiceForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'save_invoice.php',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                window.print();
            }
        });
    });
    </script>
</body>
</html> 