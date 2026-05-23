<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Aset</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
        }

        .label-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, 3.21in);
            gap: 15px;
            justify-content: center;
        }

        .stiker-label {
            background: white;
            border: 2.5px solid #000;
            border-radius: 12px;
            width: 3.21in;
            height: 1.45in;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-sizing: border-box;
            page-break-inside: avoid;
            font-family: Arial, sans-serif;
            overflow: hidden;
            color: #000;
        }

        .label-header {
            text-align: center;
            font-size: 8pt;
            font-weight: bold;
            padding: 5px 0 3px 0;
            border-bottom: 1px dashed #000;
        }

        .label-body {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 10px;
            flex: 1;
        }

        .label-logo {
            width: 38px;
            height: auto;
        }

        .label-text {
            flex: 1;
            padding: 0 10px;
            text-align: left;
            line-height: 1.3;
        }

        .label-text .kategori {
            font-size: 9pt;
            font-weight: bold;
            color: #333;
        }

        .label-text .kode {
            font-size: 10pt;
            font-weight: bold;
            margin-top: 2px;
        }

        .label-text .tahun {
            font-size: 9pt;
            font-weight: bold;
            margin-top: 2px;
            color: #333;
        }

        .label-qr {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .label-footer {
            text-align: center;
            font-size: 7.5pt;
            font-weight: bold;
            padding: 3px 0 5px 0;
            border-top: 1px dashed #000;
            text-transform: uppercase;
        }

        /* Styling khusus saat diprint ke kertas */
        @media print {
            body {
                background-color: white;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .stiker-label {
                box-shadow: none;
                border: 2.5px solid #000;
                border-radius: 12px;
            }
            @page { 
                size: A4;
                margin: 10mm; 
            }
        }
    </style>
</head>
<body>
    <div class="no-print max-w-4xl mx-auto mb-6 flex justify-between items-center bg-white p-4 rounded-lg shadow-sm border border-gray-200">
        <div>
            <h1 class="text-lg font-bold text-gray-800">Pratinjau Cetak Label</h1>
            <p class="text-sm text-gray-500">Menampilkan {{ count($assets) }} label aset siap cetak.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.close()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition-colors">Tutup</button>
            <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Sekarang
            </button>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="label-grid">
            @foreach($assets as $asset)
            <div class="stiker-label">
                <div class="label-header">PEMERINTAH KABUPATEN LAMONGAN</div>
                <div class="label-body">
                    <img src="{{ asset('images/logo-lamongan.png') }}" class="label-logo" alt="Logo">
                    <div class="label-text">
                        <div class="kategori">13.17.07.03.12.00</div>
                        <div class="kode">{{ $asset->asset_code }}</div>
                        <div class="tahun">{{ $asset->year_purchased }}</div>
                    </div>
                    <div class="label-qr">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(45)->margin(0)->generate(url('/item/' . $asset->asset_code)) !!}
                    </div>
                </div>
                <div class="label-footer">PUSKESMAS MANTUP - DINAS KESEHATAN</div>
            </div>
            @endforeach
        </div>
    </div>

    <script>
        // Otomatis membuka dialog print saat halaman dimuat
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });

        // Kosongkan antrean setelah print selesai
        window.addEventListener('afterprint', function() {
            if(confirm('Apakah label berhasil dicetak? Jika Ya, antrean cetak akan dikosongkan.')) {
                fetch('{{ route('aset.print-queue.clear') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(() => {
                    if(window.opener) {
                        window.opener.dispatchEvent(new Event('focus')); // trigger Alpine window focus listener
                    }
                    window.close();
                });
            }
        });
    </script>
</body>
</html>
