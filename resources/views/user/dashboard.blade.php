@extends('layouts.frontend')
@section('title', 'Trang cá nhân')
@section('content')
<div class="container py-4">
    <h3>Xin chào {{ Auth::user()->name }}</h3>
    <p>Đây là trang quản lý tài khoản khách hàng.</p>
    <ul>
        <li><a href="#">Thông tin tài khoản</a></li>
        <li><a href="#">Đơn hàng của tôi</a></li>
        <li><a href="#">Lịch hẹn dịch vụ</a></li>
    </ul>
</div>
@endsection