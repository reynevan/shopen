<html>
<head>
    <title>Faktura {{ $invoice->invoice_number }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @font-face {
            font-family: 'Roboto';
            src: url('{{ storage_path("fonts/Roboto-Regular.ttf") }}');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Roboto';
            src: url('{{ storage_path("fonts/Roboto-SemiBold.ttf") }}');
            font-weight: 600;
            font-style: normal;
        }

        body {
            font-family: 'Roboto', sans-serif;
        }

        .semibold {
            font-weight: 600;
            letter-spacing: 1px;
        }

        .text-sm {
            font-size: 10px;
        }

        .text-xs {
            font-size: 8px;
        }

        body * {
            padding: 0;
            margin: 0;
        }

        table.items, table.tax-rates, table.payment-details {
            border-collapse: collapse;
        }

        table.items th, table.tax-rates th, table.payment-details th {
            border-bottom: 1px solid #333;
        }

        table.items td, table.tax-rates td, table.payment-details td {
            border-bottom: 1px solid #bbb;
        }
    </style>
</head>
<body>

@includeFirst(['pdf.invoice.elements.correction-header', 'shopen::pdf.invoice.elements.correction-header'], ['invoice' => $invoice])

<div style="font-size: 16px; margin-top: 16px; margin-bottom: 4px;">Przed korektą</div>

@if ($invoice->isAddressCorrected())
    @includeFirst(['pdf.invoice.elements.billing-address', 'shopen::pdf.invoice.elements.billing-address'], ['address' => $invoice->baseInvoice->billingAddress])
@endif

@if ($invoice->hasItemsCorrected())
    @includeFirst(['pdf.invoice.elements.items', 'shopen::pdf.invoice.elements.items'], ['invoice' => $invoice->baseInvoice])
@endif

<div style="font-size: 16px; margin-top: 16px; margin-bottom: 4px;">Po korekcie</div>

@if ($invoice->isAddressCorrected())
    @includeFirst(['pdf.invoice.elements.billing-address', 'shopen::pdf.invoice.elements.billing-address'], ['address' => $invoice->billingAddress])
@endif

@if ($invoice->hasItemsCorrected())
    @includeFirst(['pdf.invoice.elements.items', 'shopen::pdf.invoice.elements.items'], ['invoice' => $invoice])
@endif



@if ($invoice->hasItemsCorrected())
    <div class="text-xs" style="position: relative; height: 100px">
        <div style="display: inline-block; width: 48%; position: absolute; left: 0;">
            @includeFirst(['pdf.invoice.elements.payment-details', 'shopen::pdf.invoice.elements.payment-details'], ['invoice' => $invoice])
        </div>
        <div style="display: inline-block; width: 48%; position: absolute; right: 0;">
            @includeFirst(['pdf.invoice.elements.tax-rates', 'shopen::pdf.invoice.elements.tax-rates'], ['invoice' => $invoice])
        </div>
    </div>
    @includeFirst(['pdf.invoice.elements.summary', 'shopen::pdf.invoice.elements.summary'], ['invoice' => $invoice])
@endif
@includeFirst(['pdf.invoice.elements.placeholders', 'shopen::pdf.invoice.elements.placeholders'], ['invoice' => $invoice])
</body>
</html>