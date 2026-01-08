@extends('layouts.app')

@section('content')
    <div class="page-title">
        <div class="d-flex gap-2 align-items-center">
            <i class="bi bi-gear-fill"></i> {{ __('Theme Colors Settings') }}
        </div>
    </div>
    <form action="{{ route('admin.themeColor.update') }}" method="POST">
        @csrf
        <div class="card mt-3">
            <div class="card-header py-3">
                <h5 class="m-0 card-title">
                    <i class="bi bi-palette-fill"></i>
                    {{ __('Current Color') }}
                </h5>
            </div>
            <div class="card-body d-flex justify-content-between gap-5 flex-wrap">

                <div class="d-flex align-items-center gap-5 mt-4 flex-wrap">
                    <div class="color-panel">
                        <span class="color-input" id="bgPrimary" style="background: {{ $primary }}"></span>
                        <p class="color" id="colorPrimary">
                            {{ $primary }}
                        </p>
                        <label for="primary_color" class="color-label">
                            {{ __('Primary') }}
                        </label>
                    </div>

                    <div class="color-panel">
                        <span class="color-input" id="bgSecondary" style="background: {{ $secondary }}"></span>
                        <p class="color" id="colorSecondary">
                            {{ $secondary }}
                        </p>
                        <label for="secondary_color" class="color-label">
                            {{ __('Secondary') }}
                        </label>
                    </div>

                </div>

                <div>
                    <button type="button" class="btn btn-primary py-3 px-3" data-bs-toggle="modal"
                        data-bs-target="#colorModal">
                        <i class="bi bi-palette-fill"></i>
                        {{ __('Change Color Palette') }}
                    </button>
                </div>

                <input type="hidden" name="primary_color" value="{{ $primary }}" id="primary_color" />
                <input type="hidden" name="secondary_color" value="{{ $secondary }}" id="secondary_color" />

            </div>
            @if (app()->environment('local'))
                @hasPermission('admin.themeColor.update')
                <div class="card-footer d-flex justify-content-start">
                    <button type="submit" class="btn btn-primary py-2 px-3">
                        {{ __('Save And Update') }}
                    </button>
                </div>
                @endhasPermission
            @endif
        </div>
    </form>

    <div class="mt-4">
        <h5 class="fw-bold">
            {{ __('Available Colors palette') }}
        </h5>

        <div class="d-flex gap-3 flex-wrap">
            @foreach ($themeColors as $themeColor)
                <div class="color-panel">
                    <button class="primary-color" style="background: {{ $themeColor->primary }};"
                        onclick="setColor('{{ $themeColor->primary }}', '{{ $themeColor->secondary }}')">
                        <div class="name"></div>
                        <span class="color">{{ $themeColor->primary }}</span>
                    </button>
                    <div class="variants">
                        @php
                            $shades = [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950];
                        @endphp
                        @foreach ($shades as $shade)
                            <div class="varint-color {{ $loop->index > 4 ? 'text-white' : '' }}"
                                style="background: {{ $themeColor['variant_' . $shade] }}">
                                {{ $shade }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

    </div>


    <form action="{{ route('admin.themeColor.change') }}" method="post">
        @csrf
        <div class="modal fade" id="colorModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="colorModalLabel">
                            {{ __('Change Color Palette') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <input type="text" id="generatedColorVariants" name="generated_color_variants" hidden>

                        <div class="mb-3">

                            <label for="primary_color" class="form-label">
                                {{ __('Select Your Primary Color') }}
                            </label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text p-0 h-100" style="border-radius: 0">
                                        <input type="color" id="colorPalette" name="primary_color" style="height: 100%"
                                            class="border" value="{{ $primary }}">
                                    </span>
                                </div>
                                <input type="text" class="form-control" placeholder="Enter hex code e.g. #EE456B"
                                    id="colorInput">
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" onclick="generateTailwindColors()">
                                Generate Colors
                            </button>
                        </div>

                        <div class="mx-auto mt-2" id="colorPanel" style="display: none">
                            <h5>{{ __('Color Variants') }}</h5>
                            <div id="colorVariants" class="mt-2 variants"></div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('Close') }}
                        </button>
                        @hasPermission('admin.themeColor.change')
                        <button type="submit" class="btn btn-primary">
                            {{ __('Save changes') }}
                        </button>
                        @endhasPermission
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('css')
    <style>
        .color-panel {
            width: 210px;
        }

        .color-panel .primary-color {
            width: 100%;
            border: none;
            padding: 22px 10px;
            cursor: pointer;
            text-transform: capitalize;
            text-align: left;
            color: #fff;
            border: 2px dotted transparent;
            transition: all 0.3s;
        }

        .color-panel .primary-color:hover {
            border: 2px dotted #ccc;
            box-shadow: 0 0 10px 5px rgba(0, 0, 0, 0.1);
            transform: scale(1.02);
        }

        .color-panel .primary-color .name {
            font-size: 16px;
            font-weight: 700;
        }

        .color-panel .variants {
            width: 100%;
            cursor: not-allowed;
        }

        .color-panel .varint-color {
            padding: 5px 10px;
            width: 100%;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function setColor(primary, secondary) {
            document.getElementById('primary_color').value = primary;
            document.getElementById('secondary_color').value = secondary;
            document.getElementById('bgPrimary').style.background = primary;
            document.getElementById('bgSecondary').style.background = secondary;
            document.getElementById('colorPrimary').innerHTML = primary;
            document.getElementById('colorSecondary').innerHTML = secondary;
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/chroma-js/2.1.0/chroma.min.js"></script>
    <script>
        $('#colorPalette').on('input', function() {
            $('#colorInput').val($(this).val());
        });

        $('#colorInput').on('input', function() {
            $('#colorPalette').val($(this).val());
        });

        function generateTailwindColors() {
            $('#colorPanel').show();
            const baseColor = document.getElementById('colorInput').value || '#a855f7';
            const colorVariants = generatePalette(baseColor);
            displayColorVariants(colorVariants);

            document.getElementById('generatedColorVariants').value = JSON.stringify(colorVariants);
        }

        function generatePalette(baseColor) {
            const palette = {};

            palette[50] = chroma.mix(baseColor, '#ffffff', 0.9, 'rgb').hex(); // Lighten 90%
            palette[100] = chroma.mix(baseColor, '#ffffff', 0.8, 'rgb').hex(); // Lighten 80%
            palette[200] = chroma.mix(baseColor, '#ffffff', 0.6, 'rgb').hex(); // Lighten 60%
            palette[300] = chroma.mix(baseColor, '#ffffff', 0.4, 'rgb').hex(); // Lighten 40%
            palette[400] = chroma.mix(baseColor, '#ffffff', 0.2, 'rgb').hex(); // Lighten 20%
            palette[500] = /^#/.test(baseColor) ? baseColor : `#${baseColor}`; // Base color
            palette[600] = chroma.mix(baseColor, '#000000', 0.1, 'rgb').hex(); // Darken 10%
            palette[700] = chroma.mix(baseColor, '#000000', 0.2, 'rgb').hex(); // Darken 20%
            palette[800] = chroma.mix(baseColor, '#000000', 0.3, 'rgb').hex(); // Darken 30%
            palette[900] = chroma.mix(baseColor, '#000000', 0.4, 'rgb').hex(); // Darken 40%
            palette[950] = chroma.mix(baseColor, '#000000', 0.5, 'rgb').hex(); // Darken 50%
            return palette;
        }

        function displayColorVariants(variants) {
            const colorContainer = document.getElementById('colorVariants');
            colorContainer.innerHTML = ''; // Clear previous variants

            for (const [shade, color] of Object.entries(variants)) {
                const colorDiv = document.createElement('div');
                colorDiv.style.backgroundColor = color;
                colorDiv.classList.add('varint-color');
                colorDiv.style.padding = '8px 10px';
                colorDiv.style.color = chroma(color).luminance() > 0.5 ? '#000' : '#fff';
                colorDiv.innerText = `${shade}: ${color}`;
                colorContainer.appendChild(colorDiv);
            }
        }
    </script>
@endpush
