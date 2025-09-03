<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture #{{ $invoice->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .company-info {
            flex: 1;
        }
        .invoice-info {
            text-align: right;
            flex: 1;
        }
        .client-info {
            margin: 30px 0;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .invoice-details {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        .invoice-details th,
        .invoice-details td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .invoice-details th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        .total-section {
            margin-top: 30px;
            text-align: right;
        }
        .total-row {
            margin: 10px 0;
            font-size: 18px;
        }
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            border-top: 2px solid #007bff;
            padding-top: 10px;
        }
        .notes {
            margin-top: 40px;
            padding: 20px;
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }
        .status-paid { background-color: #28a745; }
        .status-sent { background-color: #ffc107; color: #000; }
        .status-draft { background-color: #6c757d; }
        .status-overdue { background-color: #dc3545; }
        .status-partial { background-color: #17a2b8; }
        .status-cancelled { background-color: #343a40; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <h1>Votre Entreprise</h1>
            <p>
                123 Rue de l'Exemple<br>
                75000 Paris, France<br>
                Tél: +33 1 23 45 67 89<br>
                Email: contact@entreprise.com
            </p>
        </div>
        <div class="invoice-info">
            <h2>FACTURE #{{ $invoice->id }}</h2>
            <p>
                <strong>Date:</strong> {{ $invoice->invoice_date }}<br>
                <strong>Échéance:</strong> {{ $invoice->due_date ? $invoice->due_date : 'N/A' }}<br>
                <strong>Statut:</strong>
                <span class="status-badge status-{{ $invoice->status }}">
                    {{ ucfirst($invoice->status) }}
                </span>
            </p>
        </div>
    </div>

    <div class="client-info">
        <h3>Facturé à :</h3>
        <p>
            <strong>{{ $client->name }}</strong><br>
            @if($client->address)
                {{ $client->address }}<br>
            @endif
            @if($client->email)
                Email: {{ $client->email }}<br>
            @endif
            @if($client->phone)
                Tél: {{ $client->phone }}
            @endif
        </p>
    </div>

    <table class="invoice-details">
        <thead>
            <tr>
                <th>Description</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            {{-- Exemple de ligne de facture - à adapter selon votre modèle --}}
            <tr>
                <td>Service / Produit</td>
                <td>1</td>
                <td>{{ number_format($invoice->amount, 2, ',', ' ') }} €</td>
                <td>{{ number_format($invoice->amount, 2, ',', ' ') }} €</td>
            </tr>
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <strong>Sous-total: {{ number_format($invoice->amount, 2, ',', ' ') }} €</strong>
        </div>
        <div class="total-row">
            <strong>TVA (20%): {{ number_format($invoice->amount * 0.2, 2, ',', ' ') }} €</strong>
        </div>
        <div class="total-row total-amount">
            <strong>TOTAL: {{ number_format($invoice->amount * 1.2, 2, ',', ' ') }} €</strong>
        </div>
    </div>

    @if($invoice->notes)
        <div class="notes">
            <h4>Notes:</h4>
            <p>{{ $invoice->notes }}</p>
        </div>
    @endif

    <div class="footer">
        <p>
            Merci pour votre confiance !<br>
            Cette facture a été générée le {{ now()->format('d/m/Y à H:i') }}
        </p>
    </div>
</body>
</html>