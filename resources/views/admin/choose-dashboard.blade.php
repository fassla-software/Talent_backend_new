@extends('layouts.admin')

@section('content')
<div class="container mt-5 text-center">
    <h2 class="mb-4 title">Select Your Dashboard</h2>
    <div class="dashboard-container">
        <a href="{{ route('admin.set-dashboard', 'plumber') }}" class="dashboard-btn plumber">
            <i class="fa-solid fa-wrench"></i>
            <span>Plumber Dashboard</span>
        </a>
        <a href="{{ route('admin.set-dashboard', 'ecommerce') }}" class="dashboard-btn ecommerce">
            <i class="fa-solid fa-store"></i>
            <span>E-commerce Dashboard</span>
        </a>
    </div>
</div>

<style>
    /* General Styling */
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f7fc;
    }

    .title {
        font-weight: bold;
        text-transform: uppercase;
        color: #333;
        text-align: center;
    }

    /* Dashboard Container */
    .dashboard-container {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin-top: 20px;
    }

    /* Dashboard Buttons */
    .dashboard-btn {
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px 40px;
        font-size: 1.2rem;
        font-weight: bold;
        border-radius: 12px;
        transition: all 0.3s ease-in-out;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 250px;
    }

    .dashboard-btn i {
        font-size: 1.8rem;
        margin-right: 10px;
    }

    .plumber {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: #fff;
    }

    .plumber:hover {
        background: linear-gradient(135deg, #0056b3, #003b80);
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0, 91, 187, 0.3);
    }

    .ecommerce {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        color: #fff;
    }

    .ecommerce:hover {
        background: linear-gradient(135deg, #1e7e34, #155724);
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(40, 167, 69, 0.3);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-container {
            flex-direction: column;
            align-items: center;
        }
    }
</style>
@endsection
