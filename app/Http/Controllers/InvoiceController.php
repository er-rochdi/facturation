<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function streamPdf(\App\Models\Invoice $invoice)
    {
        $invoice->load(['client', 'company', 'items']);
        
        $client = $invoice->client;
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.pdf', compact('invoice', 'client'));
        
        return $pdf->stream('facture-' . $invoice->id . '.pdf');
    }
}
