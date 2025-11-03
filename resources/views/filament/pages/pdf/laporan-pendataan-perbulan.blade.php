<div class="header" style="text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px;">
    <h1 style="margin: 0; font-size: 24px; font-weight: bold;">Laporan Pendataan IMUT Perbulan</h1>
    <p style="margin: 5px 0;"><strong>Unit:</strong> {{ $unit }}</p>
    <p style="margin: 5px 0;"><strong>Kategori:</strong> {{ $category }}</p>
    <p style="margin: 5px 0;"><strong>Periode:</strong> {{ $bulanNama[$bulan] ?? '' }} {{ $tahun }}</p>
    <p style="margin: 5px 0;"><strong>Tanggal Cetak:</strong> {{ now()->format('d F Y H:i:s') }}</p>
</div>

<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th style="width: 50px; border: 1px solid #ddd; padding: 10px; text-align: center; font-weight: bold;">No</th>
            <th style="border: 1px solid #ddd; padding: 10px; text-align: left; font-weight: bold;">Indikator Mutu</th>
            <th style="width: 150px; border: 1px solid #ddd; padding: 10px; text-align: left; font-weight: bold;">Unit</th>
            <th style="width: 120px; border: 1px solid #ddd; padding: 10px; text-align: left; font-weight: bold;">Tipe</th>
            <th style="width: 150px; border: 1px solid #ddd; padding: 10px; text-align: center; font-weight: bold;">Total Pendataan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($indicators as $index => $indicator)
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $indicator['title'] }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $indicator['area'] }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $indicator['type'] ?? 'N/A' }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                    <strong>{{ $indicator['total_pendataan'] }}</strong>
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background-color: #f9f9f9; font-weight: bold;">
            <td colspan="4" style="border: 1px solid #ddd; padding: 10px; text-align: right;">Total Indikator:</td>
            <td style="border: 1px solid #ddd; padding: 10px; text-align: center;">{{ count($indicators) }}</td>
        </tr>
    </tfoot>
</table>

