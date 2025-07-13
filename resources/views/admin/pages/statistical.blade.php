@extends('layouts.layouts_admin')
@section('title', 'Trang quản lý thống kê')
@section('active-static', 'active')
@section('content')
    <div class="content" id="thongke">
        <div class="head">
            <div class="title">Quản Lý Thống Kê</div>
            <form method="GET" action="{{ route('admin.revenue.export') }}">
                <button type="submit">
                    <i class="fa-regular fa-file-excel"></i> Xuất file
                </button>
            </form>
                    </div>
        <div>
            <label for="statistic-type">Chọn thời gian:</label>
            <select id="statistic-type">
                <option value="7ngay">7 ngày qua</option>
                <option value="thangnay">Tháng này</option>
                <option value="thangtruoc">Tháng trước</option>
                <option value="365ngay">365 ngày</option>
            </select>
        </div>
        
        <canvas id="dailyRevenueChart" width="400" height="200"></canvas>
        
        <div class="separator_x"></div>
        <div class="chart">
            <h2 style="text-align: center;">Thống kê doanh thu</h2>
            <canvas id="sumChart" width="400" height="200"></canvas>

            <h2 style="text-align: center;">Thống kê lượt mua</h2>
            <canvas id="countChart" width="400" height="200"></canvas>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const sum = @json($sum);
            const count = @json($count);

            const labels = [
                'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4',
                'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8',
                'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
            ];


            
            const sumCtx = document.getElementById('sumChart').getContext('2d');
            new Chart(sumCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Tổng doanh thu (VNĐ)',
                        data: sum,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('vi-VN') + ' đ';
                                }
                            }
                        }
                    }
                }
            });

            const countCtx = document.getElementById('countChart').getContext('2d');
            new Chart(countCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Tổng lượt mua (đơn hàng)',
                        data: count,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            precision: 0
                        }
                    }
                }
            });
        </script>
    @endsection
    @section('script')
        <script>
            const ctx = document.getElementById('dailyRevenueChart').getContext('2d');
            let revenueChart;

            function fetchChartData(type = '7ngay') {
                fetch(`/admin/statistical/data?statistic_type=${type}`)
                    .then(response => response.json())
                    .then(data => {
                        const labels = data.chart_data.map(item => item.created_at);
                        const revenue = data.chart_data.map(item => item.total_price);

                        // Nếu chart đã tồn tại thì hủy để vẽ lại
                        if (revenueChart) {
                            revenueChart.destroy();
                        }

                        revenueChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Doanh thu theo ngày (VNĐ)',
                                    data: revenue,
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 2,
                                    tension: 0.3
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(value) {
                                                return value.toLocaleString('vi-VN') + ' đ';
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    });
            }

            // Gọi khi load trang
            fetchChartData('7ngay');

            // Gọi khi chọn loại thời gian khác
            document.getElementById('statistic-type').addEventListener('change', function() {
                fetchChartData(this.value);
            });
        </script>

    @endsection
