@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2>Complete Your Profile</h2>
    <form method="POST" action="{{ route('profile.complete.save') }}">
        @csrf

        <div class="mb-3">
            <label for="mobile" class="form-label">Mobile Number</label>
            <input type="text" name="mobile" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
