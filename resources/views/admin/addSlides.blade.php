@extends('layouts.admin')
@section('content')

    <div class="main-content-inner">
        <!-- main-content-wrap -->
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Slide</h3>
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
                        <a href="{{ route('admin.slides') }}">
                            <div class="text-tiny">Slides</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">New Slide</div>
                    </li>
                </ul>
            </div>
            @if (Session::has('success'))
                <p class="alert alert-success">{{ Session::get('success') }}</p>

            @endif
            <!-- new-category -->
            <div class="wg-box">
                <form class="form-new-product form-style-1" action="{{ route('admin.storeSlide') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <fieldset class="tagline">
                        <div class="body-title">TagLine <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Tagline" name="tagline" tabindex="0"
                            value="{{ old('tagline') }}" aria-required="true" required="">
                    </fieldset>
                    @error('tagline')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror

                    <fieldset class="title">
                        <div class="body-title">Title <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="title" name="title" tabindex="0"
                            value="{{ old('title') }}" aria-required="true" required="">
                    </fieldset>
                    @error('title')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <fieldset class="subtitle">
                        <div class="body-title">SubTitle <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="subtitle" name="subtitle" tabindex="0"
                            value="{{ old('subtitle') }}" aria-required="true" required="">
                    </fieldset>
                    @error('subtitle')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <fieldset class="link">
                        <div class="body-title">Link <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="link" name="link" tabindex="0"
                            value="{{ old('link') }}" aria-required="true" required="">
                    </fieldset>
                    @error('link')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <fieldset>
                        <div class="body-title">Upload images <span class="tf-color-1">*</span>
                        </div>
                        <div class="upload-image flex-grow">
                            <div class="item up-load">
                                <label class="uploadfile" for="myFile">
                                    <span class="icon">
                                        <i class="icon-upload-cloud"></i>
                                    </span>
                                    <span class="body-text">Drop your images here or select <span class="tf-color">click to
                                            browse</span></span>
                                    <input type="file" id="myFile" name="image" accept="image/*">
                                </label>
                            </div>
                        </div>
                        {{-- Display image when selected --}}

                        <div id="preview" style="margin-top:10px;">
                            <img id="previewImg" src="" alt="Preview"
                                style="max-width: 200px; display:none; border:1px solid #ccc; padding:5px; border-radius:8px;">
                        </div>
                    </fieldset>



                    @error('image')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <fieldset class="category">
                        <div class="body-title">Status</div>
                        <div class="select flex-grow">
                            <select class="" name="status">
                                <option value="" disabled selected>Select status</option>
                                <option value="1" @if(old('status') == '1') selected @endif>Active</option>
                                <option value="0" @if(old('status') == '0') selected @endif>Inactive</option>
                            </select>
                        </div>
                    </fieldset>
                    @error('status')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <div class="bot">
                        <div></div>
                        <button class="tf-button w208" type="submit">Save</button>
                    </div>
                </form>
            </div>
            <!-- /new-category -->
        </div>
        <!-- /main-content-wrap -->
    </div>

@endsection


@push('scripts')
    <script>
        document.getElementById('myFile').addEventListener('change', function (e) {
            const file = e.target.files[0];
            const previewImg = document.getElementById("previewImg");

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = "block";
                };
                reader.readAsDataURL(file);
            } else {
                previewImg.style.display = "none";
            }
        });
    </script>
@endpush