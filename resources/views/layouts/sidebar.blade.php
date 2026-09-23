<!-- Primary Sidebar (Main Menu) -->
<aside class="sidebar">
    <nav class="sidebar-menu">
        <ul>
            @foreach($menuList['master'] as $item)
                <li>
                    <a href="{{ $item['url'] }}" class="menu-item" data-menu="{{ $item['id'] }}" data-title="{{ $item['title'] }}" title="{{ $item['url'] }}">
                        <i class="{{ 'fas '.$item['icon'] }}"></i><br>
                        <span>{{ $item['name'] }}</span>
                    </a>
                   <!--  <a href="{{ $item['url'] }}" 
                       class="menu-item" 
                       data-menu="{{ $item['id'] }}" 
                       data-title="{{ $item['title'] }}" 
                       title="{{ $item['url'] }}">

                        <i class="{{ 'fas '.$item['icon'] }}"></i>
                        <span>{{ $item['name'] }}</span>
                    </a> -->
                </li>
            @endforeach
        </ul>
    </nav>



    <!-- Logout Button -->
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" id="logout-btn" title="Logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</aside>


<!-- Secondary Sidebar -->
<aside class="detail-sidebar">
    <div class="detail-content">
        <h3 id="menu-title">Menu</h3>
        <ul id="child-menu"></ul>
    </div>
</aside>
