<template>
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-3">
      <span class="text-sm text-gray-700">
        Showing <span class="font-medium">{{ startItem }}</span> to <span class="font-medium">{{ endItem }}</span> of <span class="font-medium">{{ totalItems }}</span> results
      </span>
            <select
                v-model="localPerPage"
                @change="$emit('update:perPage', +localPerPage)"
                class="block w-20 pl-3 pr-8 py-2 text-sm border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary rounded-md shadow-sm transition-all"
            >
                <option v-for="size in [5, 10, 15, 20, 50]" :key="size" :value="size">{{ size }}</option>
            </select>
        </div>

        <div class="flex gap-1">
            <!-- First Page -->
            <button @click="goTo(1)" :disabled="currentPage === 1" :class="buttonClass(currentPage === 1)">
                <i class="fas fa-angles-left"></i>
            </button>

            <!-- Previous Page -->
            <button @click="goTo(currentPage - 1)" :disabled="currentPage === 1" :class="buttonClass(currentPage === 1)">
                <i class="fas fa-angle-left"></i>
            </button>

            <!-- Page Numbers -->
            <template v-for="page in visiblePages" :key="page">
                <button
                    @click="goTo(page)"
                    :class="[page === currentPage ? activeButtonClass : defaultButtonClass]"
                >
                    {{ page }}
                </button>
            </template>

            <!-- Next Page -->
            <button @click="goTo(currentPage + 1)" :disabled="currentPage === lastPage" :class="buttonClass(currentPage === lastPage)">
                <i class="fas fa-angle-right"></i>
            </button>

            <!-- Last Page -->
            <button @click="goTo(lastPage)" :disabled="currentPage === lastPage" :class="buttonClass(currentPage === lastPage)">
                <i class="fas fa-angles-right"></i>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Pagination',
    props: {
        totalItems: { type: Number, required: true },
        perPage: { type: Number, required: true },
        currentPage: { type: Number, required: true },
        lastPage: { type: Number, required: true }
    },
    data() {
        return {
            localPerPage: this.perPage
        };
    },
    watch: {
        perPage(newVal) {
            this.localPerPage = newVal;
        }
    },
    computed: {
        startItem() {
            return this.totalItems === 0 ? 0 : (this.currentPage - 1) * this.perPage + 1;
        },
        endItem() {
            return Math.min(this.currentPage * this.perPage, this.totalItems);
        },
        visiblePages() {
            const range = 2;
            const start = Math.max(1, this.currentPage - range);
            const end = Math.min(this.lastPage, this.currentPage + range);
            return Array.from({ length: end - start + 1 }, (_, i) => start + i);
        },
        defaultButtonClass() {
            return 'bg-white text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-md border border-gray-300 text-sm font-medium shadow-sm transition-all';
        },
        activeButtonClass() {
            return 'bg-primary text-white px-3 py-1.5 rounded-md border border-gray-300 text-sm font-medium shadow-sm transition-all';
        }
    },
    methods: {
        goTo(page) {
            if (page >= 1 && page <= this.lastPage && page !== this.currentPage) {
                this.$emit('update:currentPage', page);
            }
        },
        buttonClass(disabled) {
            return disabled
                ? 'bg-gray-100 text-gray-400 cursor-not-allowed px-3 py-1.5 rounded-md border border-gray-300 text-sm font-medium shadow-sm'
                : 'bg-white text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-md border border-gray-300 text-sm font-medium shadow-sm transition-all';
        }
    }
};
</script>
