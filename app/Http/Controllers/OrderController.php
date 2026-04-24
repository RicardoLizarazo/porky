<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function invoice(Order $order)
    {
        $order->load(['customer','details','status','type','delivery']);

        return view('orders.invoice', compact('order'));
    }

    public function invoicePdf(Order $order)
    {
        $order->load(['customer','details','status','type','delivery']);

        $pdf = Pdf::loadView('orders.invoice-pdf', compact('order'))
            ->setPaper('A4');

        return $pdf->stream("factura_{$order->id}.pdf");
    }

    public function ticket(Order $order)
    {
        $order->load(['customer','details']);

        return view('orders.ticket', compact('order'));
    }
}
