@extends('layouts.layouts_admin')
@section('title', 'Trang quản lý thống kê')
@section('active-static', 'active')
@section('content')

    <div class="content" id="thongke">
        <div class="head d-flex align-items-center justify-content-between"
            style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
            <div class="title">Quản Lý Thống Kê</div>

            <div class="d-flex align-items-center gap-2" style="display: flex; align-items: center; gap: 15px;">

                <!-- Bộ lọc thời gian -->
                <select id="statistic-type" name="statistic_type"
                    style="padding: 10px 16px; border-radius: 6px; border: 1px solid #ccc;
                           font-size: 16px; width: 180px;">
                    <option value="7ngay">Tuần qua</option>
                    <option value="thangnay">Tháng này</option>
                    <option value="365ngay">365 ngày</option>
                </select>

                <!-- Nút xuất file -->
                <form method="GET" action="{{ route('admin.revenue.export') }}">
                    <button type="submit"
                        style="padding: 10px 20px; background-color: green; color: white;
                               border: none; border-radius: 6px; cursor: pointer;
                               font-size: 16px; width: 160px; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <i class="fa-regular fa-file-excel"></i> Xuất file
                    </button>
                </form>
            </div>
        </div>


        <div class="separator_x"></div>
        <h2 style="text-align: center;">Thống kê doanh thu</h2>




        <canvas id="dailyRevenueChart" height="150"></canvas>


        <div class="chart">
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
                        const revenue = data.chart_data.map(item => Number(item.total_price));

                        if (revenueChart) revenueChart.destroy();

                        revenueChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Doanh thu theo ngày (VNĐ)',
                                    data: revenue,
                                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: value => value.toLocaleString('vi-VN') + ' đ'
                                        }
                                    }
                                }
                            }
                        });
                    });
            }

            fetchChartData('7ngay');

            document.getElementById('statistic-type').addEventListener('change', function() {
                fetchChartData(this.value);
            });
        </script>


    @endsection
