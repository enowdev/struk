<?php
require 'config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generator Struk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .struk-container { max-width: 380px; margin: 20px auto; }
        @media print {
            body { width: 58mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="struk-container">
        <form id="strukForm" method="POST">
            <div class="mb-3">
                <label>Kasir</label>
                <input type="text" class="form-control" name="kasir" required>
            </div>
            
            <div class="mb-3">
                <label>Pelanggan</label>
                <input type="text" class="form-control" name="pelanggan">
            </div>

            <div class="items-container">
                <!-- Item rows will be added here -->
            </div>

            <div class="mb-3">
                <label>Metode Pembayaran</label>
                <select class="form-control" name="payment_method">
                    <option value="cash">Cash</option>
                    <option value="debit">Kartu Debit</option>
                    <option value="credit">Kartu Kredit</option>
                    <option value="qris">QRIS</option>
                    <option value="transfer">Transfer</option>
                </select>
            </div>

            <button type="button" class="btn btn-info no-print" onclick="addItem()">+ Tambah Item</button>
            <button type="submit" class="btn btn-primary no-print">Cetak Struk</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    // Simpan data toko di localStorage
    const tokoData = <?php echo json_encode($CONFIG_TOKO); ?>;
    localStorage.setItem('configToko', JSON.stringify(tokoData));

    function addItem() {
        const itemRow = `
            <div class="row item-row mb-2">
                <div class="col-6">
                    <input type="text" class="form-control" name="items[]" placeholder="Item" required>
                </div>
                <div class="col-2">
                    <input type="number" class="form-control qty" name="qty[]" placeholder="Qty" required>
                </div>
                <div class="col-3">
                    <input type="number" class="form-control price" name="price[]" placeholder="Harga" required>
                </div>
                <div class="col-1">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">×</button>
                </div>
            </div>
        `;
        $('.items-container').append(itemRow);
    }

    function removeItem(btn) {
        $(btn).closest('.item-row').remove();
    }

    $('#strukForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'save_struk.php',
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