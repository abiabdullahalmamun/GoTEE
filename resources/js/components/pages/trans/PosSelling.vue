<template>
    <div class=" bg-gray-50 overflow-hidden font-sans flex flex-col">
        <!-- Main Layout -->
        <div class="flex flex-col lg:flex-row flex-1 p-4 md:p-6 gap-4 md:gap-6 overflow-hidden">
            <!-- Left Section - Products & Cart -->
            <div class="flex-1 bg-white rounded-2xl shadow-sm p-4 md:p-6 flex flex-col border border-gray-100 overflow-hidden">
                <!-- Top Controls -->
                <div class="flex flex-col md:flex-row gap-4 mb-6 flex-shrink-0">
                    <!-- Barcode Scanner -->
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Scan Barcode</label>
                        <div class="relative">
                            <input
                                type="text"
                                v-model="barcodeInput"
                                @keyup.enter="handleBarcodeScan"
                                placeholder="Scan or enter barcode"
                                class="w-full px-4 py-3 pl-12 border border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 transition-all"
                                ref="barcodeInput"
                            >
                            <div class="absolute left-3 top-3 text-gray-400">
                                <i class="fas fa-barcode text-xl"></i>
                            </div>
                            <button
                                @click="handleBarcodeScan"
                                class="absolute right-3 top-3 bg-blue-600 text-white p-1 rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Date Picker -->
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sale Date</label>
                        <div class="relative">
                            <input
                                type="date"
                                v-model="saleDate"
                                class="w-full px-4 py-3 pl-10 border border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 transition-all"
                            >
                            <div class="absolute left-3 top-3 text-gray-400">
                                <i class="far fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Browse Items Button -->
                    <div class="">
                        <div class="mt-6"></div>
                        <button
                            @click="showItemModal = true"
                            class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white rounded-xl flex items-center justify-center gap-2 transition-all shadow-md hover:shadow-lg"
                        >
                            <i class="fas fa-layer-group text-lg"></i>
                            <span class="font-medium">Browse Items</span>
                        </button>
                    </div>
                </div>

                <!-- Cart Items -->
                <div class="flex flex-col" style="max-height: calc(100vh - 430px);">
                    <div class="border-b border-gray-200 pb-3 mb-4 flex justify-between items-center flex-shrink-0">
                        <h3 class="text-lg font-semibold text-gray-900">Cart Items ({{ cartItems.length }})</h3>
                        <span class="text-sm font-medium text-blue-600">Total: ৳ {{ total.toFixed(2) }}</span>
                    </div>

                    <div class="overflow-y-auto flex-1">
                        <div v-if="cartItems.length === 0" class="h-full flex flex-col items-center justify-center text-gray-400 p-8">
                            <div class="relative mb-6">
                                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-shopping-cart text-3xl"></i>
                                </div>
                                <div class="absolute -right-2 -bottom-2 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-plus text-blue-600"></i>
                                </div>
                            </div>
                            <p class="text-lg font-medium mb-1">Your cart is empty</p>
                            <p class="text-sm text-center max-w-xs">Scan items or browse our inventory to get started</p>
                        </div>

                        <div v-else class="space-y-3 pr-2">
                            <div
                                v-for="(item, index) in cartItems"
                                :key="item.id"
                                class="group p-4 bg-white rounded-xl border border-gray-100 hover:border-blue-100 hover:shadow-sm transition-all relative"
                            >
                                <div class="flex justify-between items-start gap-4">
                                    <!-- Item Info -->
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-900 truncate">{{ item.name }}</h4>
                                        <div class="flex flex-wrap gap-x-3 gap-y-1 mt-1 text-xs text-gray-500">
                                            <span>#{{ item.code }}</span>
                                            <span class="px-2 py-0.5 bg-gray-100 rounded-full">{{ item.category }}</span>
                                            <span>{{ item.brand }}</span>
                                        </div>

                                        <!-- Price -->
                                        <div class="mt-3 flex items-center gap-2">
                                            <span class="text-sm font-medium text-gray-700">৳ {{ item.price.toFixed(2) }}</span>
                                            <span class="text-xs text-gray-400">x</span>

                                            <!-- Quantity Controls -->
                                            <div class="flex items-center gap-1 bg-gray-50 rounded-lg p-0.5">
                                                <button
                                                    @click="decreaseQty(index)"
                                                    class="w-6 h-6 flex items-center justify-center bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors"
                                                >
                                                    <i class="fas fa-minus text-xs"></i>
                                                </button>

                                                <span class="w-8 text-center text-sm font-medium">{{ item.qty }}</span>

                                                <button
                                                    @click="increaseQty(index)"
                                                    class="w-6 h-6 flex items-center justify-center bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors"
                                                >
                                                    <i class="fas fa-plus text-xs"></i>
                                                </button>
                                            </div>

                                            <span class="ml-auto text-sm font-semibold text-blue-600">
                                ৳ {{ (item.price * item.qty).toFixed(2) }}
                            </span>
                                        </div>
                                    </div>

                                    <!-- Item Image Placeholder -->
                                    <div class="flex-shrink-0 w-16 h-16 bg-gray-100 rounded-lg overflow-hidden">
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <i class="fas fa-cube text-xl"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Remove Button -->
                                <button
                                    @click="removeItem(index)"
                                    class="absolute -top-2 -right-2 w-6 h-6 bg-red-100 text-red-600 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-sm hover:bg-red-200"
                                >
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reset Cart Button -->
                <button
                    @click="resetCart"
                    class="mt-4 px-4 py-3 border border-gray-200 bg-white text-gray-700 rounded-xl flex items-center justify-center gap-2 hover:bg-gray-50 transition-all font-medium flex-shrink-0"
                    :class="{'opacity-50 cursor-not-allowed': cartItems.length === 0}"
                    :disabled="cartItems.length === 0"
                >
                    <i class="fas fa-trash-alt"></i>
                    <span>Clear Cart</span>
                </button>
            </div>

            <!-- Right Section - Customer & Payment -->
            <div class="w-full lg:w-96 space-y-4 md:space-y-6 flex flex-col overflow-hidden">
                <!-- Customer Section -->
                <div class="bg-white rounded-2xl shadow-sm p-4 md:p-6 border border-gray-100 flex-shrink-0">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h3>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search Customer</label>
                        <div class="relative">
                            <input
                                type="text"
                                v-model="customerPhone"
                                @input="searchCustomer"
                                placeholder="Phone number or name"
                                class="w-full px-4 py-3 pl-12 border border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 transition-all"
                            >
                            <div class="absolute left-3 top-3 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                            <div class="absolute right-3 top-3 text-gray-400">
                                <i class="fas fa-user-plus cursor-pointer hover:text-blue-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div v-if="customer" class="p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl border border-blue-200">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 relative">
                                <div class="h-14 w-14 rounded-xl bg-white border-2 border-white shadow-sm overflow-hidden">
                                    <img
                                        v-if="customer.image"
                                        :src="customer.image"
                                        alt="Customer"
                                        class="h-full w-full object-cover"
                                    >
                                    <div v-else class="h-full w-full bg-blue-100 flex items-center justify-center text-blue-600">
                                        <i class="fas fa-user text-xl"></i>
                                    </div>
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-400 rounded-full border-2 border-white"></div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-900 truncate">{{ customer.name }}</h4>
                                <p class="text-sm text-gray-600 truncate mt-1">
                                    <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>
                                    {{ customer.address }}
                                </p>
                                <div class="flex items-center gap-3 mt-2">
                  <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full">
                    <i class="fas fa-star mr-1"></i> Premium
                  </span>
                                    <span class="text-xs text-gray-500">
                    <i class="far fa-calendar-alt mr-1"></i> {{ customer.memberSince }}
                  </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="p-6 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        <i class="fas fa-user-circle text-3xl text-gray-300 mb-2"></i>
                        <p class="text-sm text-gray-500">No customer selected</p>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="bg-white rounded-2xl shadow-sm p-4 md:p-6 border border-gray-100 flex-1 flex flex-col overflow-hidden">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Summary</h3>

                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Subtotal</span>
                            <span class="text-sm font-medium">৳ {{ subtotal.toFixed(2) }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Tax (10%)</span>
                            <span class="text-sm font-medium">৳ {{ tax.toFixed(2) }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Discount</span>
                            <div class="flex items-center gap-2">
                                <button class="text-xs text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <span class="text-sm font-medium text-red-600">-৳ {{ discount.toFixed(2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="py-3 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold">Total</span>
                            <span class="text-xl font-bold text-blue-600">৳ {{ total.toFixed(2) }}</span>
                        </div>
                    </div>

                    <div class="mt-auto pt-4 grid grid-cols-2 gap-3">
                        <button
                            class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-xl flex items-center justify-center gap-2 transition-all font-medium"
                        >
                            <i class="fas fa-save"></i>
                            <span>Save</span>
                        </button>

                        <button
                            class="px-4 py-3 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white rounded-xl flex items-center justify-center gap-2 transition-all shadow-md hover:shadow-lg font-medium"
                        >
                            <i class="fas fa-print"></i>
                            <span>Print Receipt</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Item Modal -->
        <div
            v-if="showItemModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 backdrop-blur-sm"
        >
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden border border-gray-200">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-xl font-semibold text-gray-900">Select Products</h3>
                    <button
                        @click="showItemModal = false"
                        class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-200 transition-colors"
                    >
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Search -->
                <div class="p-4 border-b border-gray-200 bg-white sticky top-0">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-search"></i>
                        </div>
                        <input
                            type="text"
                            v-model="itemSearch"
                            placeholder="Search by product name, code, or category..."
                            class="block w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 transition-all"
                            autofocus
                        >
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400">
                            <span class="text-sm">{{ filteredItems.length }} items</span>
                        </div>
                    </div>
                </div>

                <!-- Item List -->
                <div class="flex-1 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="item in filteredItems" :key="item.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 mr-3">
                                        <i class="fas fa-cube"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ item.name }}</div>
                                        <div class="text-xs text-gray-500">{{ item.brand }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">#{{ item.code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">{{ item.category }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <span :class="{'text-green-600 font-medium': item.stock > 10, 'text-orange-600 font-medium': item.stock <= 10}">
                    {{ item.stock }} in stock
                  </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">৳ {{ item.price.toFixed(2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button
                                    @click="addToCart(item)"
                                    class="text-blue-600 hover:text-blue-900 px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 transition-colors"
                                >
                                    <i class="fas fa-plus-circle mr-1"></i> Add
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <div v-if="filteredItems.length === 0" class="p-8 text-center text-gray-500">
                        <i class="fas fa-search fa-2x mb-4 opacity-30"></i>
                        <p class="font-medium">No products found</p>
                        <p class="text-sm mt-1">Try adjusting your search query</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            saleDate: new Date().toISOString().substr(0, 10),
            customerPhone: '',
            customer: null,
            showItemModal: false,
            itemSearch: '',
            barcodeInput: '',
            cartItems: [],
            items: [
                { id: 1, name: 'Wireless Mouse Pro', code: 'WM001', category: 'Electronics', brand: 'Logitech', stock: 15, price: 24.99 },
                { id: 2, name: 'Mechanical Keyboard RGB', code: 'MK002', category: 'Electronics', brand: 'Corsair', stock: 8, price: 89.99 },
                { id: 3, name: 'Noise Cancelling Headphones', code: 'BH003', category: 'Electronics', brand: 'Sony', stock: 12, price: 129.99 },
                { id: 4, name: 'Premium USB-C Cable', code: 'UC004', category: 'Accessories', brand: 'Anker', stock: 32, price: 12.99 },
                { id: 5, name: 'Executive Notebook', code: 'NB005', category: 'Stationery', brand: 'Moleskine', stock: 20, price: 9.99 },
                { id: 6, name: 'LED Desk Lamp', code: 'DL006', category: 'Furniture', brand: 'IKEA', stock: 5, price: 29.99 },
                { id: 7, name: 'Wireless Charger', code: 'WC007', category: 'Electronics', brand: 'Belkin', stock: 18, price: 19.99 },
                { id: 8, name: 'Bluetooth Speaker', code: 'BS008', category: 'Electronics', brand: 'JBL', stock: 7, price: 59.99 },
            ],
            discount: 5.00,
        }
    },
    computed: {
        filteredItems() {
            return this.items.filter(item =>
                item.name.toLowerCase().includes(this.itemSearch.toLowerCase()) ||
                item.code.toLowerCase().includes(this.itemSearch.toLowerCase()) ||
                item.category.toLowerCase().includes(this.itemSearch.toLowerCase())
            );
        },
        subtotal() {
            return this.cartItems.reduce((sum, item) => sum + (item.price * item.qty), 0);
        },
        tax() {
            return this.subtotal * 0.1;
        },
        total() {
            return this.subtotal + this.tax - this.discount;
        }
    },
    methods: {
        handleBarcodeScan() {
            if (this.barcodeInput) {
                const item = this.items.find(i => i.code === this.barcodeInput);
                if (item) {
                    this.addToCart(item);
                    this.$notify({
                        title: 'Item Added',
                        text: `${item.name} added to cart`,
                        type: 'success'
                    });
                } else {
                    this.$notify({
                        title: 'Not Found',
                        text: 'No product found with this barcode',
                        type: 'error'
                    });
                }
                this.barcodeInput = '';
                this.$refs.barcodeInput.focus();
            }
        },
        searchCustomer() {
            // Simulate customer search
            if (this.customerPhone === '1234567890') {
                this.customer = {
                    name: 'John Doe',
                    phone: '1234567890',
                    address: '123 Main St, New York, NY 10001',
                    image: 'https://randomuser.me/api/portraits/men/1.jpg',
                    memberSince: '2020-05-15'
                };
            } else {
                this.customer = null;
            }
        },
        addToCart(item) {
            const existingItem = this.cartItems.find(cartItem => cartItem.id === item.id);
            if (existingItem) {
                existingItem.qty += 1;
            } else {
                this.cartItems.push({
                    ...item,
                    qty: 1
                });
            }

            // Close modal if open
            this.showItemModal = false;
        },
        removeItem(index) {
            this.cartItems.splice(index, 1);
        },
        increaseQty(index) {
            this.cartItems[index].qty += 1;
        },
        decreaseQty(index) {
            if (this.cartItems[index].qty > 1) {
                this.cartItems[index].qty -= 1;
            } else {
                this.removeItem(index);
            }
        },
        resetCart() {
            this.cartItems = [];
        }
    },
    mounted() {
        this.$refs.barcodeInput.focus();
    }
}
</script>
