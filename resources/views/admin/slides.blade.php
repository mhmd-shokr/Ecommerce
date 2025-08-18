@extends('layouts.admin')

@section('content')
    <style>
        .link-btn {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            word-break: break-all;
        }
    </style>
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Slider</h3>
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
                        <div class="text-tiny">Slides</div>
                    </li>
                </ul>
            </div>

            <div class="wg-box">
                <div class="flex items-center justify-between gap10 flex-wrap">
                    <div class="wg-filter flex-grow">
                        <form class="form-search">
                            <fieldset class="name">
                                <input type="text" placeholder="Search here..." class="" name="name" tabindex="2" value=""
                                    aria-required="true" required="">
                            </fieldset>
                            <div class="button-submit">
                                <button class="" type="submit"><i class="icon-search"></i></button>
                            </div>
                        </form>
                    </div>
                    <a class="tf-button style-1 w208" href="{{ route('admin.addSlides') }}"><i class="icon-plus"></i>Add
                        new</a>
                </div>
                <div class="wg-table table-all-user">
                    <table class="table table-striped table-bordered table-responsive custom-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Tagline</th>
                                <th>Title</th>
                                <th>Subtitle</th>
                                <th>Link</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($slides as $slide)
                                <tr>
                                    <td>{{ $slide->id }}</td>
                                    <td class="pname">
                                        <div class="image">
                                            <img src="{{ asset('storage/' . $slide->image) }}" alt="{{ $slide->title }}"
                                                style="width:80px; height:60px; object-fit:cover; border-radius:6px;">
                                        </div>
                                    </td>
                                    <td>{{ $slide->tagline }}</td>
                                    <td>{{ $slide->title }}</td>
                                    <td>{{ $slide->subtitle }}</td>
                                    <td>
                                        <a href="{{ $slide->link }}" target="_blank" class="link-btn">
                                            {{ $slide->link }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="list-icon-function" style="display:flex; gap:10px; justify-content:center;">
                                            <a href="{{ route('admin.editSlide', $slide->id) }}">
                                                <div class="item edit" style="color:#07ff77; cursor:pointer;">
                                                    <i class="icon-edit-3"></i>
                                                </div>
                                            </a>
                                            <form action="{{ route('admin.deleteSlide',$slide->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="item delete"
                                                    style="background:none; border:none; color:#dc3545; cursor:pointer;">
                                                    <i class="icon-trash-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                    {{ $slides->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>









@endsection
@push('scripts')
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

    $(document).ready(function () {
        $('.item.delete').on('click', function (e) {
            e.preventDefault();
            var form = $(this).closest('form');

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this Slide",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

</script>

@endpush
