@if ($plumbers->isNotEmpty())                        
<div class="modal fade" id="plumberModalEdit{{ $plumber->user_id }}" tabindex="-1" role="dialog" aria-labelledby="plumberModalEditLabel{{ $plumber->user_id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                    
                                <div class="modal-header">
                                    <h5 class="modal-title" id="plumberModalEditLabel{{ $plumber->user_id }}">Plumber Details (ID: {{ $plumber->user_id }})</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                        
                                <div class="modal-body">
<form id="save-changes-form" 
      method="post" 
      action="{{ route('admin.plumberUsers.update', $plumber->user_id) }}">
    @csrf
    @method('patch')
    
    <label>Name</label>
    <div class="input-group mb-3">
        <input type="text" name="name" value="{{ $plumber->user->name }}" 
               class="form-control" placeholder="Name" aria-label="Name" aria-describedby="name-addon">
    </div>
    
    <label>Phone</label>
    <div class="input-group mb-3">
        <input type="text" name="phone" value="{{ $plumber->user->phone }}" 
               class="form-control" placeholder="Phone" aria-label="Phone">
    </div>

    <label>Nationality ID</label>
    <div class="input-group mb-3">
        <input type="text" name="nationality_id" value="{{ $plumber->nationality_id }}" 
               class="form-control" placeholder="Nationality ID" aria-label="Nationality ID">
    </div>

    <label>Gift Points</label>
    <div class="input-group mb-3">
        <input type="number" name="gift_points" value="{{ $plumber->gift_points }}" 
               class="form-control" placeholder="Gift Points" aria-label="Gift Points">
    </div>

    <label>Fixed Points</label>
    <div class="input-group mb-3">
        <input type="number" name="fixed_points" value="{{ $plumber->fixed_points }}" 
               class="form-control" placeholder="Fixed Points" aria-label="Fixed Points">
    </div>

    <label>Instant Withdrawal</label>
    <div class="input-group mb-3">
        <input type="number" name="instant_withdrawal" value="{{ $plumber->instant_withdrawal }}" 
               class="form-control" placeholder="Instant Withdrawal" aria-label="Instant Withdrawal">
    </div>
    
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success save-changes-button">Save Changes</button>
    </div>
</form>
                                </div>
                       
                            </div>
                        </div>
                    </div>
                                        @else
                                                <div class="text-center">
        <p>No plumbers found with the selected filters. Please adjust the filter criteria.</p>
    </div>
@endif
                                        
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Save Changes button handler
        document.querySelector('.save-changes-button').addEventListener('click', function () {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You are about to save changes to this plumber\'s details.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save changes!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('save-changes-form').submit();
                }
            });
        });
    });
</script>