@extends('layouts.app')

@section('content')
<div class="container">
    <input type="hidden" id="serverDateTime" value="2025-07-27 14:10:18">

    <div class="row">
        <!-- First Card - Give Referral Points -->
        <div class="col-md-6 mb-4">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="card-title mb-0">Give Referral Points</h5>
    @if($referralConfig)
<small class="text-muted">
    Current: {{ $referralConfig->referral_point }} pts
    ({{ ucfirst(str_replace('_', ' ', $referralConfig->point_type)) }})
</small>
    @endif
</div>

                <div class="card-body">
                    <div id="referralAlertContainer"></div>
                    <form id="referralForm" action="https://app.talentindustrial.com/plumber/plumber/addReferralPoints" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="referral_points" class="form-label">Points:</label>
                            <input type="number" id="referral_points" name="points" class="form-control" placeholder="Enter points amount" required>
                        </div>
                            <div class="mb-3">
    <label for="referral_point_type" class="form-label">Point Type:</label>
    <select id="referral_point_type" name="point_type" class="form-control" required>
        <option value="instant_withdrawal">Instant Withdrawal</option>
        <option value="fixed_points">Fixed Points</option>
    </select>
</div>

                        <button type="submit" class="btn btn-primary" id="referralSubmitBtn">
                            <span class="btn-text">Submit Referral Points</span>
                            <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Second Card - Points for Registration -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Set Registration Points</h5>
                </div>
        
                <div class="card-body">
                    <div id="registrationAlertContainer"></div>
                    <form id="registrationForm" action="https://app.talentindustrial.com/plumber/plumber/pointsForRegestration" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date:</label>
                            <input type="datetime-local" id="start_date" name="start_date" class="form-control" placeholder="Select start date" required>
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date:</label>
                            <input type="datetime-local" id="end_date" name="end_date" class="form-control" placeholder="Select end date" required>
                        </div>
                        <div class="mb-3">
                            <label for="registration_points" class="form-label">Points:</label>
                            <input type="number" id="registration_points" name="points" class="form-control" placeholder="Enter points amount (e.g., 20)" required>
                        </div>
                            
                            <div class="mb-3">
    <label for="registration_point_type" class="form-label">Point Type:</label>
    <select id="registration_point_type" name="point_type" class="form-control" required>
        <option value="instant_withdrawal">Instant Withdrawal</option>
        <option value="fixed_points">Fixed Points</option>
    </select>
</div>

    
                        <button type="submit" class="btn btn-primary" id="registrationSubmitBtn">
                            <span class="btn-text">Set Registration Points</span>
                            <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </form>
                </div>
            </div>
                            <div class="mt-4">
    <h6>Registration Bonus List</h6>
    <table class="table table-bordered table-striped">
       <thead>
    <tr>
        <th>ID</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Points</th>
        <th>Point Type</th>
        <th>Created At</th>
        <th>Actions</th>
    </tr>
</thead>

        <tbody>
            @forelse($registrationBonuses as $bonus)
                <tr>
                    <td>{{ $bonus->id }}</td>
                    <td>{{ $bonus->start_date }}</td>
                    <td>{{ $bonus->end_date }}</td>
                    <td>{{ $bonus->points }}</td>
    <td>{{ ucfirst(str_replace('_', ' ', $bonus->point_type)) }}</td>
                    <td>{{ $bonus->created_at }}</td>
                    <td>
                        <form action="{{ route('registration_bonus.destroy', $bonus->id) }}" method="POST" onsubmit="return confirm('Delete this entry?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">No data available.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date/time handling (removed automatic setting of default dates)
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    // Date validation
    if (startDateInput && endDateInput) {
        startDateInput.addEventListener('change', function() {
            if (endDateInput.value && this.value > endDateInput.value) {
                showMessage('registrationAlertContainer', 'warning', 'Start date cannot be after end date');
                this.value = '';
            }
        });

        endDateInput.addEventListener('change', function() {
            if (startDateInput.value && this.value < startDateInput.value) {
                showMessage('registrationAlertContainer', 'warning', 'End date cannot be before start date');
                this.value = '';
            }
        });
    }

    // Attach form submit handlers
    const referralForm = document.getElementById('referralForm');
    const registrationForm = document.getElementById('registrationForm');

    if (referralForm) {
        referralForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitReferralForm(this);
        });
    }

    if (registrationForm) {
        registrationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitRegistrationForm(this);
        });
    }
});

// Page reload function with delay for better UX
function reloadPageAfterDelay(delay = 1500) {
    setTimeout(() => {
        window.location.reload();
    }, delay);
}

// Loading state management functions
function setLoadingState(buttonId, isLoading) {
    const button = document.getElementById(buttonId);
    if (!button) return;
    
    const btnText = button.querySelector('.btn-text');
    const spinner = button.querySelector('.spinner-border');
    
    if (isLoading) {
        button.disabled = true;
        btnText.textContent = 'Processing...';
        spinner.classList.remove('d-none');
        button.classList.add('loading');
    } else {
        button.disabled = false;
        spinner.classList.add('d-none');
        button.classList.remove('loading');
        
        // Reset original text based on button ID
        if (buttonId === 'referralSubmitBtn') {
            btnText.textContent = 'Submit Referral Points';
        } else if (buttonId === 'registrationSubmitBtn') {
            btnText.textContent = 'Set Registration Points';
        }
    }
}

// Message display function
function showMessage(containerId, type, message) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    // Clear existing alerts
    container.innerHTML = '';
    container.appendChild(alertDiv);

    // Auto-dismiss after 4 seconds (only if page won't reload)
    setTimeout(() => {
        if (alertDiv && alertDiv.parentElement) {
            alertDiv.remove();
        }
    }, 4000);
}

// Submit Referral Form
function submitReferralForm(form) {
    // Start loading state
    setLoadingState('referralSubmitBtn', true);
    
    const formData = new FormData(form);
    const jsonData = Object.fromEntries(formData.entries());

    fetch(form.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(jsonData)
    })
    .then(async response => {
        let data;
        try {
            data = await response.json();
        } catch (e) {
            data = {success: false, message: "Invalid server response"};
        }

        if (response.ok) {
            if (data.success === true) {
                showMessage('referralAlertContainer', 'success', data.message || 'Points added successfully');
                // Reload page after successful submission
                reloadPageAfterDelay();
            } else {
                showMessage('referralAlertContainer', 'warning', data.message || 'Unexpected response from server');
                // End loading state for non-success responses
                setLoadingState('referralSubmitBtn', false);
            }
        } else {
            showMessage('referralAlertContainer', 'danger', data.message || 'Server error occurred');
            // End loading state for error responses
            setLoadingState('referralSubmitBtn', false);
        }
    })
    .catch(error => {
        showMessage('referralAlertContainer', 'danger', 'An error occurred while submitting referral points');
        console.error('Error:', error);
        // End loading state for catch errors
        setLoadingState('referralSubmitBtn', false);
    });
}

// Submit Registration Form
function submitRegistrationForm(form) {
    // Start loading state
    setLoadingState('registrationSubmitBtn', true);
    
    const startDate = new Date(form.querySelector('#start_date').value);
    const endDate = new Date(form.querySelector('#end_date').value);

    const formData = new FormData(form);
    formData.set('start_date', startDate.toISOString());
    formData.set('end_date', endDate.toISOString());

    const jsonData = Object.fromEntries(formData.entries());

    fetch(form.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(jsonData)
    })
    .then(async response => {
        let data;
        try {
            data = await response.json();
        } catch (e) {
            data = {success: false, message: "Invalid server response"};
        }

        if (response.ok) {
            if (data.success === true) {
                showMessage('registrationAlertContainer', 'success', data.message || 'Registration bonus rule created successfully');
                // Reload page after successful submission
                reloadPageAfterDelay();
            } else {
                showMessage('registrationAlertContainer', 'warning', data.message || 'Unexpected response from server');
                // End loading state for non-success responses
                setLoadingState('registrationSubmitBtn', false);
            }
        } else {
            showMessage('registrationAlertContainer', 'danger', data.message || 'Server error occurred');
            // End loading state for error responses
            setLoadingState('registrationSubmitBtn', false);
        }
    })
    .catch(error => {
        showMessage('registrationAlertContainer', 'danger', 'An error occurred while updating points settings');
        console.error('Error:', error);
        // End loading state for catch errors
        setLoadingState('registrationSubmitBtn', false);
    });
}
</script>

<style>
/* Optional: Add some custom styling for the loading state */
.btn.loading {
    position: relative;
}

.btn.loading:disabled {
    opacity: 0.8;
}

/* Ensure spinner aligns properly */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.1em;
}
</style>
@endpush
@endsection