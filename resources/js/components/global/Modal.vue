<template>
    <!-- Create Modal -->
    <vfm-modal v-model="createModalOpen" class="flex items-center justify-center">
        <vfm-container class="max-w-2xl w-full mx-4">
            <vfm-content class="bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                <div class="flex justify-between items-center border-b p-4">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Create New Record</h3>
                    <vfm-close class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </vfm-close>
                </div>
                <div class="p-4">
                    <slot name="create-form"></slot>
                </div>
                <div class="flex justify-end p-4 border-t">
                    <button @click="closeCreateModal" class="btn btn-outline mr-2">Cancel</button>
                    <button @click="$emit('create-submit')" class="btn btn-primary">Create</button>
                </div>
            </vfm-content>
        </vfm-container>
    </vfm-modal>

    <!-- Edit Modal -->
    <vfm-modal v-model="editModalOpen" class="flex items-center justify-center">
        <vfm-container class="max-w-2xl w-full mx-4">
            <vfm-content class="bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                <div class="flex justify-between items-center border-b p-4">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Edit Record</h3>
                    <vfm-close class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </vfm-close>
                </div>
                <div class="p-4">
                    <slot name="edit-form"></slot>
                </div>
                <div class="flex justify-end p-4 border-t">
                    <button @click="closeEditModal" class="btn btn-outline mr-2">Cancel</button>
                    <button @click="$emit('edit-submit')" class="btn btn-primary">Update</button>
                </div>
            </vfm-content>
        </vfm-container>
    </vfm-modal>

    <!-- View Modal -->
    <vfm-modal v-model="viewModalOpen" class="flex items-center justify-center">
        <vfm-container class="max-w-2xl w-full mx-4">
            <vfm-content class="bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                <div class="flex justify-between items-center border-b p-4">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">View Details</h3>
                    <vfm-close class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </vfm-close>
                </div>
                <div class="p-4">
                    <slot name="view-content"></slot>
                </div>
                <div class="flex justify-end p-4 border-t">
                    <button @click="closeViewModal" class="btn btn-outline">Close</button>
                </div>
            </vfm-content>
        </vfm-container>
    </vfm-modal>
</template>

<script>
import { ref } from 'vue'
import { useModal } from 'vue-final-modal'

export default {
    setup() {
        const { open: openCreateModal, close: closeCreateModal } = useModal({
            component: 'vfm-modal',
            attrs: {
                onClickOutside: () => closeCreateModal()
            }
        })

        const { open: openEditModal, close: closeEditModal } = useModal({
            component: 'vfm-modal',
            attrs: {
                onClickOutside: () => closeEditModal()
            }
        })

        const { open: openViewModal, close: closeViewModal } = useModal({
            component: 'vfm-modal',
            attrs: {
                onClickOutside: () => closeViewModal()
            }
        })

        return {
            createModalOpen: ref(false),
            editModalOpen: ref(false),
            viewModalOpen: ref(false),
            openCreateModal,
            closeCreateModal,
            openEditModal,
            closeEditModal,
            openViewModal,
            closeViewModal
        }
    }
}
</script>
