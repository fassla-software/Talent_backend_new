@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Send Notification to Plumbers</h2>

<div id="flash-messages">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
</div>

<form method="GET" action="{{ route('plumber.send-notification-multiple') }}" class="mb-4">
    <div class="row">
        <div class="col-md-3">
            <label for="city">City</label>
            <select name="city" class="form-control">
                <option value="">Select City</option>
                @foreach($cities as $cityOption)
                    <option value="{{ $cityOption }}" {{ request('city') == $cityOption ? 'selected' : '' }}>
                        {{ $cityOption }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="area">Area</label>
            <select name="area" class="form-control">
                <option value="">Select Area</option>
                @foreach($areas as $areaOption)
                    <option value="{{ $areaOption }}" {{ request('area') == $areaOption ? 'selected' : '' }}>
                        {{ $areaOption }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="min_fixed_points">Min Fixed Points</label>
            <input type="number" name="min_fixed_points" class="form-control" value="{{ request('min_fixed_points') }}">
        </div>

        <div class="col-md-3">
            <label for="max_fixed_points">Max Fixed Points</label>
            <input type="number" name="max_fixed_points" class="form-control" value="{{ request('max_fixed_points') }}">
        </div>

        <div class="col-md-12 d-flex justify-content-end mt-2">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </div>
</form>


    <!-- Notification Form -->
    <form method="POST" action="{{ route('plumber.send-notification-multiple') }}" id="notification-form">
        @csrf
        <!-- Hidden filter fields to persist them in the POST request -->
        <input type="hidden" name="city" value="{{ $city }}">
        <input type="hidden" name="area" value="{{ $area }}">
        <input type="hidden" name="min_fixed_points" value="{{ $minFixedPoints }}">
        <input type="hidden" name="max_fixed_points" value="{{ $maxFixedPoints }}">

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="body">Description</label>
            <textarea name="body" class="form-control" required></textarea>
        </div>

        <div class="form-check mb-3 p-3 bg-light border rounded">
            <input type="checkbox" name="send_to_all" id="send_to_all" class="form-check-input" value="1" hidden>
            <label class="form-check-label h5 mb-0" for="send_to_all">
                <strong>Send to all matched plumbers (Total: {{ $plumbers->total() }})</strong>
            </label>
            <div id="selection-summary" class="text-muted mt-1" style="display: none;">
                All matched results are selected. Uncheck any item to select specific plumbers.
            </div>
        </div>

        <div class="d-flex justify-content-between mb-3">
            <button type="submit" class="btn btn-primary btn-lg">Send Notification</button>
            <button type="button" id="clear-selection" class="btn btn-secondary">Clear Selection</button>
        </div>

        <!-- Table of Plumbers -->
        <table class="table table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>
                        <input type="checkbox" id="select-all-header"> 
                        <span id="select-all-label">Select All Matched</span>
                    </th>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>City</th>
                    <th>Area</th>
                    <th>Fixed Points</th>
                </tr>
            </thead>
            <tbody id="plumber-table-body">
                @foreach($plumbers as $plumber)
                    <tr>
                        <td>
                            <input type="checkbox" class="plumber-checkbox" value="{{ $plumber->user->id }}">
                        </td>
                        <td>{{ $plumber->user->id }}</td>
                        <td>{{ $plumber->user->name }}</td>
                        <td>{{ $plumber->city }}</td>
                        <td>{{ $plumber->area }}</td>
                        <td>{{ $plumber->fixed_points }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination Controls -->
        <div class="d-flex justify-content-center mt-4">
            {{ $plumbers->appends(request()->input())->links() }}
        </div>
    </form>
</div>

<script>
    const STORAGE_KEY = 'selected_plumbers';
    const SEND_ALL_KEY = 'send_to_all_matched';
    const sendToAllCheckbox = document.getElementById('send_to_all');
    const selectAllHeader = document.getElementById('select-all-header');
    const selectionSummary = document.getElementById('selection-summary');

    function getSelectedIds() {
        const stored = localStorage.getItem(STORAGE_KEY);
        return stored ? JSON.parse(stored) : [];
    }

    function saveSelectedIds(ids) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(ids));
    }

    function isSendAllActive() {
        return localStorage.getItem(SEND_ALL_KEY) === 'true';
    }

    function setSendAllActive(active) {
        localStorage.setItem(SEND_ALL_KEY, active);
        sendToAllCheckbox.checked = active;
        selectAllHeader.checked = active;
        if (active) {
            selectionSummary.style.display = 'block';
            document.querySelectorAll('.plumber-checkbox').forEach(cb => cb.checked = true);
        } else {
            selectionSummary.style.display = 'none';
        }
    }

    function updateUIFromState() {
        const active = isSendAllActive();
        sendToAllCheckbox.checked = active;
        selectAllHeader.checked = active;
        selectionSummary.style.display = active ? 'block' : 'none';

        const selectedIds = getSelectedIds();
        document.querySelectorAll('.plumber-checkbox').forEach(checkbox => {
            if (active) {
                checkbox.checked = true;
            } else {
                checkbox.checked = selectedIds.includes(checkbox.value);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', updateUIFromState);

    // Sync header Select All with Send to all matched
    selectAllHeader.addEventListener('change', function() {
        setSendAllActive(this.checked);
        if (!this.checked) {
            localStorage.removeItem(STORAGE_KEY);
            updateUIFromState();
        }
    });

    // Sync the "Send to all matched" checkbox too
    sendToAllCheckbox.addEventListener('change', function() {
        setSendAllActive(this.checked);
        if (!this.checked) {
            localStorage.removeItem(STORAGE_KEY);
            updateUIFromState();
        }
    });

    document.getElementById('plumber-table-body').addEventListener('change', function(e) {
        if (e.target.classList.contains('plumber-checkbox')) {
            if (isSendAllActive()) {
                // If we were in "send all" mode and user unchecked one, we exit "send all" mode
                // and seed the selection with all current page IDs minus the unchecked one
                // Actually, just exit "send all" and use the normal selection logic
                setSendAllActive(false);
                
                // Initialize selection with all IDs on current page except the one unchecked?
                // Or just the one(s) that are checked.
                let selectedIds = [];
                document.querySelectorAll('.plumber-checkbox').forEach(cb => {
                    if (cb.checked) selectedIds.push(cb.value);
                });
                saveSelectedIds(selectedIds);
            } else {
                let selectedIds = getSelectedIds();
                if (e.target.checked) {
                    if (!selectedIds.includes(e.target.value)) {
                        selectedIds.push(e.target.value);
                    }
                } else {
                    selectedIds = selectedIds.filter(id => id !== e.target.value);
                    selectAllHeader.checked = false;
                }
                saveSelectedIds(selectedIds);
            }
            updateUIFromState();
        }
    });

    document.getElementById('clear-selection').addEventListener('click', function() {
        localStorage.removeItem(STORAGE_KEY);
        setSendAllActive(false);
        updateUIFromState();
    });

    document.getElementById('notification-form').addEventListener('submit', function(e) {
        if (sendToAllCheckbox.checked) {
            return true;
        }

        const selectedIds = getSelectedIds();
        if (selectedIds.length === 0) {
            alert('Please select at least one plumber or check "Send to all matched".');
            e.preventDefault();
            return false;
        }

        // Add hidden inputs for selected IDs
        selectedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'plumbers[]';
            input.value = id;
            this.appendChild(input);
        });
    });
</script>

@endsection
