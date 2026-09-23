<template>
    <div class="containers">
        <!-- Top Section with Create Button and Search -->
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <button @click="showCreateModal" class="bg-gradient-to-r from-primary-dark to-primary hover:from-primary hover:to-primary-dark text-white px-5 py-2.5 rounded-lg flex items-center gap-2 transition-all shadow-md hover:shadow-lg active:scale-[0.98]">
                    <i class="fas fa-plus"></i>
                    Create New
                </button>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <!-- Search Input -->
                <div class="relative flex-grow max-w-md">
                    <input
                        v-model="searchQuery"
                        @input="handleSearch"
                        type="text"
                        placeholder="Search..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all shadow-sm focus:shadow-md"
                    >
                    <div class="absolute left-3 top-3 text-gray-400">
                        <i class="fas fa-search text-base"></i>
                    </div>
                </div>

                <!-- Refresh Button -->
                <button @click="fetchData" class="p-2.5 text-gray-600 hover:text-primary hover:bg-gray-100 rounded-lg transition-all shadow-sm hover:shadow-md active:scale-95">
                    <i class="fas fa-sync-alt text-base"></i>
                </button>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SL</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sequence</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="(item, index) in items" :key="item.id" class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ (currentPage - 1) * perPage + index + 1 }}</td>
                        <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ item.code }}</td>
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">{{ item.name }}</td>
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ item.slug }}</td>
                        <td class="px-6 py-2 whitespace-nowrap">
                            <span :class="[item.isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800', 'px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full']">
                                {{ item.isActive ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ item.sequence }}</td>
                        <td class="px-6 py-2 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end items-center gap-2">
                                <button
                                    @click="showViewModal(item)"
                                    class="text-primary hover:text-blue-900 p-1.5 rounded-lg hover:bg-blue-50 transition-all shadow-sm hover:shadow-md active:scale-95"
                                >
                                    <i class="fas fa-eye"></i>
                                </button>

                                <div class="relative">
                                    <button
                                        @click.stop="toggleDropdown(item.id)"
                                        class="text-gray-600 hover:text-gray-900 p-1.5 rounded-lg hover:bg-gray-50 transition-all shadow-sm hover:shadow-md active:scale-95"
                                        :class="{ 'bg-gray-100': activeDropdown === item.id }"
                                    >
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>

                                    <transition
                                        enter-active-class="transition ease-out duration-100"
                                        enter-from-class="transform opacity-0 scale-95"
                                        enter-to-class="transform opacity-100 scale-100"
                                        leave-active-class="transition ease-in duration-75"
                                        leave-from-class="transform opacity-100 scale-100"
                                        leave-to-class="transform opacity-0 scale-95"
                                    >
                                        <div
                                            v-if="activeDropdown === item.id"
                                            class="absolute right-0 z-50 mt-1 w-48 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                            v-click-outside="() => activeDropdown = null"
                                        >
                                            <div class="py-1">
                                                <button
                                                    @click.stop="showEditModal(item)"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left flex items-center gap-2 transition-colors"
                                                >
                                                    <i class="fas fa-pen text-primary text-sm"></i>
                                                    Edit
                                                </button>
                                                <button
                                                    @click.stop="confirmDelete(item)"
                                                    class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 w-full text-left flex items-center gap-2 transition-colors"
                                                >
                                                    <i class="fas fa-trash text-red-500 text-sm"></i>
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </transition>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Empty State -->
                    <tr v-if="items.length === 0">
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center gap-3 text-gray-400">
                                <i class="fas fa-face-frown text-5xl"></i>
                                <p class="text-lg font-medium">No data found</p>
                                <p class="text-sm">Try adjusting your search or filter</p>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <Pagination
            :total-items="totalItems"
            :per-page="perPage"
            :current-page="currentPage"
            :last-page="lastPage"
            @update:perPage="perPage = $event; currentPage = 1; fetchData()"
            @update:currentPage="currentPage = $event; fetchData()"
        />

        <!-- Create Modal -->
        <vue-final-modal
            v-model="showCreate"
            classes="modal-container"
            content-class="modal-content"
            overlay-transition="vfm-fade"
            content-transition="vfm-slide-up"
        >
            <div class="modal-inner">
                <div class="flex justify-between items-center mb-4 border-b pb-4">
                    <h3 class="text-lg font-medium text-gray-900">Create New Category</h3>
                    <button @click="closeCreateModal" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form @submit.prevent="handleCreate">
                    <div class="space-y-4">
                        <div>
                            <label for="create-code" class="block text-sm font-medium text-gray-700">Code</label>
                            <input
                                v-model="createForm.code"
                                type="text"
                                id="create-code"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                                required
                            >
                        </div>

                        <div>
                            <label for="create-name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input
                                v-model="createForm.name"
                                type="text"
                                id="create-name"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                                required
                            >
                        </div>

                        <div>
                            <label for="create-sequence" class="block text-sm font-medium text-gray-700">Sequence</label>
                            <input
                                v-model.number="createForm.sequence"
                                type="number"
                                id="create-sequence"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                                required
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <div class="mt-2 flex items-center space-x-4">
                                <label class="inline-flex items-center">
                                    <input
                                        v-model="createForm.isActive"
                                        type="radio"
                                        class="h-4 w-4 text-primary focus:ring-primary border-gray-300"
                                        :value="true"
                                    >
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input
                                        v-model="createForm.isActive"
                                        type="radio"
                                        class="h-4 w-4 text-primary focus:ring-primary border-gray-300"
                                        :value="false"
                                    >
                                    <span class="ml-2 text-sm text-gray-700">Inactive</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button
                            type="button"
                            @click="closeCreateModal"
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                        >
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </vue-final-modal>

        <!-- Edit Modal -->
        <vue-final-modal
            v-model="showEdit"
            classes="modal-container"
            content-class="modal-content"
            overlay-transition="vfm-fade"
            content-transition="vfm-slide-up"
        >
            <div v-if="selectedItem" class="modal-inner">
                <div class="flex justify-between items-center mb-4 border-b pb-4">
                    <h3 class="text-lg font-medium text-gray-900">Edit Category</h3>
                    <button @click="closeEditModal" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form @submit.prevent="handleEdit">
                    <div class="space-y-4">
                        <div>
                            <label for="edit-code" class="block text-sm font-medium text-gray-700">Code</label>
                            <input
                                v-model="editForm.code"
                                type="text"
                                id="edit-code"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                                required
                            >
                        </div>

                        <div>
                            <label for="edit-name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input
                                v-model="editForm.name"
                                type="text"
                                id="edit-name"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                                required
                            >
                        </div>

                        <div>
                            <label for="edit-sequence" class="block text-sm font-medium text-gray-700">Sequence</label>
                            <input
                                v-model.number="editForm.sequence"
                                type="number"
                                id="edit-sequence"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                                required
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <div class="mt-2 flex items-center space-x-4">
                                <label class="inline-flex items-center">
                                    <input
                                        v-model="editForm.isActive"
                                        type="radio"
                                        class="h-4 w-4 text-primary focus:ring-primary border-gray-300"
                                        :value="true"
                                    >
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input
                                        v-model="editForm.isActive"
                                        type="radio"
                                        class="h-4 w-4 text-primary focus:ring-primary border-gray-300"
                                        :value="false"
                                    >
                                    <span class="ml-2 text-sm text-gray-700">Inactive</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button
                            type="button"
                            @click="closeEditModal"
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                        >
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </vue-final-modal>

        <!-- View Modal -->
        <vue-final-modal
            v-model="showView"
            classes="modal-container"
            content-class="modal-content"
            overlay-transition="vfm-fade"
            content-transition="vfm-slide-up"
        >
            <div v-if="selectedItem" class="p-6">
                <div class="flex justify-between items-center mb-4 border-b pb-4">
                    <h3 class="text-lg font-medium text-gray-900">Category Details</h3>
                    <button @click="closeViewModal" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Code</label>
                        <p class="mt-1 text-sm text-gray-900">{{ selectedItem.code }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ selectedItem.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Slug</label>
                        <p class="mt-1 text-sm text-gray-900">{{ selectedItem.slug }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Sequence</label>
                        <p class="mt-1 text-sm text-gray-900">{{ selectedItem.sequence }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Status</label>
                        <p class="mt-1 text-sm text-gray-900">
                            <span :class="[selectedItem.isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800', 'px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full']">
                                {{ selectedItem.isActive ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button
                        @click="closeViewModal"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                    >
                        Close
                    </button>
                </div>
            </div>
        </vue-final-modal>

        <!-- Delete Confirmation Modal -->
        <vue-final-modal
            v-model="showDelete"
            classes="modal-container"
            content-class="modal-content"
            overlay-transition="vfm-fade"
            content-transition="vfm-slide-up"
        >
            <div v-if="selectedItem" class="p-6">
                <div class="flex justify-between items-center mb-4 border-b pb-4">
                    <h3 class="text-lg font-medium text-gray-900">Confirm Deletion</h3>
                    <button @click="closeDeleteModal" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <p class="text-sm text-gray-600">
                        Are you sure you want to delete "<span class="font-medium">{{ selectedItem.name }}</span>"? This action cannot be undone.
                    </p>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        @click="closeDeleteModal"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                    >
                        Cancel
                    </button>
                    <button
                        @click="handleDelete"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </vue-final-modal>
    </div>
</template>

<script>
import { VueFinalModal } from 'vue-final-modal';

// Click outside directive
const clickOutside = {
    beforeMount(el, binding) {
        el.clickOutsideEvent = event => {
            if (!(el === event.target || el.contains(event.target))) {
                binding.value();
            }
        };
        document.addEventListener('click', el.clickOutsideEvent);
    },
    unmounted(el) {
        document.removeEventListener('click', el.clickOutsideEvent);
    },
};

export default {
    components: { VueFinalModal },
    directives: {
        'click-outside': clickOutside
    },
    data() {
        return {
            items: [],
            searchQuery: '',
            currentPage: 1,
            perPage: 10,
            totalItems: 0,
            lastPage: 1,
            activeDropdown: null,

            showCreate: false,
            showEdit: false,
            showView: false,
            showDelete: false,
            selectedItem: null,

            createForm: { code: '', name: '', sequence: 1, isActive: true },
            editForm: { code: '', name: '', sequence: 1, isActive: true }
        };
    },
    methods: {
        fetchData() {
            const params = {
                page: this.currentPage,
                per_page: this.perPage,
                search: this.searchQuery,
                sort_by: 'id',
                sort_dir: 'asc'
            };

            this.$axios.get('/categories', { params })
                .then(response => {
                    this.items = response.data.result.data;
                    this.totalItems = response.data.result.meta.total;
                    this.lastPage = response.data.result.meta.last_page;
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });
        },
        handleSearch() {
            this.currentPage = 1;
            this.fetchData();
        },
        toggleDropdown(itemId) {
            this.activeDropdown = this.activeDropdown === itemId ? null : itemId;
        },
        showCreateModal() {
            this.createForm = { code: '', name: '', sequence: 1, isActive: true };
            this.showCreate = true;
        },
        handleCreate() {
            this.$axios.post('/categories', this.createForm)
                .then(() => {
                    this.closeCreateModal();
                    this.fetchData();
                })
                .catch(error => {
                    console.error('Error creating item:', error);
                });
        },
        closeCreateModal() {
            this.showCreate = false;
        },
        showEditModal(item) {
            this.selectedItem = { ...item };
            this.editForm = {
                code: item.code,
                name: item.name,
                sequence: item.sequence,
                isActive: item.isActive
            };
            this.showEdit = true;
            this.activeDropdown = null;
        },
        handleEdit() {
            this.$axios.put(`/categories/${this.selectedItem.id}`, this.editForm)
                .then(() => {
                    this.closeEditModal();
                    this.fetchData();
                })
                .catch(error => {
                    console.error('Error updating item:', error);
                });
        },
        closeEditModal() {
            this.showEdit = false;
            this.selectedItem = null;
        },
        showViewModal(item) {
            this.selectedItem = { ...item };
            this.showView = true;
        },
        closeViewModal() {
            this.showView = false;
            this.selectedItem = null;
        },
        confirmDelete(item) {
            this.selectedItem = { ...item };
            this.showDelete = true;
            this.activeDropdown = null;
        },
        closeDeleteModal() {
            this.showDelete = false;
            this.selectedItem = null;
        },
        handleDelete() {
            this.$axios.delete(`/categories/${this.selectedItem.id}`)
                .then(() => {
                    this.closeDeleteModal();
                    this.fetchData();
                })
                .catch(error => {
                    console.error('Error deleting item:', error);
                });
        }
    },
    mounted() {
        this.fetchData();
    }
};
</script>

<style scoped>
/* Add any custom styles here */
</style>
