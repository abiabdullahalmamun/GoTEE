<!-- Header Overlay -->
<div class="absolute top-0 left-0 w-full z-20">
    <div class="bg-primary-opt backdrop-blur-md text-white py-4 f-topbar-area">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
            <!-- Logo / Title -->
            <h1 class="text-2xl md:text-4xl font-bold"><a href="{{ URL::to('/') }}">{{ optional($menuList['metaInfo'])['comName'] ?? null }}</a></h1>

            <!-- User Info & Actions -->
            <div class="flex items-center space-x-3 relative">
      
                   
           
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownButton = document.getElementById('profileDropdownButton');
        const dropdownMenu = document.getElementById('profileDropdownMenu');
        const dropdownArrow = document.getElementById('dropdownArrow');

        let isOpen = false;

        dropdownButton.addEventListener('click', function(e) {
            e.stopPropagation();

            if (isOpen) {
                // Close the dropdown
                dropdownMenu.classList.add('hidden');
                dropdownMenu.classList.remove('opacity-100', 'scale-100');
                dropdownMenu.classList.add('opacity-0', 'scale-95');
                dropdownArrow.classList.remove('rotate-180');
            } else {
                // Open the dropdown
                dropdownMenu.classList.remove('hidden');
                setTimeout(() => {
                    dropdownMenu.classList.remove('opacity-0', 'scale-95');
                    dropdownMenu.classList.add('opacity-100', 'scale-100');
                }, 20);
                dropdownArrow.classList.add('rotate-180');
            }

            isOpen = !isOpen;
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            if (isOpen) {
                dropdownMenu.classList.add('hidden');
                dropdownMenu.classList.remove('opacity-100', 'scale-100');
                dropdownMenu.classList.add('opacity-0', 'scale-95');
                dropdownArrow.classList.remove('rotate-180');
                isOpen = false;
            }
        });
    });
</script>
