<template>
    <div class="space-y-4">

        <!-- Collapse 1 (Open by default) -->
        <div class="border rounded-lg overflow-hidden">
            <button
                @click="toggleCollapse(1)"
                class="w-full flex justify-between items-center p-4 bg-gray-50 hover:bg-gray-100 transition"
            >
                <h3 class="font-semibold text-primary">Header Info</h3>
                <i
                    :class="`fas ${isOpen[1] ? 'fa-chevron-up' : 'fa-chevron-down'}`"
                    class="text-gray-500"
                ></i>
            </button>

            <div v-show="isOpen[1]" class="p-4 space-y-4">
                <!-- Title Input -->
                <div>
                    <label class="block text-gray-700 mb-2">Title</label>
                    <input
                        v-model="formData[1].title"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="Enter title"
                    >
                </div>

                <!-- Company Name Input -->
                <div>
                    <label class="block text-gray-700 mb-2">Company Name</label>
                    <input
                        v-model="formData[1].companyName"
                        type="text"
                        class="w-full px-3 py-2 border rounded border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="Enter company name"
                    >
                </div>

                <!-- Description Textarea -->
                <div>
                    <label class="block text-gray-700 mb-2">Policy Info</label>
                    <textarea
                        v-model="formData[1].aboutus"
                        rows="4"
                        class="w-full px-3 py-2 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="Type here..."
                    ></textarea>
                </div>

                <!-- Logo Upload -->
                <div class="flex">
                    <div class="mr-6">
                        <label class="block text-gray-700 mb-2">Company Logo</label>
                        <input
                            type="file"
                            @change="handleLogoUpload"
                            accept="image/*"
                            class="w-full px-3 py-2 border  border-gray-200 rounded"
                            ref="logoInput"
                        >
                    </div>

                    <!-- Logo Preview Section -->
                    <div class="mt-3">
                        <h4 class="text-sm font-medium mb-1">Preview:</h4>
                        <div class="relative w-32 h-32 border rounded overflow-hidden">
                            <!-- Existing Logo -->
                            <img
                                v-if="formData[1].logoUrl && !logoPreview"
                                :src="baseURL + formData[1].logoUrl"
                                class="w-full h-full object-contain"
                                alt="Current Logo"
                            >

                            <!-- New Logo Preview -->
                            <img
                                v-if="logoPreview"
                                :src="logoPreview"
                                class="w-full h-full object-contain"
                                alt="New Logo Preview"
                            >

                            <!-- Placeholder when no logo exists -->
                            <div
                                v-if="!formData[1].logoUrl && !logoPreview"
                                class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-400"
                            >
                                <i class="fas fa-image text-2xl"></i>
                            </div>

                            <!-- Remove Logo Button -->
                            <button
                                v-if="formData[1].logoUrl || logoPreview"
                                @click="removeHeaderLogo"
                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition"
                            >
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Policy PDF Preview Section -->
                    <div class="mt-3 ml-5">
                        <div class="mr-6">
                            <label class="block text-gray-700 mb-2">Company Policy PDF</label>
                            <input
                                type="file"
                                @change="handleMessPolicyPdfUpload"
                                accept=".pdf"
                                class="w-full px-3 py-2 border border-gray-200 rounded"
                                ref="pdfInput"
                            >
                        </div>
                        <div class="mt-2 text-sm text-primary-dark" v-if="formData[1].pdf_url || pdfPreview">
                            <i class="fas fa-file-pdf mr-1"></i>
                            <span v-if="pdfPreview">Selected: {{ formData[1].pdf.name || 'New PDF' }}</span>
                            <span v-else>Current: {{ formData[1].pdf_url.split('/').pop() }}</span>
                        </div>
                         <!-- Preview or Link to View -->
                        <div class="mt-2" v-if="formData[1].pdf_url || pdfPreviewUrl">
                            <p class="text-gray-600">Uploaded PDF file:</p>
                            <iframe
                                v-if="pdfPreviewUrl"
                                :src="pdfPreviewUrl"
                                class="w-full h-64 border mt-2"
                            ></iframe>
                            <a
                                v-else
                                :href="formData[1].pdf_url"
                                target="_blank"
                                class="text-blue-500 underline mt-1 inline-block"
                            >
                                View PDF
                            </a>
                        </div>
                    </div>
                    <!-- Signature Image Upload -->
                    <div class="mt-3 ml-5">
                        <div class="mr-6">
                            <label class="block text-gray-700 mb-2">Signature Image</label>
                            <input
                                type="file"
                                @change="handleSignatureUpload"
                                accept="image/*"
                                class="w-full px-3 py-2 border border-gray-200 rounded"
                                ref="signatureInput"
                            >
                        </div>
                        <!-- Signature Preview Section -->
                        <div class="mt-2">
                            <h4 class="text-sm font-medium mb-1">Preview:</h4>
                            <div class="relative w-32 h-32 border rounded overflow-hidden">
                                <!-- Existing Signature -->
                                <img
                                    v-if="formData[1].signatureUrl && !signaturePreview"
                                    :src="baseURL + formData[1].signatureUrl"
                                    class="w-full h-full object-contain"
                                    alt="Current Signature"
                                >

                                <!-- New Signature Preview -->
                                <img
                                    v-if="signaturePreview"
                                    :src="signaturePreview"
                                    class="w-full h-full object-contain"
                                    alt="New Signature Preview"
                                >

                                <!-- Placeholder when no signature exists -->
                                <div
                                    v-if="!formData[1].signatureUrl && !signaturePreview"
                                    class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-400"
                                >
                                    <i class="fas fa-signature text-2xl"></i>
                                </div>

                            </div>
                        </div>
                    </div>


                    <!-- Signature Image Upload 22 -->
                    <div class="mt-3 ml-5">
                        <div class="mr-6">
                            <label class="block text-gray-700 mb-2">Signature Two Image</label>
                            <input
                                type="file"
                                @change="handleSignatureUpload2"
                                accept="image/*"
                                class="w-full px-3 py-2 border border-gray-200 rounded"
                                ref="signatureInput"
                            >
                        </div>
                        <!-- Signature Preview Section -->
                        <div class="mt-2">
                            <h4 class="text-sm font-medium mb-1">Preview:</h4>
                            <div class="relative w-32 h-32 border rounded overflow-hidden">
                                <!-- Existing Signature -->
                                <img
                                    v-if="formData[1].signatureUrl2 && !signaturePreview2"
                                    :src="baseURL + formData[1].signatureUrl2"
                                    class="w-full h-full object-contain"
                                    alt="Current Signature"
                                >

                                <!-- New Signature Preview -->
                                <img
                                    v-if="signaturePreview2"
                                    :src="signaturePreview2"
                                    class="w-full h-full object-contain"
                                    alt="New Signature Preview"
                                >

                                <!-- Placeholder when no signature exists -->
                                <div
                                    v-if="!formData[1].signatureUrl2 && !signaturePreview2"
                                    class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-400"
                                >
                                    <i class="fas fa-signature text-2xl"></i>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>


                <div class="flex justify-end mt-4">
                    <button
                        @click="submitForm(1)"
                        class="px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark transition"
                        :disabled="isSubmitting[1]"
                    >
                        <i v-if="!isSubmitting[1]" class="fas fa-save mr-2"></i>
                        <i v-else class="fas fa-spinner fa-spin mr-2"></i>
                        {{ isSubmitting[1] ? 'Updating...' : 'Update' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Slider Section -->
        <div class="border rounded-lg overflow-hidden">
            <button
                @click="toggleCollapse(2)"
                class="w-full flex justify-between items-center p-4 bg-gray-50 hover:bg-gray-100 transition"
            >
                <h3 class="font-semibold text-primary">Slider Info</h3>
                <i
                    :class="`fas ${isOpen[2] ? 'fa-chevron-up' : 'fa-chevron-down'}`"
                    class="text-gray-500"
                ></i>
            </button>

            <div v-show="isOpen[2]" class="p-4">
                <!-- Slider Images Display -->
                <div class="mb-6">
                    <div v-if="loadingSliders" class="flex justify-center py-4">
                        <i class="fas fa-spinner fa-spin text-2xl text-primary"></i>
                    </div>
                    <div v-else-if="sliders.length === 0" class="text-gray-500 text-center py-4">
                        No sliders found
                    </div>
                    <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <div
                            v-for="(slider, index) in sliders"
                            :key="index"
                            class="relative rounded-lg overflow-hidden border group"
                        >
                            <img
                                :src="baseURL+slider.imageUrl"
                                :alt="'Slider'"
                                class="w-full h-32 object-cover"
                            >
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 flex items-center justify-center">
                                <button
                                    @click.stop="confirmDeleteSlider(slider.id)"
                                    class="opacity-0 group-hover:opacity-100 bg-red-500 text-white px-2 py-1 rounded-full hover:bg-red-600 transition"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button
                        @click="openModal(2)"
                        class="px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark transition mr-2"
                    >
                        <i class="fas fa-plus mr-2"></i> Add Slider
                    </button>
                    <button
                        @click="fetchSliders"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition mr-2"
                    >
                        <i class="fas fa-sync-alt mr-2"></i> Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- Service Items Section -->
        <div class="border rounded-lg overflow-hidden">
            <button
                @click="toggleCollapse(5)"
                class="w-full flex justify-between items-center p-4 bg-gray-50 hover:bg-gray-100 transition"
            >
                <h3 class="font-semibold text-primary">Service Items</h3>
                <i
                    :class="`fas ${isOpen[5] ? 'fa-chevron-up' : 'fa-chevron-down'}`"
                    class="text-gray-500"
                ></i>
            </button>

            <div v-show="isOpen[5]" class="p-4">
                <!-- Service Items Display -->
                <div class="mb-6">
                    <div v-if="loadingServices" class="flex justify-center py-4">
                        <i class="fas fa-spinner fa-spin text-2xl text-primary"></i>
                    </div>
                    <div v-else-if="services.length === 0" class="text-gray-500 text-center py-4">
                        No service items found
                    </div>
                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div
                            v-for="(service, index) in services"
                            :key="index"
                            class="relative border rounded-lg overflow-hidden group"
                        >
                            <div class="p-4">
                                <!-- Service Image -->
                                <div class="relative h-40 mb-3">
                                    <img
                                        :src="baseURL + service.imageUrl"
                                        :alt="service.title"
                                        class="w-full h-full object-cover rounded"
                                    >
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 flex items-center justify-center">
                                        <button
                                            @click.stop="openEditServiceModal(service)"
                                            class="opacity-0 group-hover:opacity-100 bg-primary text-white py-1 px-2 rounded-full hover:bg-primary-dark transition mr-2"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button
                                            @click.stop="confirmDeleteService(service.id)"
                                            class="opacity-0 group-hover:opacity-100 bg-red-500 text-white py-1 px-2 rounded-full hover:bg-red-600 transition"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Service Info -->
                                <div>
                                    <h4 class="font-medium text-lg">{{ service.title }}</h4>
                                    <div v-if="service.pdfUrl" class="mt-2">
                                        <a
                                            :href="baseURL + service.pdfUrl"
                                            target="_blank"
                                            class="text-primary-dark hover:underline flex items-center"
                                        >
                                            <i class="fas fa-file-pdf mr-2"></i> Download PDF
                                        </a>
                                    </div>
                                    <div v-if="service.refUrl" class="mt-1">
                                        <a
                                            :href="service.refUrl"
                                            target="_blank"
                                            class="text-primary-dark hover:underline flex items-center"
                                        >
                                            <i class="fas fa-link mr-2"></i> Reference Link
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button
                        @click="openServiceModal"
                        class="px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark transition mr-2"
                    >
                        <i class="fas fa-plus mr-2"></i> Add Service
                    </button>
                    <button
                        @click="fetchServices"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition"
                    >
                        <i class="fas fa-sync-alt mr-2"></i> Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- Gallery Section -->
        <div class="border rounded-lg overflow-hidden">
            <button
                @click="toggleCollapse(3)"
                class="w-full flex justify-between items-center p-4 bg-gray-50 hover:bg-gray-100 transition"
            >
                <h3 class="font-semibold text-primary">Gallery Info</h3>
                <i
                    :class="`fas ${isOpen[3] ? 'fa-chevron-up' : 'fa-chevron-down'}`"
                    class="text-gray-500"
                ></i>
            </button>

            <div v-show="isOpen[3]" class="p-4">
                <!-- Gallery Images Display -->
                <div class="mb-6">
                    <div v-if="loadingGalleries" class="flex justify-center py-4">
                        <i class="fas fa-spinner fa-spin text-2xl text-primary"></i>
                    </div>
                    <div v-else-if="galleries.length === 0" class="text-gray-500 text-center py-4">
                        No gallery images found
                    </div>
                    <div v-else class="flex space-x-4 overflow-x-auto pb-2">
                        <div
                            v-for="(gallery, index) in galleries"
                            :key="index"
                            class="relative flex-shrink-0 w-48 h-32 rounded-lg overflow-hidden border group"
                        >
                            <img
                                :src="baseURL+gallery.imageUrl"
                                :alt="'Gallery Image'"
                                class="w-full h-32 object-cover"
                            >
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 flex items-center justify-center">
                                <button
                                    @click.stop="confirmDeleteGallery(gallery.id)"
                                    class="opacity-0 group-hover:opacity-100 bg-red-500 text-white px-2 py-1 rounded-full hover:bg-red-600 transition"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button
                        @click="openModal(3)"
                        class="px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark transition mr-2"
                    >
                        <i class="fas fa-plus mr-2"></i> Add Gallery Image
                    </button>
                    <button
                        @click="fetchGalleries"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition mr-2"
                    >
                        <i class="fas fa-sync-alt mr-2"></i> Refresh
                    </button>
                </div>
            </div>
        </div>


        <!-- Footer Section -->
        <div class="border rounded-lg overflow-hidden">
            <button
                @click="toggleCollapse(4)"
                class="w-full flex justify-between items-center p-4 bg-gray-50 hover:bg-gray-100 transition"
            >
                <h3 class="font-semibold text-primary">Footer Info</h3>
                <i
                    :class="`fas ${isOpen[4] ? 'fa-chevron-up' : 'fa-chevron-down'}`"
                    class="text-gray-500"
                ></i>
            </button>

            <div v-show="isOpen[4]" class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Section - Contact Info -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-lg border-b pb-2">Contact Information</h4>

                        <!-- Address -->
                        <div>
                            <label class="block text-gray-700 mb-1">Address</label>
                            <input
                                v-model="footerData.address"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-primary"
                                placeholder="Enter company address"
                            >
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-gray-700 mb-1">Phone</label>
                            <input
                                v-model="footerData.phone"
                                type="text"
                                class="w-full px-3 py-2 border  border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-primary"
                                placeholder="Enter phone number"
                            >
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-gray-700 mb-1">Email</label>
                            <input
                                v-model="footerData.email"
                                type="email"
                                class="w-full px-3 py-2 border  border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-primary"
                                placeholder="Enter email address"
                            >
                        </div>

                        <!-- Update Button -->
                        <div class="pt-2">
                            <button
                                @click="updateFooterInfo"
                                class="px-4 py-2 bg-primary text-white  border-gray-200 rounded hover:bg-primary-dark transition"
                                :disabled="isUpdatingFooter"
                            >
                                <i v-if="!isUpdatingFooter" class="fas fa-save mr-2"></i>
                                <i v-else class="fas fa-spinner fa-spin mr-2"></i>
                                {{ isUpdatingFooter ? 'Updating...' : 'Update Contact Info' }}
                            </button>
                        </div>
                    </div>

                    <!-- Right Section - Important Links -->
                    <div class="space-y-4">
                        <div class="flex justify-between items-center border-b pb-2">
                            <h4 class="font-medium text-lg">Important Links</h4>

                            <div class="flex justify-between items-center border-b pb-2">
                                <button
                                    @click="fetchFooterLinks"
                                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition mr-3"
                                >
                                    <i class="fas fa-sync-alt mr-2"></i> Refresh Links
                                </button>
                                <button
                                    @click="openLinkModal"
                                    class="px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark transition"
                                >
                                    <i class="fas fa-plus mr-2"></i> Add Link
                                </button>
                            </div>
                        </div>

                        <!-- Links List -->
                        <div v-if="loadingLinks" class="flex justify-center py-4">
                            <i class="fas fa-spinner fa-spin text-2xl text-primary"></i>
                        </div>
                        <div v-else-if="footerLinks.length === 0" class="text-gray-500 text-center py-4">
                            No links added yet
                        </div>
                        <div v-else class="space-y-2">
                            <div
                                v-for="(link, index) in footerLinks"
                                :key="index"
                                class="flex items-center justify-between p-3 bg-gray-50 rounded"
                            >
                                <div>
                                    <a :href="link.linkUrl" target="_blank" class="text-primary-dark hover:underline">
                                        {{ link.title || link.linkUrl }}
                                    </a>
                                </div>
                                <button
                                    @click="confirmDeleteLink(link.id)"
                                    class="text-red-500 hover:text-red-700 transition"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        <!-- Service Modal -->
        <div v-if="showServiceModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg w-full max-w-md">
                <div class="p-4 border-b">
                    <h3 class="font-semibold text-lg">{{ editingService ? 'Edit Service' : 'Add New Service' }}</h3>
                </div>

                <div class="p-4 space-y-4">
                    <!-- Title -->
                    <div>
                        <label class="block text-gray-700 mb-1">Service Title*</label>
                        <input
                            v-model="serviceForm.title"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-200 rounded"
                            placeholder="Enter service title"
                            required
                        >
                    </div>

                    <!-- Image Upload -->
                    <div>
                        <label class="block text-gray-700 mb-1">Service Image*</label>
                        <input
                            type="file"
                            @change="handleServiceImageUpload"
                            accept="image/*"
                            class="w-full px-3 py-2 border border-gray-200 rounded"
                            ref="serviceImageInput"
                        >
                        <div v-if="serviceImagePreview" class="mt-3">
                            <h4 class="text-sm font-medium mb-1">Preview:</h4>
                            <img :src="serviceImagePreview" class="max-h-40 rounded border">
                        </div>
                        <div v-else-if="serviceForm.image_url" class="mt-3">
                            <h4 class="text-sm font-medium mb-1">Current Image:</h4>
                            <img :src="baseURL + serviceForm.image_url" class="max-h-40 rounded border">
                        </div>
                    </div>

                    <!-- PDF Upload -->
                    <div>
                        <label class="block text-gray-700 mb-1">PDF Document</label>
                        <input
                            type="file"
                            @change="handleServicePdfUpload"
                            accept=".pdf"
                            class="w-full px-3 py-2 border border-gray-200 rounded"
                            ref="servicePdfInput"
                        >
                        <div v-if="serviceForm.pdf_url" class="mt-2 text-sm text-primary-dark">
                            <i class="fas fa-file-pdf mr-1"></i> Current PDF: {{ serviceForm.pdf_url.split('/').pop() }}
                        </div>
                    </div>

                    <!-- Reference URL -->
                    <div>
                        <label class="block text-gray-700 mb-1">Reference URL</label>
                        <input
                            v-model="serviceForm.ref_url"
                            type="url"
                            class="w-full px-3 py-2 border border-gray-200 rounded"
                            placeholder="example.com"
                        >
                    </div>
                </div>

                <div class="p-4 border-t flex justify-end space-x-2">
                    <button
                        @click="closeServiceModal"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition"
                    >
                        Cancel
                    </button>
                    <button
                        @click="saveService"
                        class="px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark transition"
                        :disabled="isSavingService"
                    >
                        <i v-if="!isSavingService" class="fas fa-save mr-2"></i>
                        <i v-else class="fas fa-spinner fa-spin mr-2"></i>
                        {{ isSavingService ? 'Saving...' : 'Save Service' }}
                    </button>
                </div>
            </div>
        </div>


        <!-- Link Modal -->
        <div v-if="showLinkModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg w-full max-w-md">
                <div class="p-4 border-b">
                    <h3 class="font-semibold text-lg">Add Important Link</h3>
                </div>

                <div class="p-4 space-y-4">
                    <div>
                        <label class="block text-gray-700 mb-1">Link Text</label>
                        <input
                            v-model="newLink.title"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-200 rounded"
                            placeholder="Enter link text"
                        >
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">URL</label>
                        <input
                            v-model="newLink.url"
                            type="url"
                            class="w-full px-3 py-2 border border-gray-200 rounded"
                            placeholder="example.com"
                        >
                    </div>
                </div>

                <div class="p-4 border-t flex justify-end space-x-2">
                    <button
                        @click="showLinkModal = false"
                        class="px-4 py-2 bg-gray-300 rounded  hover:bg-gray-400 transition"
                    >
                        Cancel
                    </button>
                    <button
                        @click="addNewLink"
                        class="px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark transition"
                        :disabled="isAddingLink"
                    >
                        <i v-if="!isAddingLink" class="fas fa-save mr-2"></i>
                        <i v-else class="fas fa-spinner fa-spin mr-2"></i>
                        {{ isAddingLink ? 'Adding...' : 'Add Link' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Slider Modal -->
        <div v-if="showModal && activeModal === 2" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg w-full max-w-md">
                <div class="p-4 border-b">
                    <h3 class="font-semibold text-lg">Add New Slider</h3>
                </div>

                <div class="p-4">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Slider Image</label>
                        <input
                            type="file"
                            ref="fileInput"
                            @change="handleFileUpload"
                            accept="image/*"
                            class="w-full px-3 py-2 border border-gray-200 rounded"
                        >
                        <div v-if="imagePreview" class="mt-3">
                            <h4 class="text-sm font-medium mb-1">Preview:</h4>
                            <img :src="imagePreview" class="max-h-40 rounded border">
                        </div>
                    </div>
                </div>

                <div class="p-4 border-t flex justify-end space-x-2">
                    <button
                        @click="closeModal"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition"
                    >
                        Cancel
                    </button>
                    <button
                        @click="uploadSlider"
                        class="px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark transition"
                        :disabled="isModalSubmitting"
                    >
                        <i v-if="!isModalSubmitting" class="fas fa-save mr-2"></i>
                        <i v-else class="fas fa-spinner fa-spin mr-2"></i>
                        {{ isModalSubmitting ? 'Uploading...' : 'Upload' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Gallery Modal -->
        <div v-if="showModal && activeModal === 3" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg w-full max-w-md">
                <div class="p-4 border-b">
                    <h3 class="font-semibold text-lg">Add New Gallery Image</h3>
                </div>

                <div class="p-4">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Gallery Image</label>
                        <input
                            type="file"
                            ref="fileInput"
                            @change="handleFileUpload"
                            accept="image/*"
                            class="w-full px-3 py-2 border border-gray-200 rounded"
                        >
                        <div v-if="imagePreview" class="mt-3">
                            <h4 class="text-sm font-medium mb-1">Preview:</h4>
                            <img :src="imagePreview" class="max-h-40 rounded border">
                        </div>
                    </div>
                </div>

                <div class="p-4 border-t flex justify-end space-x-2">
                    <button
                        @click="closeModal"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition"
                    >
                        Cancel
                    </button>
                    <button
                        @click="uploadGallery"
                        class="px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark transition"
                        :disabled="isModalSubmitting"
                    >
                        <i v-if="!isModalSubmitting" class="fas fa-save mr-2"></i>
                        <i v-else class="fas fa-spinner fa-spin mr-2"></i>
                        {{ isModalSubmitting ? 'Uploading...' : 'Upload' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            baseURL: window.baseURL || '/',
            isOpen: {
                1: true,
                2: false,
                3: false,
                4: false,
                5: false // Added for services
            },
            isSubmitting: {
                1: false,
                2: false,
                3: false,
                4: false,
                5: false // Added for services
            },
            showModal: false,
            activeModal: 1,
            isModalSubmitting: false,
            formData: {
                1: {
                    title: '',
                    companyName: '',
                    aboutus: '',
                    logo: null,
                    pdf: null
                },
                2: { image: null, text: '' },
                3: { image: null, text: '' },
            },

            sliders: [],
            galleries: [],
            loadingSliders: false,
            loadingGalleries: false,
            imagePreview: null,
            signaturePreview: null,
            signaturePreview2: null,
            logoPreview: null,
            // Add these new data properties:
            footerData: {
                address: '',
                phone: '',
                email: ''
            },
            footerLinks: [],
            loadingLinks: false,
            showLinkModal: false,
            newLink: {
                title: '',
                url: ''
            },
            isAddingLink: false,
            isUpdatingFooter: false,
            services: [],
            loadingServices: false,
            showServiceModal: false,
            editingService: null,
            serviceForm: {
                title: '',
                image: null,
                image_url: '',
                pdf: null,
                signature: null,
                signature2: null,
                signatureUrl: '',
                signatureUrl2: '',
                pdf_url: '',
                ref_url: ''
            },
            serviceImagePreview: null,
            isSavingService: false
        }
    },
    computed: {
        modalTitle() {
            const titles = {
                1: 'Header Information',
                2: 'Add Slider Information',
                3: 'Add Gallery Image'
            };
            return titles[this.activeModal] || 'Add Content';
        }
    },
    methods: {

        toggleCollapse(section) {
            for (let key in this.isOpen) {
                this.isOpen[key] = false;
            }
            this.isOpen[section] = !this.isOpen[section];
            if (section === 1 && this.isOpen[1]) {
                this.fetchHeaderData();
            }

            if (section === 2 && this.isOpen[2]) {
                this.fetchSliders();
            }

            if (section === 3 && this.isOpen[3]) {
                this.fetchGalleries();
            }
            if (section === 4 && this.isOpen[4]) {
                this.fetchFooterInfo();
                this.fetchFooterLinks();
            }
            if (section === 5 && this.isOpen[5]) {
                this.fetchServices();
            }
        },

        openModal(section) {
            this.activeModal = section;
            this.showModal = true;
            this.imagePreview = null;
            this.formData[section].image = null;
            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
        },

        closeModal() {
            this.showModal = false;
            this.imagePreview = null;
            this.formData[this.activeModal].image = null;
        },

        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.formData[this.activeModal].image = file;

                // Create preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        handleLogoUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.formData[1].logo = file;

                // Create preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.logoPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        handleSignatureUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.formData[1].signature = file;

                // Create preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.signaturePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        handleSignatureUpload2(event) {
            const file = event.target.files[0];
            if (file) {
                this.formData[1].signature2 = file;

                // Create preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.signaturePreview2 = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },


        async uploadSlider() {
            if (!this.formData[2].image) {
                alert('Please select an image');
                return;
            }

            this.isModalSubmitting = true;

            const formData = new FormData();
            formData.append('image', this.formData[2].image);
            formData.append('text', this.formData[2].text);

            try {
                const response = await axios.post('/web-ms/sliders', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                this.closeModal();
                this.fetchSliders();
            } catch (error) {
                console.error(error);
                this.$toast.error('Error uploading slider');
            } finally {
                this.isModalSubmitting = false;
            }
        },

        async uploadGallery() {
            if (!this.formData[3].image) {
                alert('Please select an image');
                return;
            }

            this.isModalSubmitting = true;

            const formData = new FormData();
            formData.append('image', this.formData[3].image);
            formData.append('text', this.formData[3].text);

            try {
                const response = await axios.post('/web-ms/galleries', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                this.closeModal();
                this.fetchGalleries();
            } catch (error) {
                console.error(error);
                this.$toast.error('Error uploading gallery image');
            } finally {
                this.isModalSubmitting = false;
            }
        },

        async fetchSliders() {
            this.loadingSliders = true;
            try {
                const response = await axios.get('/web-ms/sliders');
                this.sliders = response.data.result;
            } catch (error) {
                console.error(error);
                this.$toast.error('Failed to load sliders');
            } finally {
                this.loadingSliders = false;
            }
        },

        async fetchGalleries() {
            this.loadingGalleries = true;
            try {
                const response = await axios.get('/web-ms/galleries');
                this.galleries = response.data.result;
            } catch (error) {
                console.error(error);
                this.$toast.error('Failed to load galleries');
            } finally {
                this.loadingGalleries = false;
            }
        },

        confirmDeleteSlider(sliderId) {
            if (confirm('Are you sure you want to delete this slider?')) {
                this.deleteSlider(sliderId);
            }
        },

        confirmDeleteGallery(galleryId) {
            if (confirm('Are you sure you want to delete this gallery image?')) {
                this.deleteGallery(galleryId);
            }
        },

        async deleteSlider(sliderId) {
            try {
                await axios.delete(`/web-ms/sliders/${sliderId}`);
                this.fetchSliders();
            } catch (error) {
                console.error(error);
                this.$toast.error('Failed to delete slider');
            }
        },

        async deleteGallery(galleryId) {
            try {
                await axios.delete(`/web-ms/galleries/${galleryId}`);
                this.fetchGalleries();
            } catch (error) {
                console.error(error);
                this.$toast.error('Failed to delete gallery image');
            }
        },


        // Update your fetchHeaderData method to include signature
        async fetchHeaderData() {
            this.loadingHeader = true;
            try {
                const response = await axios.get('/web-ms/header-info');
                const headerData = response.data.result;

                // Update form data with fetched values
                this.formData[1].title = headerData.title || '';
                this.formData[1].companyName = headerData.comName || '';
                this.formData[1].aboutus = headerData.aboutUs || '';

                // Set logo preview
                if (headerData.logoUrl) {
                    this.formData[1].logoUrl = headerData.logoUrl;
                    this.logoPreview = this.baseURL + headerData.logoUrl;
                }

                // Set PDF preview
                if (headerData.pdf_url) {
                    this.formData[1].pdf_url = headerData.pdf_url;
                    this.formData[1].pdf = null;
                }

                // Set signature preview
                if (headerData.signature_url) {
                    this.formData[1].signatureUrl = headerData.signature_url;
                    this.signaturePreview = this.baseURL + headerData.signature_url;
                    this.formData[1].signatureUrl2  = this.baseURL + headerData.signature_url2;
                    this.signaturePreview2 = this.baseURL + headerData.signature_url2;
                }

            } catch (error) {
                console.error('Error fetching header data:', error);
            } finally {
                this.loadingHeader = false;
            }
        },


        removeHeaderLogo() {
            // Clear the logo file input
            if (this.$refs.logoInput) {
                this.$refs.logoInput.value = '';
            }

            // Reset the logo preview
            this.logoPreview = null;

            // Clear the uploaded file reference
            this.formData[1].logo = null;

            // If you want to completely remove the logo from the server immediately
            // (instead of waiting for form submission), you can add:
            if (this.formData[1].logoUrl) {
                if (confirm('Are you sure you want to remove the logo?')) {
                    this.removeHeaderLogoFromServer();
                }
            }
        },

        async removeHeaderLogoFromServer() {
            try {
                await axios.delete('/web-ms/header/logo');
                this.formData[1].logoUrl = '';
                this.$toast.success('Logo removed successfully');
            } catch (error) {
                console.error('Error removing logo:', error);
                this.$toast.error('Failed to remove logo');
            }
        },

        async submitForm(section) {
            this.isSubmitting[section] = true;
            try {
                const formData = new FormData();

                // Append all fields for section 1
                if (section === 1) {
                    formData.append('title', this.formData[1].title);
                    formData.append('companyName', this.formData[1].companyName);
                    formData.append('aboutus', this.formData[1].aboutus);
                    if (this.formData[1].logo) {
                        formData.append('logo', this.formData[1].logo);
                    }
                    if (this.formData[1].pdf) {
                        formData.append('pdf', this.formData[1].pdf);
                    }
                    if (this.formData[1].signature) {
                        formData.append('signature', this.formData[1].signature);
                    }
                    if (this.formData[1].signature2) {
                        formData.append('signature2', this.formData[1].signature2);
                    }
                } else {
                    // For other sections
                    for (const key in this.formData[section]) {
                        formData.append(key, this.formData[section][key]);
                    }
                }

                const response = await axios.post(`/web-ms/header-info`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                this.fetchHeaderData();

            } catch (error) {

                console.error(error);
            } finally {
                this.isSubmitting[section] = false;
            }
        },



        // Open link modal
        openLinkModal() {
            this.newLink = { title: '', url: '' };
            this.showLinkModal = true;
        },

        // Fetch footer contact info
        async fetchFooterInfo() {
            try {
                const response = await axios.get('/web-ms/footer-info');
                this.footerData = response.data.result || {
                    address: '',
                    phone: '',
                    email: ''
                };
            } catch (error) {
                console.error('Error fetching footer info:', error);
            }
        },

        // Update footer contact info
        async updateFooterInfo() {
            this.isUpdatingFooter = true;
            try {
                await axios.post('/web-ms/footer-info', this.footerData);
            }
            catch (error) {
                console.error('Error updating footer info:', error);

            } finally {
                this.isUpdatingFooter = false;
            }
        },

        // Fetch footer links
        async fetchFooterLinks() {
            this.loadingLinks = true;
            try {
                const response = await axios.get('/web-ms/footer-links');
                this.footerLinks = response.data.result || [];
            } catch (error) {
                console.error('Error fetching footer links:', error);
                this.$toast.error('Failed to load links');
            } finally {
                this.loadingLinks = false;
            }
        },

        // Add new link
        async addNewLink() {

            this.isAddingLink = true;
            try {
                await axios.post('/web-ms/footer-links', this.newLink);
                this.showLinkModal = false;
                this.fetchFooterLinks();
            } catch (error) {
                console.error('Error adding link:', error);
            } finally {
                this.isAddingLink = false;
            }
        },

        // Confirm and delete link
        confirmDeleteLink(linkId) {
            if (confirm('Are you sure you want to delete this link?')) {
                this.deleteLink(linkId);
            }
        },

        // Delete link
        async deleteLink(linkId) {
            try {
                await axios.delete(`/web-ms/footer-links/${linkId}`);
                this.fetchFooterLinks();
            } catch (error) {
                console.error('Error deleting link:', error);
            }
        },

// Fetch services
        async fetchServices() {
            this.loadingServices = true;
            try {
                const response = await axios.get('/web-ms/services');
                this.services = response.data.result || [];
            } catch (error) {
                console.error('Error fetching services:', error);
            } finally {
                this.loadingServices = false;
            }
        },

        // Open service modal
        openServiceModal() {
            this.editingService = null;
            this.serviceForm = {
                title: '',
                image: null,
                image_url: '',
                pdf: null,
                pdf_url: '',
                ref_url: ''
            };
            this.serviceImagePreview = null;
            if (this.$refs.serviceImageInput) this.$refs.serviceImageInput.value = '';
            if (this.$refs.servicePdfInput) this.$refs.servicePdfInput.value = '';
            this.showServiceModal = true;
        },

        // Open edit service modal
        openEditServiceModal(service) {
            this.editingService = service;
            this.serviceForm = {
                title: service.title,
                image: null,
                image_url: service.imageUrl,
                pdf: null,
                pdf_url: service.pdfUrl,
                ref_url: service.refUrl
            };
            this.serviceImagePreview = null;
            if (this.$refs.serviceImageInput) this.$refs.serviceImageInput.value = '';
            if (this.$refs.servicePdfInput) this.$refs.servicePdfInput.value = '';
            this.showServiceModal = true;
        },

        // Close service modal
        closeServiceModal() {
            this.showServiceModal = false;
            this.editingService = null;
            this.serviceImagePreview = null;
        },

        // Handle service image upload
        handleServiceImageUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.serviceForm.image = file;

                // Create preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.serviceImagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        // Handle service PDF upload
        handleServicePdfUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.serviceForm.pdf = file;
            }
        },

        // Handle mess policy PDF upload
        handleMessPolicyPdfUpload(event) {
            const file = event.target.files[0];
            if (file && file.type === 'application/pdf') {
                this.formData[1].pdf = file;
                this.pdfPreview = URL.createObjectURL(file); // Optional: show preview
            } else {
                alert('Please upload a valid PDF file.');
            }
        },


        // Save service
        async saveService() {
            if (!this.serviceForm.title) {
                return;
            }

            if (!this.serviceForm.image && !this.serviceForm.image_url) {
                return;
            }

            this.isSavingService = true;

            try {
                const formData = new FormData();
                formData.append('title', this.serviceForm.title);
                if (this.serviceForm.image) formData.append('image', this.serviceForm.image);
                if (this.serviceForm.pdf) formData.append('pdf', this.serviceForm.pdf);
                if (this.serviceForm.ref_url) formData.append('ref_url', this.serviceForm.ref_url);

                let response;
                if (this.editingService) {
                    response = await axios.post(`/web-ms/services/${this.editingService.id}`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });
                } else {
                    response = await axios.post('/web-ms/services', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });
                }

                this.closeServiceModal();
                this.fetchServices();
            } catch (error) {
                console.error('Error saving service:', error);
            } finally {
                this.isSavingService = false;
            }
        },

        // Confirm delete service
        confirmDeleteService(serviceId) {
            if (confirm('Are you sure you want to delete this service?')) {
                this.deleteService(serviceId);
            }
        },

        // Delete service
        async deleteService(serviceId) {
            try {
                await axios.delete(`/web-ms/services/${serviceId}`);
                this.fetchServices();
            } catch (error) {
                console.error('Error deleting service:', error);
            }
        }

    },
    mounted() {
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        const token = document.head.querySelector('meta[name="csrf-token"]');
        if (token) {
            axios.defaults.headers.common['X-CSRF-TOken'] = token.content;
        }

        this.fetchHeaderData();
    }
}
</script>
