<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        .header {
            margin-bottom: 20px;
        }

        .header table {
            width: 100%;
        }

        .logo {
            height: 60px;
            width: auto;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            margin: 0;
            color: #111;
        }

        .site-name {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #666;
            margin-top: 5px;
        }

        .order-info {
            text-align: right;
        }

        .order-number {
            font-size: 24px;
            font-weight: bold;
            color: #10b981;
            margin: 5px 0;
        }

        .label {
            font-size: 10px;
            font-weight: bold;
            color: #999;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .details-section {
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }

        .details-section table {
            width: 100%;
        }

        .details-section td {
            vertical-align: top;
            width: 50%;
        }

        .customer-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .address {
            font-size: 11px;
            color: #666;
            max-width: 250px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            background: #f3f4f6;
            color: #374151;
        }

        .status-completed {
            background: #dcfce7;
            color: #15803d;
        }

        .status-processing {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .payment-status {
            font-weight: bold;
        }

        .payment-paid {
            color: #10b981;
        }

        .payment-unpaid {
            color: #ef4444;
        }

        .payment-failed {
            color: #ef4444;
        }

        .status-pending {
            background: #fef9c3;
            color: #a16207;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #b91c1c;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            background: #f9fafb;
            text-align: left;
            padding: 10px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: #999;
            border-bottom: 2px solid #eee;
        }

        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
        }

        .item-name {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 3px;
        }

        .item-category {
            font-size: 10px;
            color: #999;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer-section {
            width: 100%;
        }

        .totals-table {
            width: 250px;
            float: right;
        }

        .totals-table td {
            padding: 5px 0;
        }

        .grand-total-row td {
            border-top: 2px solid #111;
            padding-top: 10px;
            margin-top: 10px;
        }

        .grand-total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #0000;
        }

        .notes {
            width: 300px;
            padding: 15px;
            background: #fafafa;
            border: 1px solid #eee;
            border-radius: 8px;
            font-size: 11px;
        }

        .copyright {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #ccc;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    @if(isset($settings['site_logo']))
                    <img src="{{ public_path('storage/' . $settings['site_logo']) }}" class="logo">
                    @endif
                    <h1 class="invoice-title">INVOICE</h1>
                    <div class="site-name">{{ $settings['site_name'] ?? 'Kantin NFBS' }}</div>
                </td>
                <td class="order-info">
                    <div class="label">Order Number</div>
                    <div class="order-number">#{{ $order->order_number }}</div>
                    <div class="label">Date Issued</div>
                    <div>{{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('d F Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="details-section">
        <table>
            <tr>
                <td style="line-height: 1.6; vertical-align: top;">
                    <div class="label" style="margin-bottom: 4px;">Ditagihkan Kepada</div>
                    <div class="customer-name" style="font-size: 14px; font-weight: bold;">{{ $order->user->name ?? 'Customer' }}</div>
                    <div style="color: #666; margin-bottom: 20px;">{{ $order->user->username ?? '' }}</div>

                    <div class="label" style="margin-bottom: 4px;">Alamat Pengiriman</div>
                    <div class="address" style="color: #444; width: 80%;">{{ $order->shipping_address ?? $order->address }}</div>
                </td>

                <td class="text-right" style="padding-top: 20px; padding-bottom: 20px; line-height: 1.6; vertical-align: top;">
                    <div class="label" style="margin-bottom: 8px;">Status Pesanan</div>
                    <div style="margin-bottom: 25px;"> <span class="status-badge status-{{ $order->status }}" style="padding: 4px 12px;">
                            {{ strtoupper($order->status) }}
                        </span>
                    </div>

                    <div class="label" style="margin-bottom: 4px;">Metode Pembayaran</div>
                    <div style="font-weight: bold;">{{ $order->financialAccount->name ?? '-' }}</div>
                    <div style="color: #666; font-size: 10px; margin-bottom: 10px;">{{ $order->financialAccount->account_number ?? '' }}</div>

                    <div style="margin-top: 15px;">
                        <span class="payment-status payment-{{ $order->payment_status }}" style="padding: 4px 12px;">
                            {{ strtoupper($order->payment_status) }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="45%">Item Deskripsi</th>
                <th width="10%" class="text-center">Qty</th>
                <th width="20%" class="text-right">Harga Satuan</th>
                <th width="20%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <div class="item-name">{{ $item->product->name ?? 'Product' }}</div>
                    <div class="item-category">{{ $item->product->category->name ?? '' }}</div>
                </td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">Rp {{ number_format($item->selling_price, 0, ',', '.') }}</td>
                <td class="text-right" style="font-weight: bold;">Rp {{ number_format($item->selling_price * $item->quantity, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-section">
        <table style="width: 100%;">
            <tr>
                <td style="vertical-align: bottom;">
                    <div class="notes">
                        <div style="font-weight: bold; margin-bottom: 5px;">Notes & Remarks</div>
                        Terima kasih telah berbelanja di {{ $settings['site_name'] ?? 'Kantin NFBS' }}.<br>
                        Silahkan simpan invoice ini sebagai bukti transaksi yang sah.
                    </div>
                </td>
                <td style="vertical-align: bottom;">
                    <table class="totals-table">
                        <tr>
                            <td class="label">Subtotal</td>
                            <td class="text-right" style="font-weight: bold;">Rp {{ number_format($order->grand_total - $order->shipping_cost, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Shipping Fee</td>
                            <td class="text-right" style="font-weight: bold;">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="grand-total-row">
                            <td class="label" style="color: #111;">Grand Total</td>
                            <td class="text-right grand-total-amount">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="copyright">
        Copyright &copy; {{ date('Y') }} {{ $settings['site_name'] ?? 'Kantin NFBS' }} - All Rights Reserved
    </div>
</body>

</html>