<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Barang</title>
    <!-- Tambahkan library QRCode.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #e5e7eb;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .label-container {
            width: 6cm;
            height: 4cm;
            background-color: white;
            border: 2px solid #000;
            box-sizing: border-box;
            padding: 4px;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            page-break-inside: avoid;
        }

        .label-text {
            width: 65%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: left;
            padding-right: 4px;
        }

        .label-qr {
            width: 35%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .line {
            width: 100%;
            margin: 2px 0;
            line-height: 1.1;
            word-wrap: break-word;
        }

        /* Baris 1: Nama Barang */
        .line-1 {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Baris 2: Instansi */
        .line-2 {
            font-size: 7px;
        }

        /* Baris 3: Kode Wilayah/Instansi */
        .line-3 {
            font-size: 7px;
        }

        /* Baris 4: Kode 108 + Register */
        .line-4 {
            font-size: 9px;
            font-weight: bold;
            margin-top: 4px;
            margin-bottom: 2px;
        }

        /* Baris 5: Dinas Kesehatan */
        .line-5 {
            font-size: 7px;
        }

        /* Pengaturan Cetak */
        @media print {
            body {
                background-color: white;
                align-items: flex-start;
                justify-content: flex-start;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            @page {
                margin: 0;
                size: 6cm 4cm;
            }
            .label-container {
                border: 1px solid #000;
            }
        }
    </style>
</head>
<body>

    <div class="no-print" style="position: absolute; top: 20px; text-align: center; width: 100%;">
        <p style="margin-bottom: 10px; font-size: 14px; font-weight: bold; color: #374151;">Pratinjau Cetak Label</p>

        <button onclick="window.print()" style="padding: 8px 16px; background-color: #4f46e5; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Cetak Label</button>
        <button onclick="window.close()" style="padding: 8px 16px; background-color: #e5e7eb; color: #374151; border: 1px solid #d1d5db; border-radius: 4px; cursor: pointer; font-weight: bold; margin-left: 8px;">Tutup</button>
    </div>

    <div class="label-container">
        <div class="label-text">
            <!-- Header: Logo & Instansi -->
            <div style="display: flex; align-items: center; margin-bottom: 3px; border-bottom: 1px solid #000; padding-bottom: 2px;">
                <img src="{{ asset('images/logo-lamongan.png') }}" alt="Logo" style="width: 14px; height: 16px; margin-right: 4px;">
                <div style="display: flex; flex-direction: column;">
                    <div class="line line-2" style="font-weight: bold;">PEMKAB LAMONGAN</div>
                    <div class="line line-5" style="font-size: 6px;">Dinas Kesehatan</div>
                </div>
            </div>

            <!-- Baris 1: Nama Barang -->
            <div class="line line-1" style="font-size: 9px;">{{ Str::limit($item->nama_barang, 30) }}</div>
            
            <!-- Baris 4: Kode Final + Register -->
            <div class="line line-4" style="margin-top: 2px;">{{ $item->kode_barang ?? '-' }}</div>

            <!-- Baris Tambahan: Tahun Pengadaan -->
            <div class="line" style="font-size: 7px; margin-top: 2px; font-weight: bold;">
                Pengadaan: <span id="tahun-cetak">{{ $item->tahun_pengadaan ?? date('Y') }}</span>
            </div>
            
            <!-- Baris 3: Hardcode Kode Instansi -->
            <div class="line line-3" style="font-size: 6px; margin-top: 1px;">13.17.07.03.12.00</div>
        </div>
        
        <div class="label-qr" id="qrcode"></div>
    </div>

    <script>
        // Generate QR Code
        document.addEventListener("DOMContentLoaded", function() {
            var url = "{{ url('/scan/' . $item->id) }}";
            new QRCode(document.getElementById("qrcode"), {
                text: url,
                width: 65,  // Ukuran proporsional dengan kotak 4cm
                height: 65,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.M
            });
        });
    </script>
</body>
</html>
