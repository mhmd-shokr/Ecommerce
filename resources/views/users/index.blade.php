@extends('layouts.app')

@section('content')
<style>
.account-nav {
    list-style: none;
    padding: 0;
    margin: 0;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0px 4px 12px rgba(0,0,0,0.05);
    overflow: hidden;
}
.account-nav li a {
    display: block;
    padding: 12px 16px;
    color: #333;
    text-decoration: none;
    border-bottom: 1px solid #eee;
    transition: background 0.3s, color 0.3s;
}
.account-nav li a:hover,
.account-nav li a.active {
    background: #6a6e51;
    color: #fff;
}
.account-nav li:last-child a {
    border-bottom: none;
}
</style>

<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
      <h2 class="page-title">My Account</h2>
      <div class="row">
        <div class="col-lg-3">
         @include('users.account-nav')
        </div>
        <div class="col-lg-9">
          <div class="page-content my-account__dashboard">
            <p>Hello <strong>{{ Auth::user()->name }}</strong></p>
            <p>From your account dashboard you can view your 
               <a class="unerline-link" href="{{ route('users.orders') }}">recent orders</a>, 
               manage your shipping addresses, 
               and edit your password and account details.
            </p>
          </div>
        </div>
      </div>
    </section>
</main>
@endsection
