@extends('layouts.app')

@section('content')
    <div class="page-title">
        <div class="d-flex gap-2 align-items-center">
            <i class="fa-solid fa-user"></i> {{ __('Create New') }}
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-10 mx-auto">
            <form action="{{ route('admin.employee.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <!--######## User Information ##########-->
                <div class="card mt-3">
                    <div class="card-body">

                        <div class="d-flex gap-2 border-bottom pb-2">
                            <i class="fa-solid fa-user"></i>
                            <h5>
                                {{ __('User Information') }}
                            </h5>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mt-3">
                                            <x-input label="First Name" name="name" type="text"
                                                placeholder="Enter Name" required="true"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mt-3">
                                            <x-input label="Last Name" name="last_name" type="text"
                                                placeholder="Enter Name" />
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <x-input label="Phone Number" name="phone" type="number" placeholder="Enter phone number" required="true" />
                                </div>

                                <div class="mt-3">
                                    <x-select label="Gender" name="gender">
                                        <option value="male">{{ __('Male') }}</option>
                                        <option value="female">{{ __('Female') }}</option>
                                    </x-select>
                                </div>
                                <div class="mt-3">
                                    <x-input type="email" name="email" label="Email"
                                        placeholder="Enter Email Address" required="true"/>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mt-3 d-flex align-items-center justify-content-center">
                                    <div class="ratio1x1">
                                        <img id="previewProfile" src="https://placehold.co/500x500/png" alt=""
                                            width="100%">
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <x-file name="profile_photo" label="User profile (Ratio 1:1)"
                                        preview="previewProfile" />
                                </div>

                                <div class="mt-3">
                                    <x-select label="Role" name="role" required="true">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}">
                                                {{ __($role->name) }}
                                            </option>
                                        @endforeach
                                    </x-select>
                                </div>
                            </div>

                            <div class="col-lg-6 mt-3">
                                <x-input type="text" name="password" label="Password" placeholder="Enter Password" required="true"/>
                            </div>

                            <div class="col-lg-6 mt-3">
                                <x-input type="text" name="password_confirmation" label="Confirm Password" placeholder="Enter Confirm Password" required="true"/>
                            </div>

                            <!-- Envoy Specific Fields -->
                            <div id="envoyFields" style="display: none;" class="row">
                                <div class="col-lg-6 mt-3">
                                    <x-input type="number" name="weight" label="Weight" placeholder="Enter Weight" />
                                </div>
                                <div class="col-lg-6 mt-3">
                                    <x-input type="number" name="target" label="Target" placeholder="Enter Target" />
                                </div>
                                <div class="col-lg-6 mt-3">
                                    <x-input type="number" name="salary" label="Salary" placeholder="Enter Salary" />
                                </div>
                                <div class="col-lg-6 mt-3">
                                    <x-input type="text" name="region" label="Region" placeholder="Enter Region" />
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer py-3">
                        <button class="btn btn-primary px-5 py-2.5" type="submit">
                            {{ __('Submit') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            function toggleEnvoyFields() {
                if ($('select[name="role"]').val() === 'envoy') {
                    $('#envoyFields').show();
                } else {
                    $('#envoyFields').hide();
                }
            }

            $('select[name="role"]').on('change', function() {
                toggleEnvoyFields();
            });

            // Initial check
            toggleEnvoyFields();
        });
    </script>
@endpush
