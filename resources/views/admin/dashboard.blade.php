@extends('layouts.admin')

@section('title', 'Bảng điều khiển')

@section('content')
<div class="row g-4">
    {{-- Cards thống kê --}}
    <div class="col-md-3">
        <div class="card p-4 text-center">
            <div class="stat-icon mx-auto mb-2"><i class="fa-solid fa-box-open"></i></div>
            <h5 class="fw-bold mb-0">Sản phẩm</h5>
            <p class="display-6 fw-bold text-rose mb-0">{{ $totalProducts ?? 128 }}</p>
            <small class="text-muted">Tổng số sản phẩm hiện có</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 text-center">
            <div class="stat-icon mx-auto mb-2"><i class="fa-solid fa-users"></i></div>
            <h5 class="fw-bold mb-0">Khách hàng</h5>
            <p class="display-6 fw-bold text-rose mb-0">{{ $totalCustomers ?? 356 }}</p>
            <small class="text-muted">Khách hàng đã đăng ký</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 text-center">
            <div class="stat-icon mx-auto mb-2"><i class="fa-solid fa-receipt"></i></div>
            <h5 class="fw-bold mb-0">Đơn hàng</h5>
            <p class="display-6 fw-bold text-rose mb-0">{{ $totalOrders ?? 74 }}</p>
            <small class="text-muted">Đơn hàng trong tháng</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4 text-center">
            <div class="stat-icon mx-auto mb-2"><i class="fa-solid fa-scissors"></i></div>
            <h5 class="fw-bold mb-0">Dịch vụ</h5>
            <p class="display-6 fw-bold text-rose mb-0">{{ $totalServices ?? 42 }}</p>
            <small class="text-muted">Dịch vụ hiện đang hoạt động</small>
        </div>
    </div>
</div>

{{-- Biểu đồ doanh thu --}}
<div class="card mt-4 p-4">
    <h5 class="fw-bold mb-3">📈 Doanh thu 6 tháng gần đây</h5>
    <canvas id="revenueChart" height="100"></canvas>
</div>

{{-- Biểu đồ dịch vụ --}}
<div class="card mt-4 p-4">
    <h5 class="fw-bold mb-3">🐾 Lượt đặt lịch theo dịch vụ</h5>
    <canvas id="serviceChart" weight="100"></canvas>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart pastel tone
    const pastelColors = ['#FFC9DE', '#FFB6C1', '#FF8FAB', '#F9A1BC', '#FDBED1', '#FFDCE4'];

    // Revenue Chart
    const ctxRevenue = document.getElementById('revenueChart');
    new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: ['Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10'],
            datasets: [{
                label: 'Doanh thu (triệu VNĐ)',
                data: [25, 32, 40, 28, 45, 52],
                borderColor: '#FF8FAB',
                backgroundColor: 'rgba(255, 143, 171, 0.2)',
                fill: true,
                tension: 0.3,
                borderWidth: 3
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: getTextColor()
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: getTextColor()
                    },
                    grid: {
                        color: 'rgba(255,182,193,0.2)'
                    }
                },
                y: {
                    ticks: {
                        color: getTextColor()
                    },
                    grid: {
                        color: 'rgba(255,182,193,0.2)'
                    }
                }
            }
        }
    });

    // Service Chart
    const ctxService = document.getElementById('serviceChart');
    new Chart(ctxService, {
        type: 'doughnut',
        data: {
            labels: ['Grooming', 'Khám bệnh', 'Tiêm phòng', 'Lưu trú'],
            datasets: [{
                data: [35, 25, 20, 20],
                backgroundColor: pastelColors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        color: getTextColor()
                    }
                }
            }
        }
    });

    // Helper: đổi màu chữ theo theme
    function getTextColor() {
        const theme = document.documentElement.getAttribute('data-theme');
        return theme === 'dark' ? '#EEE' : '#444';
    }
</script>
@endpush