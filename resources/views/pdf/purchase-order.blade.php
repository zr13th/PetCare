<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Phiếu Nhập Kho - {{ $record->po_number }}</title>
    <style>
    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 12px;
    }

    .header {
        text-align: center;
        margin-bottom: 20px;
    }

    .logo {
        font-size: 24px;
        font-weight: bold;
        color: #2563eb;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .table th,
    .table td {
        border: 1px solid #000;
        padding: 8px;
        text-align: left;
    }

    .footer {
        margin-top: 30px;
        text-align: right;
    }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo">PETCARE SYSTEM</div>
        <p>Đ/C: Long Xuyên, An Giang</p>
        <h2>PHIẾU NHẬP KHO</h2>
        <p>Mã đơn: {{ $record->po_number }} | Ngày: {{ $record->created_at->format('d/m/Y') }}</p>
    </div>

    <div class="info">
        <p><strong>Nhà cung cấp:</strong> {{ $record->supplier->name ?? 'Chưa xác định' }}</p>
        <p><strong>Kho nhập:</strong> {{ $record->warehouse->name ?? 'Chưa xác định' }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>STT</th>
                <th>Sản phẩm (SKU)</th>
                <th>Số lượng</th>
                <th>Giá vốn</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->variant->product->name }} ({{ $item->variant->sku }})</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->cost_price) }} đ</td>
                <td>{{ number_format($item->quantity * $item->cost_price) }} đ</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right;"><strong>Tổng cộng:</strong></td>
                <td><strong>{{ number_format($record->total_amount) }} đ</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Ngày .... tháng .... năm 2026</p>
        <p style="margin-right: 50px;">Người lập phiếu</p>
        <br><br>
        <p style="margin-right: 45px;">(Ký tên)</p>
    </div>
</body>

</html>