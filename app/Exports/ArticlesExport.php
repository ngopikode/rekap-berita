<?php

namespace App\Exports;

use App\Models\Article;
use App\Models\Publisher;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ArticlesExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $month;
    protected $year;

    // 1. Terima parameter dari pemanggilan export
    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function view(): View
    {
        // 2. Buat instance Carbon berdasarkan bulan dan tahun yang dipilih
        $targetDate = Carbon::create($this->year, $this->month, 1);

        $startOfMonth = $targetDate->copy()->startOfMonth();
        $endOfMonth = $targetDate->copy()->endOfMonth();
        $daysInMonth = $targetDate->daysInMonth;

        // Hanya ambil publisher milik user yang sedang login
        $publishers = Publisher::where('user_id', auth()->id())
            ->with(['articles' => function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('published_at', [$startOfMonth, $endOfMonth]);
            }])
            ->orderBy('name', 'asc')
            ->get();

        // Hitung Total Per Tanggal untuk Footer
        $userPublisherIds = $publishers->pluck('id');
        $totalPerDate = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            // Gunakan target date agar perhitungannya sesuai bulan pilihan
            $dateString = $targetDate->copy()->setDay($day)->toDateString();
            $totalPerDate[$day] = Article::whereIn('publisher_id', $userPublisherIds)
                ->whereDate('published_at', $dateString)
                ->count();
        }

        $grandTotal = Article::whereIn('publisher_id', $userPublisherIds)
            ->whereMonth('published_at', $this->month)
            ->whereYear('published_at', $this->year)
            ->count();

        return view('exports.articles', [
            'publishers' => $publishers,
            'daysInMonth' => $daysInMonth,
            'totalPerDate' => $totalPerDate,
            'grandTotal' => $grandTotal,
            'monthName' => $targetDate->translatedFormat('F'),
            'year' => $this->year
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // 3. Pastikan penentuan jumlah hari dan hari Minggu sesuai filter
        $targetDate = Carbon::create($this->year, $this->month, 1);
        $daysInMonth = $targetDate->daysInMonth;

        $lastColumnIndex = $daysInMonth + 3; // No + Nama + Days + Total
        $lastRowIndex = $sheet->getHighestRow();

        // Global Style (Font, Border, Alignment)
        $sheet->getStyle('A1:' . $this->getColumnLetter($lastColumnIndex) . $lastRowIndex)->applyFromArray([
            'font' => ['name' => 'Arial', 'size' => 10],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        // Header Title (Row 1)
        $sheet->mergeCells('A1:' . $this->getColumnLetter($lastColumnIndex) . '1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE0E0E0']],
        ]);

        // Header Columns (Row 2)
        $sheet->getStyle('A2:' . $this->getColumnLetter($lastColumnIndex) . '2')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFCCCCCC']],
        ]);

        // Nama Media Alignment (Left)
        if ($lastRowIndex > 3) {
            $sheet->getStyle('B3:B' . ($lastRowIndex - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }

        // Styling Hari Minggu (Merah) menyesuaikan bulan & tahun yang dipilih
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $targetDate->copy()->setDay($day);

            if ($date->isSunday()) {
                $colIndex = $day + 2; // A=1, B=2, C=3 (Day 1)
                $colLetter = $this->getColumnLetter($colIndex);

                // Header Minggu (Merah Tua, Teks Putih)
                $sheet->getStyle($colLetter . '2')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFF0000']],
                    'font' => ['color' => ['argb' => 'FFFFFFFF']],
                ]);

                // Kolom Minggu Body (Merah Muda)
                if ($lastRowIndex >= 3) {
                    $sheet->getStyle($colLetter . '3:' . $colLetter . $lastRowIndex)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFCCCC']],
                    ]);
                }
            }
        }

        // Footer Row (Total Per Tanggal)
        $sheet->getStyle('A' . $lastRowIndex . ':' . $this->getColumnLetter($lastColumnIndex) . $lastRowIndex)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFFFCC']],
        ]);

        // Merge Footer Label "TOTAL POSTINGAN..."
        $sheet->mergeCells('A' . $lastRowIndex . ':B' . $lastRowIndex);
        $sheet->getStyle('A' . $lastRowIndex)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }

    private function getColumnLetter($index)
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index);
    }
}
