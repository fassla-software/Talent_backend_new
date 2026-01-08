<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;

class DistributorCouponExport implements FromCollection, WithHeadings, WithTitle, WithDrawings
{
    protected $coupons;
    protected $drawings = [];

    public function __construct($coupons)
    {
        $this->coupons = $coupons;
    }

    public function collection()
    {
        return $this->coupons->map(function ($coupon, $index) {
            // Generate QR code
            $qrCode = new QrCode($coupon->code, errorCorrectionLevel: ErrorCorrectionLevel::High, size: 100);
            $writer = new PngWriter();
            $qrResult = $writer->write($qrCode);
            $qrPath = storage_path('app/temp_qr_' . $coupon->id . '.png');
            file_put_contents($qrPath, $qrResult->getString());

            // Create drawing for QR code
            $drawing = new Drawing();
            $drawing->setName('QR_' . $coupon->id);
            $drawing->setDescription('QR Code for ' . $coupon->code);
            $drawing->setPath($qrPath);
            $drawing->setHeight(100);
            $drawing->setWidth(100);
            $drawing->setCoordinates('G' . ($index + 2)); // Column G, starting from row 2 (after header)

            $this->drawings[] = $drawing;

            return [
                'Code' => $coupon->code,
                'Sales Value' => number_format($coupon->sales_value, 2),
                'Status' => ucfirst($coupon->status),
                'Area' => $coupon->area_name,
                'Expires At' => $coupon->expired_at ? \Carbon\Carbon::parse($coupon->expired_at)->format('Y-m-d') : '',
                'Used At' => $coupon->used_at ? \Carbon\Carbon::parse($coupon->used_at)->format('Y-m-d H:i:s') : 'Not used',
                'QR Code' => '', // Placeholder, image will be embedded via drawing
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Code',
            'Sales Value',
            'Status',
            'Area',
            'Expires At',
            'Used At',
            'QR Code',
        ];
    }

    public function title(): string
    {
        return 'Distributor Coupons';
    }

    public function drawings()
    {
        return $this->drawings;
    }

    public function __destruct()
    {
        // Clean up temporary QR files
        foreach ($this->coupons as $coupon) {
            $qrPath = storage_path('app/temp_qr_' . $coupon->id . '.png');
            if (file_exists($qrPath)) {
                unlink($qrPath);
            }
        }
    }
}
