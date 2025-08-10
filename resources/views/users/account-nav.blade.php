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

.account-nav li a:hover {
    background: #6a6e51;
    color: #fff;
}

.account-nav li:last-child a {
    border-bottom: none;
}

</style>

<ul class="account-nav">
    <li><a href="{{ route('user.index') }}" class="menu-link menu-link_us-s">Dashboard</a></li>
    <li><a href="{{ route('users.orders') }}" class="menu-link menu-link_us-s">Orders</a></li>
    {{-- <li><a href="{{ route('user.addresses') }}" class="menu-link menu-link_us-s">Addresses</a></li>
    <li><a href="{{ route('user.details') }}" class="menu-link menu-link_us-s">Account Details</a></li>
    <li><a href="{{ route('user.wishlist') }}" class="menu-link menu-link_us-s">Wishlist</a></li> --}}
    <li>
        <a href="{{ route('logout') }}"
           class="menu-link menu-link_us-s"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </li>
</ul>

