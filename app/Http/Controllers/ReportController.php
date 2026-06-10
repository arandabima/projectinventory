<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ReportExport;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('reports.index', [
            'exports' => ReportExport::latest()->take(10)->get(),
            'stockValue' => Item::sum('current_stock'),
            'movementCount' => StockMovement::count(),
            'lowStockCount' => Item::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            'orderCount' => Order::count(),
            'pendingPaymentCount' => Payment::where('status', Payment::STATUS_PENDING)->count(),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $data = $request->validate([
            'report_type' => ['required', 'in:stock,movement,orders,payments'],
            'period_start' => ['nullable', 'date'],
            'period_end' => ['nullable', 'date', 'after_or_equal:period_start'],
            'generated_by' => ['nullable', 'string', 'max:120'],
        ]);

        $fileName = $data['report_type'].'-report-'.now()->format('Ymd-His').'.csv';

        ReportExport::create([
            'report_type' => $data['report_type'],
            'period_start' => $data['period_start'] ?? null,
            'period_end' => $data['period_end'] ?? null,
            'generated_by' => $data['generated_by'] ?? 'operator',
            'file_name' => $fileName,
        ]);

        return Response::streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');

            if ($data['report_type'] === 'stock') {
                fputcsv($handle, ['SKU', 'Nama Barang', 'Kategori', 'Stok', 'Minimum', 'Satuan', 'Lokasi', 'Status']);
                Item::with('category')->orderBy('name')->chunk(100, function ($items) use ($handle) {
                    foreach ($items as $item) {
                        fputcsv($handle, [$item->sku, $item->name, $item->category?->name, $item->current_stock, $item->minimum_stock, $item->unit, $item->location, $item->status]);
                    }
                });
            } else {
                if ($data['report_type'] === 'orders') {
                    fputcsv($handle, ['Tanggal', 'Nomor Order', 'Supplier', 'Penerima', 'Status Order', 'Status Bayar', 'Total', 'Dibuat Oleh']);
                    $query = Order::with('payment', 'createdBy')->latest();

                    if (! empty($data['period_start'])) {
                        $query->whereDate('created_at', '>=', $data['period_start']);
                    }

                    if (! empty($data['period_end'])) {
                        $query->whereDate('created_at', '<=', $data['period_end']);
                    }

                    $query->chunk(100, function ($orders) use ($handle) {
                        foreach ($orders as $order) {
                            fputcsv($handle, [
                                $order->created_at->format('Y-m-d H:i:s'),
                                $order->order_number,
                                $order->supplier_name,
                                $order->recipient_name,
                                $order->status,
                                $order->payment?->status,
                                $order->total_amount,
                                $order->createdBy?->username ?? $order->createdBy?->name,
                            ]);
                        }
                    });

                    fclose($handle);
                    return;
                }

                if ($data['report_type'] === 'payments') {
                    fputcsv($handle, ['Tanggal', 'Nomor Order', 'Reference', 'Transaction ID', 'Metode', 'Status', 'Amount', 'Paid At']);
                    $query = Payment::with('order')->latest();

                    if (! empty($data['period_start'])) {
                        $query->whereDate('created_at', '>=', $data['period_start']);
                    }

                    if (! empty($data['period_end'])) {
                        $query->whereDate('created_at', '<=', $data['period_end']);
                    }

                    $query->chunk(100, function ($payments) use ($handle) {
                        foreach ($payments as $payment) {
                            fputcsv($handle, [
                                $payment->created_at->format('Y-m-d H:i:s'),
                                $payment->order?->order_number,
                                $payment->doku_reference_number,
                                $payment->doku_transaction_id,
                                $payment->payment_method,
                                $payment->status,
                                $payment->amount,
                                $payment->paid_at?->format('Y-m-d H:i:s'),
                            ]);
                        }
                    });

                    fclose($handle);
                    return;
                }

                fputcsv($handle, ['Tanggal', 'SKU', 'Nama Barang', 'Jenis', 'Jumlah', 'Sebelum', 'Sesudah', 'Dicatat Oleh', 'Catatan']);
                $query = StockMovement::with('item')->latest();

                if (! empty($data['period_start'])) {
                    $query->whereDate('created_at', '>=', $data['period_start']);
                }

                if (! empty($data['period_end'])) {
                    $query->whereDate('created_at', '<=', $data['period_end']);
                }

                $query->chunk(100, function ($movements) use ($handle) {
                    foreach ($movements as $movement) {
                        fputcsv($handle, [$movement->created_at->format('Y-m-d H:i:s'), $movement->item?->sku, $movement->item?->name, $movement->type, $movement->quantity, $movement->stock_before, $movement->stock_after, $movement->recorded_by, $movement->notes]);
                    }
                });
            }

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }
}
