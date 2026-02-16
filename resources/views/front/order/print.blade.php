<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #{{ $order->order_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/primeicons/primeicons.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        @media print {
            @page {
                size: A4;
                margin: 0;
            }

            body {
                margin: 0;
                padding: 0;
                background: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }

            .printable-area {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                width: 100% !important;
                max-width: none !important;
            }

            tr {
                page-break-inside: avoid;
            }
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 8rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            opacity: 0.1;
            white-space: nowrap;
            pointer-events: none;
            z-index: 50;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Actions Bar -->
        <div class="flex justify-between items-center mb-8 no-print">
            <a href="{{ route('home') }}"
                class="flex items-center text-gray-600 hover:text-gray-900 transition-colors font-bold text-sm tracking-widest">
                <i class="pi pi-arrow-left mr-2"></i>
                <span>Back to Home</span>
            </a>

            <div class="flex gap-2">
                <button onclick="window.print()" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-gray-700 font-bold text-sm hover:bg-gray-50 flex items-center gap-2">
                    <i class="pi pi-print"></i> Print
                </button>
            </div>
        </div>

        <!-- Invoice Content -->
        <div class="printable-area relative bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">

            <!-- Watermark -->
            <div class="watermark {{ 
                $order->payment_status === 'paid' ? 'text-green-600' : 
                ($order->payment_status === 'unpaid' || $order->payment_status === 'failed' ? 'text-red-600' : 'text-gray-300')
            }}">
                {{ $order->payment_status }}
            </div>

            <div class="overflow-x-auto">
                <table class="relative z-10 w-full">
                    <tbody>
                        <!-- HEADER ROW -->
                        <tr>
                            <td colspan="2">
                                <div class="px-12 pt-12 pb-6">
                                    <table class="w-full">
                                        <tbody>
                                            <tr>
                                                <td class="align-top">
                                                    @if(isset($settings['site_logo']))
                                                    @php
                                                    $logoPath = storage_path('app/public/' . $settings['site_logo']);
                                                    $logoData = '';
                                                    if (file_exists($logoPath)) {
                                                    $logoData = base64_encode(file_get_contents($logoPath));
                                                    }
                                                    @endphp

                                                    @if($logoData)
                                                    <img src="data:image/png;base64,{{ $logoData }}" alt="Logo" style="height: 64px; width: auto;">
                                                    @else
                                                    <h2 style="color: #16a34a; font-weight: bold;">KOPERASI NFBS</h2>
                                                    @endif
                                                    @endif
                                                    <h1 class="text-4xl pt-4 font-black tracking-tight text-gray-900 uppercase">INVOICE</h1>
                                                </td>
                                                <td class="text-right align-top">
                                                    <div class="mb-4">
                                                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Order Number</label>
                                                        <p class="text-3xl font-black font-mono text-emerald-500">#{{ $order->order_number }}</p>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Date Issued</label>
                                                        <p class="text-gray-900 font-bold">{{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('d F Y') }}</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>

                        <!-- INFO ROW -->
                        <tr class="border-t border-b border-gray-50">
                            <td class="px-12 py-8 align-top w-1/2 border-r border-gray-50">
                                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Customer Details</h3>
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center border border-gray-100 text-gray-400">
                                        <i class="pi pi-user text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-lg font-black text-gray-900 capitalize leading-tight">{{ $order->user->name ?? 'Customer' }}</p>
                                        <p class="text-sm text-gray-500">{{ $order->user->username ?? '' }}</p>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Shipping Address</label>
                                    <p class="text-sm text-gray-600 leading-relaxed">{{ $order->shipping_address ?? $order->address }}</p>
                                </div>
                            </td>
                            <td class="px-12 py-8 align-top text-right w-1/2">
                                <div class="mb-6">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Order Status</label>
                                    <span class="inline-block px-4 py-1.5 rounded-full text-xs font-black uppercase {{ 
                                        $order->status === 'completed' ? 'bg-green-100 text-green-700' : 
                                        ($order->status === 'processing' ? 'bg-blue-100 text-blue-700' : 
                                        ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'))
                                    }}">
                                        {{ $order->status }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Payment Method</label>
                                    <p class="font-black text-gray-900 text-lg leading-tight">{{ $order->financialAccount->name ?? '-' }}</p>
                                    <p class="text-sm text-gray-500 font-mono">{{ $order->financialAccount->account_number ?? '' }}</p>
                                    <div class="mt-2">
                                        <span class="text-sm font-black uppercase {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $order->payment_status }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- ITEMS ROW -->
                        <tr>
                            <td colspan="2" class="px-8 pt-6">
                                <table class="w-full border-collapse">
                                    <thead>
                                        <tr class="bg-gray-50 rounded-lg">
                                            <th class="text-left font-black text-[10px] text-gray-400 uppercase tracking-widest py-3 pl-4 border-b border-gray-100 rounded-l-lg">No</th>
                                            <th class="text-left font-black text-[10px] text-gray-400 uppercase tracking-widest py-3 border-b border-gray-100">Item Details</th>
                                            <th class="text-center font-black text-[10px] text-gray-400 uppercase tracking-widest py-3 border-b border-gray-100">Qty</th>
                                            <th class="text-right font-black text-[10px] text-gray-400 uppercase tracking-widest py-3 border-b border-gray-100">Price</th>
                                            <th class="text-right font-black text-[10px] text-gray-400 uppercase tracking-widest py-3 pr-4 border-b border-gray-100 rounded-r-lg">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @foreach($order->orderItems as $index => $item)
                                        <tr>
                                            <td class="py-4 pl-4 text-sm text-gray-400 font-bold">{{ $index + 1 }}</td>
                                            <td class="py-4">
                                                <p class="font-black text-gray-900 mb-0.5 leading-tight">{{ $item->product->name ?? 'Product' }}</p>
                                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $item->product->category->name ?? '' }}</span>
                                            </td>
                                            <td class="py-4 text-center text-sm font-black text-gray-900 border-x border-gray-50">{{ $item->quantity }}</td>
                                            <td class="py-4 text-right text-sm font-bold text-gray-600">Rp {{ number_format($item->selling_price, 0, ',', '.') }}</td>
                                            <td class="py-4 text-right text-sm font-black text-gray-900 pr-4">Rp {{ number_format($item->selling_price * $item->quantity, 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>

                        <!-- FOOTER ROW -->
                        <tr>
                            <td class="px-12 pt-8 pb-12 align-bottom w-1/2">
                                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 max-w-sm">
                                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Notes & Remarks</h4>
                                    <p class="text-xs text-gray-500 leading-relaxed">* Silahkan kirim <span class="font-bold text-gray-700">bukti transfer</span> Anda melalui WhatsApp untuk mempercepat verifikasi.</p>
                                    @if(isset($settings['whatsapp_number']))
                                    <div class="mt-4 flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white text-xs">
                                            <i class="pi pi-whatsapp"></i>
                                        </div>
                                        <span class="text-xs font-black text-gray-700">{{ $settings['whatsapp_number'] }}</span>
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-12 pt-8 pb-12 align-bottom">
                                <table class="w-full max-w-xs ml-auto divide-y divide-gray-100">
                                    <tbody>
                                        <tr>
                                            <td class="text-left py-3 text-sm font-bold text-gray-400">Subtotal</td>
                                            <td class="text-right py-3 text-lg font-black text-gray-900">Rp {{ number_format($order->grand_total - $order->shipping_cost, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-left py-3 text-sm font-bold text-gray-400">Shipping Fee</td>
                                            <td class="text-right py-3 text-lg font-black text-gray-600">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr class="border-t-2 border-gray-900">
                                            <td class="text-left pt-4 text-sm font-black text-gray-900 uppercase tracking-widest">Grand Total</td>
                                            <td class="text-right pt-4 text-4xl font-black text-emerald-600 tracking-tighter leading-none">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-gray-50 px-12 py-6 text-center border-t border-gray-100">
                <p class="text-[10px] font-bold text-gray-300 uppercase tracking-[0.2em]">
                    Copyright &copy; {{ date('Y') }} {{ $settings['site_name'] ?? 'Kantin NFBS' }} - All Rights Reserved
                </p>
            </div>
        </div>
    </div>
</body>

</html>