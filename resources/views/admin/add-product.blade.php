@extends('layouts.admin')


@section('content')

<div class="main-content-inner">
    <!-- main-content-wrap -->
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Add Product</h3>
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
                    <a href="{{ route("admin.products") }}">
                        <div class="text-tiny">Products</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{ route("admin.addProducts") }}">
                    <div class="text-tiny">Add product</div>
                    </a>
                </li>
            </ul>
        </div>
        @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        <!-- form-add-product -->
        <form class="tf-section-2 form-add-product" method="POST" enctype="multipart/form-data"
            action="{{ route('admin.storeProducts') }}">
        @csrf
            <div class="wg-box">
                <fieldset class="name">
                    <div class="body-title mb-10">Product name <span class="tf-color-1">*</span>
                    </div>
                    <input class="mb-10" type="text" placeholder="Enter product name"
                        name="name" tabindex="0" value="{{ old('name') }}" aria-required="true" required="">
                        @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                        <div class="text-tiny">Do not exceed 100 characters when entering the
                        product name.</div>
                </fieldset>

                <fieldset class="name">
                    <div class="body-title mb-10">Slug <span class="tf-color-1">*</span></div>
                    <input class="mb-10" type="text" placeholder="Enter product slug"
                        name="slug" tabindex="0" value="{{ old('slug') }}" aria-required="true" required="">
                    
                        @error('slug')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                        <div class="text-tiny">Do not exceed 100 characters when entering the
                        product name.</div>
                </fieldset>

                <div class="gap22 cols">
                    <fieldset class="category">
                        <div class="body-title mb-10">Category <span class="tf-color-1">*</span>
                        </div>
                        <div class="select">
                            <select class="" name="category_id">
                                <option disabled selected>Choose category</option>
                                @foreach ($categories as $category )
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror

                        </div>
                    </fieldset>
                    <fieldset class="brand">
                        <div class="body-title mb-10">Brand <span class="tf-color-1">*</span>
                        </div>
                        <div class="select">
                            <select class="" name="brand_id">
                                <option disabled selected >Choose Brand</option>
                                @foreach ($brands as $brand )
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                            @error('brand_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                        </div>
                    </fieldset>
                </div>

                <fieldset class="shortdescription">
                    <div class="body-title mb-10">Short Description <span
                            class="tf-color-1">*</span></div>
                    <textarea class="mb-10 ht-150" name="short_description"
                        placeholder="Short Description" tabindex="0" aria-required="true"
                        >{{ old('short_description') }}</textarea>
                        @error('short_description')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                        <div class="text-tiny">Do not exceed 100 characters when entering the
                        product name.</div>
                </fieldset>

                <fieldset class="description">
                    <div class="body-title mb-10">Description <span class="tf-color-1">*</span>
                    </div>
                    <textarea class="mb-10" name="description" placeholder="Description"
                        tabindex="0" aria-required="true" required="">{{ old('description') }}</textarea>
                        @error('description')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                        <div class="text-tiny">Do not exceed 100 characters when entering the
                        product name.</div>
                </fieldset>
            </div>
            <div class="wg-box">
                <fieldset>
                    <div class="body-title">Upload images <span class="tf-color-1">*</span>
                    </div>
                    <div class="upload-image flex-grow">
                        <div class="item" id="imgpreview" style="display:none">
                            <img src="#"
                                class="effect8" alt="">
                        </div>
                        <div id="upload-file" class="item up-load">
                            <label class="uploadfile" for="myFile">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="body-text">Drop your images here or select <span
                                        class="tf-color">click to browse</span></span>
                                <input type="file" id="myFile" name="image" accept="image/*">
                                @error('image')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            </label>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <div class="body-title mb-10">Upload Gallery Images</div>
                    <div class="upload-image mb-16">
                        <!-- <div class="item">
                            <img src="images/upload/upload-1.png" alt="">
                        </div> -->
                        <div id="galUpload" class="item up-load">
                            <label class="uploadfile" for="gFile">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="text-tiny">Drop your images here or select <span
                                        class="tf-color">click to browse</span></span>
                                <input type="file" id="gFile" name="images[]" accept="image/*"
                                    multiple="" >
                                    @error('images')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror

                            </label>
                        </div>
                        <div id="galleryPreview" class="d-flex flex-wrap mt-3"></div>

                    </div>
                </fieldset>

                <div class="cols gap22">
                    <fieldset class="name">
                        <div class="body-title mb-10">Regular Price <span
                                class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Enter regular price"
                            name="regular_price" tabindex="0" value="{{ old('regular_price') }}" aria-required="true"
                            required="">
                    </fieldset>
                    <fieldset class="name">
                        <div class="body-title mb-10">Sale Price <span
                                class="tf-color-1">*</span></div>
                        <input class="mb-10" type="text" placeholder="Enter sale price"
                            name="sale_price" tabindex="0" value="{{ old('sale_price') }}" aria-required="true"
                        >
                    </fieldset>
                </div>


                <div class="cols gap22">
                    <fieldset class="name">
                        <div class="body-title mb-10">SKU <span class="tf-color-1">*</span>
                        </div>
                        <input class="mb-10" type="text" placeholder="Enter SKU" name="SKU"
                            tabindex="0" value="{{ old('SKU') }}" aria-required="true" required="">
                    </fieldset>
                    <fieldset class="name">
                        <div class="body-title mb-10">Quantity <span class="tf-color-1">*</span>
                        </div>
                        <input class="mb-10" type="text" placeholder="Enter quantity"
                            name="quantity" tabindex="0" value="{{ old('quantity') }}" aria-required="true"
                            required="">
                    </fieldset>
                </div>

                <div class="cols gap22">
                    <fieldset class="name">
                        <div class="body-title mb-10">Stock</div>
                        <div class="select mb-10">
                            <select class="" name="stock_status">
                                <option value="instock">InStock</option>
                                <option value="outofstock">Out of Stock</option>
                            </select>
                        </div>
                    </fieldset>
                    <fieldset class="name">
                        <div class="body-title mb-10">Featured</div>
                        <div class="select mb-10">
                            <select class="" name="featured">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </fieldset>
                </div>
                <div class="cols gap10">
                    <button class="tf-button w-full" type="submit">Add product</button>
                </div>
            </div>
        </form>
        <!-- /form-add-product -->
    </div>
    <!-- /main-content-wrap -->
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

        $('#gFile').on('change', function(e) {
    const gphotos = this.files;

    // امسح الصور القديمة قبل عرض الجديدة
    $("#galleryPreview").html('');

    $.each(gphotos, function(i, file) {
        const imgURL = URL.createObjectURL(file);
        const preview = `
            <div class="item gitems">
                <img src="${imgURL}" class="effect8" style="width: 100px; height: 100px; object-fit: cover; margin: 5px;" alt="">
            </div>
        `;
        $("#galleryPreview").append(preview);
    });
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
