@extends('layouts.admin')

@section('content')

<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Brand infomation</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{ route('admin.brands') }}">
                        <div class="text-tiny">Brands</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{ route('admin.addBrand') }}">
                        <div class="text-tiny">New Brand</div>
                    </a>
                    
                </li>
            </ul>
        </div>
        <div class="wg-table table-all-user">
            <div class="table-responsive">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        
        <!-- new-category -->
        <div class="wg-box">
            <form class="form-new-product form-style-1" action="{{ route('admin.store') }}" method="POST"
                enctype="multipart/form-data">
              @csrf

                <fieldset class="name">
                    <div class="body-title">Brand Name <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Brand name" name="name"
                        tabindex="0" value="{{ old('name') }}" aria-required="true" required="">
                </fieldset>
                @error('name')
                <span class="alert alert-danger text-center">{{ $message }}</span>
                @enderror

                <fieldset class="name">
                    <div class="body-title">Brand Slug <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Brand Slug" name="slug"
                    tabindex="0" value="{{ old('slug') }} " aria-required="true" required="">
                </fieldset>
                @error('slug')
                <span class="alert alert-danger text-center">{{ $message }}</span>
                @enderror

                <fieldset>
                    <div class="body-title">Upload images <span class="tf-color-1">*</span>
                    </div>
                    <div class="upload-image flex-grow">
                        <div class="item" id="imgpreview" style="display:none">
                            <img src="" class="effect8" alt="">
                        </div>
                        <div id="upload-file" class="item up-load">
                            <label class="uploadfile" for="myFile">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="body-text">Drop your images here or select <span
                                        class="tf-color">click to browse</span></span>
                                <input type="file" id="myFile" name="image" accept="image/*">
                            </label>
                        </div>
                    </div>
                </fieldset>
                @error('image')
                <span class="alert alert-danger text-center">{{ $message }}</span>
                @enderror
                
                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(function(){
        // When the user changes the file in the input with id 'myFile'
        $('#myFile').on('change', function(e) {
            // Get the selected file (if any)
            const [file] = this.files;

            if (file) {
                // Change the src attribute of the preview image to show the selected file
                $("#imgpreview img").attr('src', URL.createObjectURL(file));
                // Show the preview container if it was hidden
                $("#imgpreview").show();
            }
        });

        // When the user changes the text in the input with name 'name'
        $("input[name='name']").on("change", function() {
            // Use the StringToSlug function to convert the text into a URL-friendly slug
            // and set this slug as the value of the input with name 'slug'
            $("input[name='slug']").val(StringToSlug($(this).val()));
        });
    });

    // Function to convert normal text into a URL-friendly slug
    function StringToSlug(Text) {
        return Text.toLowerCase()                    // Convert to lowercase
            .replace(/[^\w\s-]/g, '')               // Remove all characters except letters, numbers, spaces, and hyphens
            .trim()                                // Remove whitespace from start and end
            .replace(/[\s_-]+/g, '-')             // Replace spaces, underscores, and multiple hyphens with a single hyphen
            .replace(/^-+|-+$/g, '');            // Remove hyphens from start and end
    }
</script>

@endpush
