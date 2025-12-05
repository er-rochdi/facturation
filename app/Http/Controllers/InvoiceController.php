<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function streamPdf(\App\Models\Invoice $invoice)
    {
        $invoice->load(['client', 'company', 'items']);
        
        // If invoice has no company, use the first company as default
        if (!$invoice->company) {
            $invoice->setRelation('company', \App\Models\Company::first());
        }
        
        $client = $invoice->client;
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.pdf', compact('invoice', 'client'));
        
        return $pdf->stream('facture-' . $invoice->id . '.pdf');
    }
}
