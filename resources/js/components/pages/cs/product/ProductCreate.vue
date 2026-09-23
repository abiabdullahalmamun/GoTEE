<template>
    <div class="min-h-screen  p-4 md:p-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <p class="text-gray-500">Add a new product to your inventory</p>
            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Basic Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            Basic Information
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
                                <input
                                    v-model="product.name"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter product name"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Product Code *</label>
                                <input
                                    v-model="product.code"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter product code"
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <select
                                    v-model="product.prod_cat_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">Select Category</option>
                                    <option v-for="category in categories" :value="category.id" :key="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Product Type</label>
                                <select
                                    v-model="product.prod_type_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">Select Type</option>
                                    <option v-for="type in productTypes" :value="type.id" :key="type.id">
                                        {{ type.name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sub Group</label>
                                <select
                                    v-model="product.prod_sub_group_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">Select Sub Group</option>
                                    <option v-for="subGroup in subGroups" :value="subGroup.id" :key="subGroup.id">
                                        {{ subGroup.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Unit of Measure</label>
                                <select
                                    v-model="product.prod_uom_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">Select UOM</option>
                                    <option v-for="uom in uoms" :value="uom.id" :key="uom.id">
                                        {{ uom.name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Warranty (months)</label>
                                <input
                                    v-model="product.warranty_month"
                                    type="number"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter warranty period"
                                >
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea
                                v-model="product.description"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Enter product description"
                            ></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Video URL</label>
                            <input
                                v-model="product.video_url"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Enter video URL"
                            >
                        </div>

                        <div class="flex items-center">
                            <input
                                type="checkbox"
                                id="isActive"
                                v-model="product.is_active"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            >
                            <label for="isActive" class="ml-2 block text-sm text-gray-700">Active Product</label>
                        </div>
                    </div>
                </div>

                <!-- Pricing Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-tags text-blue-500"></i>
                            Default Pricing
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Price</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input
                                        v-model="defaultPrices.purchase_price"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="block w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="0.00"
                                    >
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">MRP</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input
                                        v-model="defaultPrices.mrp"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="block w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="0.00"
                                    >
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input
                                        v-model="defaultPrices.price"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="block w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="0.00"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Variants Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 ">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-layer-group text-blue-500"></i>
                            Product Variants
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Color Options</label>
                                    <multiselect
                                        v-model="selectedColors"
                                        :options="colors"
                                        :multiple="true"
                                        :close-on-select="false"
                                        :clear-on-select="false"
                                        :preserve-search="true"
                                        placeholder="Select colors"
                                        label="name"
                                        track-by="id"
                                        class="multiselect-tailwind"
                                    ></multiselect>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Size Type</label>
                                    <select
                                        v-model="selectedSizeType"
                                        @change="loadSizes"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                        <option value="">Select Size Type</option>
                                        <option v-for="sizeType in sizeTypes" :value="sizeType" :key="sizeType.id">
                                            {{ sizeType.name }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Size Options</label>
                                    <multiselect
                                        v-model="selectedSizes"
                                        :options="filteredSizes"
                                        :multiple="true"
                                        :close-on-select="false"
                                        :clear-on-select="false"
                                        :preserve-search="true"
                                        placeholder="Select sizes"
                                        label="name"
                                        track-by="id"
                                        :disabled="!selectedSizeType"
                                        class="multiselect-tailwind"
                                    ></multiselect>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button
                                    @click="generateVariants"
                                    :disabled="!canGenerateVariants"
                                    class="btn-outline"
                                    :class="{'opacity-50 cursor-not-allowed': !canGenerateVariants}"
                                >
                                    <i class="fas fa-cog mr-2"></i> Generate Variants
                                </button>
                            </div>
                        </div>

                        <!-- Variant Table -->
                        <div v-if="variants.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variant</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MRP</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selling</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Default</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="(variant, index) in variants" :key="index" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div v-if="variant.prod_color_id" class="flex-shrink-0 h-5 w-5 rounded-full"
                                                 :style="{backgroundColor: getColorCode(variant.prod_color_id)}"></div>
                                            <div class="ml-2">
                                                <span v-if="variant.prod_color_id">{{ getColorName(variant.prod_color_id) }}</span>
                                                <span v-if="variant.prod_color_id && variant.prod_size_id"> / </span>
                                                <span v-if="variant.prod_size_id">{{ getSizeName(variant.prod_size_id) }}</span>
                                                <span v-if="!variant.prod_color_id && !variant.prod_size_id">Default</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input
                                            v-model="variant.code"
                                            type="text"
                                            class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                        >
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                                <span class="text-gray-500 text-sm">$</span>
                                            </div>
                                            <input
                                                v-model="variant.purchase_price"
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                class="block w-full pl-6 pr-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                            >
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                                <span class="text-gray-500 text-sm">$</span>
                                            </div>
                                            <input
                                                v-model="variant.mrp"
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                class="block w-full pl-6 pr-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                            >
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                                <span class="text-gray-500 text-sm">$</span>
                                            </div>
                                            <input
                                                v-model="variant.price"
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                class="block w-full pl-6 pr-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                            >
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <input
                                            type="radio"
                                            name="defaultVariant"
                                            v-model="variant.is_default"
                                            :value="true"
                                            @change="setDefaultVariant(index)"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500"
                                        >
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <button
                                            @click="removeVariant(index)"
                                            class="text-red-500 hover:text-red-700"
                                        >
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Images -->
            <div class="space-y-6">
                <!-- Images Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-images text-blue-500"></i>
                            Product Images
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Upload Area -->
                        <div
                            @click="triggerFileInput"
                            @dragover.prevent="dragOver"
                            @drop.prevent="handleDrop"
                            class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer transition-colors"
                            :class="{'border-blue-500 bg-blue-50': isDragActive}"
                        >
                            <div class="space-y-2">
                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400" :class="{'text-blue-500': isDragActive}"></i>
                                <p class="text-sm text-gray-500" :class="{'text-blue-500': isDragActive}">
                                    Drag & drop images here or click to browse
                                </p>
                                <p class="text-xs text-gray-400">Supports JPG, PNG up to 5MB</p>
                            </div>
                            <input
                                type="file"
                                ref="fileInput"
                                multiple
                                accept="image/*"
                                @change="handleFiles"
                                class="hidden"
                            >
                        </div>

                        <!-- Image Previews -->
                        <div class="grid grid-cols-2 gap-3">
                            <div
                                v-for="(image, index) in images"
                                :key="index"
                                class="relative rounded-lg overflow-hidden border border-gray-200 group"
                            >
                                <img :src="image.preview" alt="Product image" class="w-full h-32 object-cover">

                                <!-- Image Overlay Actions -->
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex flex-col justify-between p-2 opacity-0 group-hover:opacity-100">
                                    <div class="flex justify-end">
                                        <button
                                            @click.stop="removeImage(index)"
                                            class="text-white hover:text-red-300 p-1"
                                        >
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>

                                    <div class="bg-white p-2 rounded space-y-1">
                                        <select
                                            v-model="image.prod_variant_id"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        >
                                            <option value="">All Variants</option>
                                            <option v-for="variant in variants" :value="variant.id" :key="variant.id">
                                                {{ getVariantName(variant) }}
                                            </option>
                                        </select>
                                        <div class="flex items-center justify-between">
                                            <label class="text-xs text-gray-700">Default</label>
                                            <input
                                                type="checkbox"
                                                v-model="image.is_default"
                                                class="h-3 w-3 text-blue-600 focus:ring-blue-500"
                                            >
                                        </div>
                                    </div>
                                </div>

                                <!-- Default Badge -->
                                <div v-if="image.is_default" class="absolute top-2 left-2 bg-blue-500 text-white text-xs px-2 py-1 rounded">
                                    Default
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-clipboard-check text-blue-500"></i>
                            Summary
                        </h2>
                    </div>
                    <div class="p-6 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Product Name:</span>
                            <span class="font-medium">{{ product.name || 'Not set' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Product Code:</span>
                            <span class="font-medium">{{ product.code || 'Not set' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Variants:</span>
                            <span class="font-medium">{{ variants.length }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Images:</span>
                            <span class="font-medium">{{ images.length }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Status:</span>
                            <span class="font-medium" :class="{'text-green-500': product.is_active, 'text-gray-500': !product.is_active}">
                {{ product.is_active ? 'Active' : 'Inactive' }}
              </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Multiselect from 'vue-multiselect'

export default {
    components: {
        Multiselect
    },
    data() {
        return {
            product: {
                prod_cat_id: null,
                prod_type_id: null,
                prod_sub_group_id: null,
                prod_uom_id: null,
                code: '',
                name: '',
                description: '',
                video_url: '',
                warranty_month: 0,
                is_active: true,
                sequence: null
            },
            defaultPrices: {
                purchase_price: 0,
                mrp: 0,
                price: 0
            },
            // Static data for dropdowns
            categories: [
                { id: 1, name: 'Electronics', code: 'ELEC' },
                { id: 2, name: 'Clothing', code: 'CLOTH' },
                { id: 3, name: 'Home & Kitchen', code: 'HOME' }
            ],
            productTypes: [
                { id: 1, name: 'Physical', code: 'PHYS' },
                { id: 2, name: 'Digital', code: 'DIGI' },
                { id: 3, name: 'Service', code: 'SERV' }
            ],
            subGroups: [
                { id: 1, name: 'Men', code: 'MEN', group_id: 2 },
                { id: 2, name: 'Women', code: 'WOMEN', group_id: 2 },
                { id: 3, name: 'Kids', code: 'KIDS', group_id: 2 }
            ],
            uoms: [
                { id: 1, name: 'Piece', code: 'PC' },
                { id: 2, name: 'Kilogram', code: 'KG' },
                { id: 3, name: 'Liter', code: 'L' }
            ],
            colors: [
                { id: 1, name: 'Red', code: 'RED', is_active: true, hex: '#ef4444' },
                { id: 2, name: 'Blue', code: 'BLUE', is_active: true, hex: '#3b82f6' },
                { id: 3, name: 'Green', code: 'GREEN', is_active: true, hex: '#10b981' },
                { id: 4, name: 'Black', code: 'BLACK', is_active: true, hex: '#000000' },
                { id: 5, name: 'White', code: 'WHITE', is_active: true, hex: '#ffffff' }
            ],
            sizeTypes: [
                { id: 1, name: 'Clothing', code: 'CLOTH' },
                { id: 2, name: 'Shoes', code: 'SHOE' }
            ],
            sizes: [
                { id: 1, size_type_id: 1, name: 'XS', code: 'XS', is_active: true },
                { id: 2, size_type_id: 1, name: 'S', code: 'S', is_active: true },
                { id: 3, size_type_id: 1, name: 'M', code: 'M', is_active: true },
                { id: 4, size_type_id: 1, name: 'L', code: 'L', is_active: true },
                { id: 5, size_type_id: 1, name: 'XL', code: 'XL', is_active: true },
                { id: 6, size_type_id: 2, name: '38', code: '38', is_active: true },
                { id: 7, size_type_id: 2, name: '39', code: '39', is_active: true },
                { id: 8, size_type_id: 2, name: '40', code: '40', is_active: true },
                { id: 9, size_type_id: 2, name: '41', code: '41', is_active: true },
                { id: 10, size_type_id: 2, name: '42', code: '42', is_active: true }
            ],
            selectedColors: [],
            selectedSizeType: null,
            selectedSizes: [],
            variants: [],
            images: [],
            isDragActive: false
        }
    },
    computed: {
        filteredSizes() {
            if (!this.selectedSizeType) return [];
            return this.sizes.filter(size => size.size_type_id === this.selectedSizeType.id);
        },
        canGenerateVariants() {
            return this.selectedColors.length > 0 || this.selectedSizes.length > 0;
        }
    },
    methods: {
        loadSizes() {
            this.selectedSizes = [];
        },
        generateVariants() {
            // If no colors or sizes selected, create a single default variant
            if (this.selectedColors.length === 0 && this.selectedSizes.length === 0) {
                this.variants = [{
                    prod_color_id: null,
                    prod_size_id: null,
                    code: this.product.code + '-DEFAULT',
                    purchase_price: this.defaultPrices.purchase_price,
                    mrp: this.defaultPrices.mrp,
                    price: this.defaultPrices.price,
                    is_default: true,
                    sequence: null
                }];
                return;
            }

            // Generate all possible combinations of selected colors and sizes
            const newVariants = [];

            // If no colors selected, just use sizes
            if (this.selectedColors.length === 0) {
                this.selectedSizes.forEach(size => {
                    newVariants.push({
                        prod_color_id: null,
                        prod_size_id: size.id,
                        code: `${this.product.code}-${size.code}`,
                        purchase_price: this.defaultPrices.purchase_price,
                        mrp: this.defaultPrices.mrp,
                        price: this.defaultPrices.price,
                        is_default: newVariants.length === 0,
                        sequence: null
                    });
                });
            }
            // If no sizes selected, just use colors
            else if (this.selectedSizes.length === 0) {
                this.selectedColors.forEach(color => {
                    newVariants.push({
                        prod_color_id: color.id,
                        prod_size_id: null,
                        code: `${this.product.code}-${color.code}`,
                        purchase_price: this.defaultPrices.purchase_price,
                        mrp: this.defaultPrices.mrp,
                        price: this.defaultPrices.price,
                        is_default: newVariants.length === 0,
                        sequence: null
                    });
                });
            }
            // Both colors and sizes selected
            else {
                this.selectedColors.forEach(color => {
                    this.selectedSizes.forEach(size => {
                        newVariants.push({
                            prod_color_id: color.id,
                            prod_size_id: size.id,
                            code: `${this.product.code}-${color.code}-${size.code}`,
                            purchase_price: this.defaultPrices.purchase_price,
                            mrp: this.defaultPrices.mrp,
                            price: this.defaultPrices.price,
                            is_default: newVariants.length === 0,
                            sequence: null
                        });
                    });
                });
            }

            this.variants = newVariants;
        },
        setDefaultVariant(index) {
            this.variants.forEach((v, i) => {
                v.is_default = (i === index);
            });
        },
        removeVariant(index) {
            this.variants.splice(index, 1);
            // If we removed the default variant and there are other variants, set the first one as default
            if (this.variants.length > 0 && !this.variants.some(v => v.is_default)) {
                this.variants[0].is_default = true;
            }
        },
        getColorName(colorId) {
            const color = this.colors.find(c => c.id === colorId);
            return color ? color.name : '';
        },
        getColorCode(colorId) {
            const color = this.colors.find(c => c.id === colorId);
            return color ? color.hex : '#cccccc';
        },
        getSizeName(sizeId) {
            const size = this.sizes.find(s => s.id === sizeId);
            return size ? size.name : '';
        },
        getVariantName(variant) {
            let name = '';
            if (variant.prod_color_id) name += this.getColorName(variant.prod_color_id);
            if (variant.prod_color_id && variant.prod_size_id) name += ' / ';
            if (variant.prod_size_id) name += this.getSizeName(variant.prod_size_id);
            return name || 'Default';
        },
        triggerFileInput() {
            this.$refs.fileInput.click();
        },
        handleFiles(event) {
            const files = event.target.files || event.dataTransfer.files;
            this.processFiles(files);
        },
        dragOver() {
            this.isDragActive = true;
        },
        handleDrop(event) {
            this.isDragActive = false;
            this.handleFiles(event);
        },
        processFiles(files) {
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (!file.type.match('image.*')) continue;

                const reader = new FileReader();
                reader.onload = (e) => {
                    this.images.push({
                        file: file,
                        preview: e.target.result,
                        prod_variant_id: null,
                        is_default: this.images.length === 0,
                        sequence: null
                    });
                };
                reader.readAsDataURL(file);
            }
        },
        removeImage(index) {
            this.images.splice(index, 1);
            // If we removed the default image and there are other images, set the first one as default
            if (this.images.length > 0 && !this.images.some(img => img.is_default)) {
                this.images[0].is_default = true;
            }
        },
        cancel() {
            // Navigate back or reset form
            this.$router.back();
        },
        saveProduct() {
            // Validate product data
            if (!this.product.name || !this.product.code) {
                this.$toast.error('Product name and code are required');
                return;
            }

            // Validate variants
            if (this.variants.length === 0) {
                this.$toast.error('Please generate at least one product variant');
                return;
            }

            // Prepare form data
            const formData = new FormData();

            // Add product data
            formData.append('product', JSON.stringify(this.product));

            // Add variants
            formData.append('variants', JSON.stringify(this.variants));

            // Add images
            this.images.forEach((image, index) => {
                formData.append(`images[${index}]`, image.file);
                formData.append(`images_data[${index}]`, JSON.stringify({
                    prod_variant_id: image.prod_variant_id,
                    is_default: image.is_default,
                    sequence: image.sequence
                }));
            });

            // Here you would typically make an API call to save the product
            console.log('Saving product:', this.product);
            console.log('With variants:', this.variants);
            console.log('And images:', this.images);

            // Example API call (you would replace this with your actual API call)
            /*
            axios.post('/api/products', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            })
            .then(response => {
              this.$toast.success('Product saved successfully!');
              this.$router.push({ name: 'products' });
            })
            .catch(error => {
              console.error('Error saving product:', error);
              this.$toast.error('Error saving product. Please try again.');
            });
            */
        }
    }
}
</script>

<style>
/* Multiselect Tailwind compatibility */
.multiselect-tailwind .multiselect__tags {
    @apply min-h-[42px] border border-gray-300 rounded-md;
}
.multiselect-tailwind .multiselect__content-wrapper {
    @apply border border-gray-300 shadow-lg;
}
.multiselect-tailwind .multiselect__option--highlight {
    @apply bg-blue-500;
}
.multiselect-tailwind .multiselect__option--selected.multiselect__option--highlight {
    @apply bg-red-500;
}
.multiselect-tailwind .multiselect__tag {
    @apply bg-blue-500;
}
.multiselect-tailwind .multiselect__tag-icon:after {
    @apply text-white hover:text-red-200;
}

/* Custom button styles */
.btn-primary {
    @apply bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow-sm transition-colors duration-200;
}
.btn-secondary {
    @apply bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md shadow-sm transition-colors duration-200;
}
.btn-outline {
    @apply border border-blue-500 text-blue-500 hover:bg-blue-50 px-4 py-2 rounded-md transition-colors duration-200;
}
</style>
