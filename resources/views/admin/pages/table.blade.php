@extends('layouts.layouts_admin')
@section('title', 'Trang quản lý bàn ăn')
@section('active-table', 'active')
<style>
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
        }
        .table-box {
            margin-top: 12px;
            background-color: #fff8dc;
            border: 2px solid #ffcc99;
            border-radius: 10px;
            padding: 30px 10px;
            text-align: center;
            font-weight: bold;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.2);
            transition: transform 0.2s ease;
        }
        .table-box:hover {
            transform: scale(1.05);
            background-color: #ffefcc;
        }
        .btn {
        text-align: center;
        padding: 6px;
        width: 130px;
        margin-bottom: 12px;
        border-radius: 4px;
        float: right;
        margin-right: 0px;
        margin-top: 1px;
    }

    .filter {
        margin-top: 3px;
        margin-right: 5px;
        padding: 7px 30px;
        border-radius: 5px;
    }

    td,
    th {
        word-wrap: break-word;
        word-break: break-word;
    }
    </style>
@section('content')
    <div class="content" id="banan">
        <div class="head">
            <div class="title">Quản Lý bàn ăn</div>
            <!-- Nút ẩn/hiện -->
        <button onclick="toggleForm()" class="btn btn-warning">Ẩn/Hiện Form</button>
        </div>
        <div class="separator_x">
        </div>

        <div>
            <!-- Form thêm/sửa bàn -->
            <div id="table-form" style="display: block;">
                <form action="{{ route('admin.table.store') }}" method="POST">
                    @csrf
                    <label>Số bàn</label>
                    <input type="text" name="name" class="form-control">
                    <label>Trạng thái</label>
                    <select name="table_status_id" class="form-control">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-success">Lưu</button>
                </form>
            </div>
            <div class="grid-container">
    @for ($i = 1; $i <= 12; $i++)
        <div class="table-box">Ô trống {{ $i }}</div>
    @endfor
</div>

        </div>
    </div>
@endsection
@section('script')
<script>
function toggleForm() {
    const form = document.getElementById('table-form');
    form.style.display = (form.style.display === 'none') ? 'block' : 'none';
}
</script>
@endsection
