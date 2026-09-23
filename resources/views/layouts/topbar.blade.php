<header class="topbar">
    <div class="topbar-left flex items-center">
        <button class="menu-toggle"><i class="fas fa-bars"></i></button>
        <!-- Website Button -->
        <a title="Go to Website" href="{{ URL::to('/dashboard') }}" class="relative text-white transition duration-300
                   after:content-[''] after:absolute after:left-0 after:bottom-0 after:w-0 after:h-0.5
                   after:bg-white after:transition-all after:duration-300 hover:after:w-full">
            <i class="fas fa-globe"></i> <span class="hidden md:inline">GoTEE</span>
        </a>
        | <h2 class="logo font-serif font-bold text-2xl">{{ optional($menuList['metaInfo'])['comName'] ?? null }}-{{Auth::user()->center->center_name}}</h2>
    </div>



    <div class="topbar-right flex items-center">
        <div class="hidden md:flex flex-col justify-end mr-2 text-right">
            <span class="username font-bold">{{ Auth::user()->FullName }}</span>
            <span class="type text-sm">{{ Auth::user()?->role?->name }}</span>
        </div>

        <img src="{{ Auth::user()->avatar ?? asset('assets/images/meta/user_avatar.jpg') }}" alt="User Avatar" class="user-avatar">
    </div>
</header>
