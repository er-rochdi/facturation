<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $invoice->invoice_number ?? '#' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px 40px;
            color: #000;
            font-size: 14px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo {
            max-height: 100px;
            margin-bottom: 10px;
        }

        .date {
            text-align: right;
            margin-bottom: 30px;
        }

        .invoice-title-wrapper {
            text-align: center;
            margin-bottom: 30px;
        }

        .invoice-title {
            display: inline-block;
            border: 1px solid #000;
            background-color: #e0e0e0;
            padding: 5px 20px;
            font-weight: bold;
            font-size: 16px;
        }

        .client-info {
            margin-bottom: 30px;
            padding-left: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        .table th {
            background-color: #999;
            color: white;
            font-weight: bold;
        }

        .table td.left {
            text-align: left;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border: 1px solid #ccc;
        }

        .totals-table td {
            padding: 10px;
            border: 1px solid #ccc;
            font-weight: bold;
        }

        .amount-in-words {
            margin-bottom: 50px;
        }

        .signature {
            text-align: right;
            margin-bottom: 70px;
        }

        .footer {
            font-size: 11px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }

        .legal-note {
            font-size: 10px;
            margin-bottom: 20px;
        }

        .company-details {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            line-height: 1.5;
        }

        .company-details span {
            margin-right: 15px;
        }
    </style>
</head>

<body>
    <div class="header">
        @if ($invoice->company && $invoice->company->logo)
            <img src="{{ storage_path('app/public/' . $invoice->company->logo) }}" alt="Logo" class="logo">
        @endif
    </div>

    <div class="date">
        Date :
        {{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d / m / Y') : date('d / m / Y') }}
    </div>

    <div class="invoice-title-wrapper">
        <div class="invoice-title">
            Facture N° {{ $invoice->invoice_number ?? str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}
        </div>
    </div>

    <div class="client-info">
        <strong>Client :</strong> {{ $invoice->client->name }}<br>
        <strong>ICE :</strong> {{ $invoice->client->ice ?? '' }}
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 40%;">Désignation</th>
                <th>Nbr de jours</th>
                <th>Prix par jour</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $calculatedTotal = 0;
            @endphp
            @if ($invoice->items->count() > 0)
                @foreach ($invoice->items as $item)
                    @php
                        $lineTotal = $item->days * $item->unit_price;
                        $calculatedTotal += $lineTotal;
                    @endphp
                    <tr>
                        <td class="left">{{ $item->designation }}</td>
                        <td>{{ $item->days }}</td>
                        <td>{{ number_format($item->unit_price, 0, ',', ' ') }}</td>
                        <td>{{ number_format($lineTotal, 0, ',', ' ') }}</td>
                    </tr>
                @endforeach
                {{-- Add some empty rows to fill space --}}
                @for ($i = 0; $i < 6 - $invoice->items->count(); $i++)
                    <tr>
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            @else
                <!-- Fallback if no items input yet, use global amount -->
                <tr>
                    <td class="left">{{ $invoice->notes ?? 'Service' }}</td>
                    <td>1</td>
                    <td>{{ number_format($invoice->amount, 0, ',', ' ') }}</td>
                    <td>{{ number_format($invoice->amount, 0, ',', ' ') }}</td>
                </tr>
                @php $calculatedTotal = $invoice->amount; @endphp
                @for ($i = 0; $i < 6; $i++)
                    <tr>
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td style="width: 50%;">Montant en dirhams exonéré de la TVA¹</td>
            <td style="width: 30%;">Total Net à payer</td>
            <td style="text-align: right;">{{ number_format($calculatedTotal, 0, ',', ' ') }}</td>
        </tr>
    </table>

    <div class="amount-in-words">
        ARRETE LA PRESENTE FACTURE A LA SOMME DE :<br>
        <strong>#{{ class_exists('NumberFormatter') ? new NumberFormatter('fr', NumberFormatter::SPELLOUT)->format($calculatedTotal) : $calculatedTotal }}
            Dirhams#</strong>
    </div>

    <div class="signature">
        Signature :<br>
        @if ($invoice->company && $invoice->company->signature_path)
            <!-- Assuming there's a signature in company provided fields or similar, otherwise blank -->
            <!-- <img src="..." /> -->
        @endif
    </div>

    <div class="footer">
        <div class="legal-note">
            ¹Art 91 – II – 1 du Code Général des Impôts.
        </div>
        <div style="border-top: 1px dashed #ccc; margin-bottom: 10px;"></div>
        <div class="company-details">
            @if ($invoice->company)
                <span><strong>{{ $invoice->company->type === 'company' ? 'Entreprise' : 'Auto Entrepreneur' }}
                        :</strong> {{ $invoice->company->name }}</span>
                @if ($invoice->company->cnie)
                    <span><strong>CNIE :</strong> {{ $invoice->company->cnie }}</span>
                @endif
                @if ($invoice->company->address)
                    <span style="display: block; width: 100%; margin: 5px 0;">
                        <strong>Adresse :</strong> {{ $invoice->company->address }}
                    </span>
                @endif
                @if ($invoice->company->ice)
                    <span style="display: block; width: 100%; margin-bottom: 5px;">
                        <strong>ICE
                            {{ $invoice->company->type === 'individual' ? '(N° d’inscription au registre national de l’auto-entrepreneur)' : '' }}
                            :</strong> {{ $invoice->company->ice }}
                    </span>
                @endif
                @if ($invoice->company->if)
                    <span><strong>IF :</strong> {{ $invoice->company->if }}</span>
                @endif
                @if ($invoice->company->patente)
                    <span><strong>Taxe professionnelle N° :</strong> {{ $invoice->company->patente }}</span>
                @endif
                @if ($invoice->company->rc)
                    <span><strong>RC :</strong> {{ $invoice->company->rc }}</span>
                @endif
                <div style="width: 100%; margin-top: 5px;">
                    @if ($invoice->company->phone)
                        <span style="margin-right: 20px;"><strong>TEL :</strong> {{ $invoice->company->phone }}</span>
                    @endif
                    @if ($invoice->company->email)
                        <span><strong>Mail :</strong> {{ $invoice->company->email }}</span>
                    @endif
                </div>
            @endif
        </div>
    </div>
</body>

</html>
