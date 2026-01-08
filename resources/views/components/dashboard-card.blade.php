@props(['icon', 'title', 'value'])
<div class="col-md-6 col-lg-4 col-xl-3 mb-3">
    <div class="dashboard-summery text-white" style="background: linear-gradient(135deg, #28a745, #218838); border-radius: 10px; padding: 20px;">
        <h2>{{ $value }}</h2>
        <h5>{{ $title }}</h5>
        <div class="icon">
            <i class="bi {{ $icon }}"></i>
        </div>
    </div>
</div>
