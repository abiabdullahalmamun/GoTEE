<template>

</template>

<script>
import { VueFinalModal } from 'vue-final-modal';

export default {
    name: 'BaseModal',
    components: { VueFinalModal },
    props: {
        modelValue: {
            type: Boolean,
            required: true
        },
        title: {
            type: String,
            default: 'Modal Title'
        },
        classes: {
            type: String,
            default: 'modal-container'
        },
        contentClass: {
            type: String,
            default: 'modal-content'
        }
    },
    emits: ['update:modelValue'],
    methods: {
        handleClose() {
            this.$emit('update:modelValue', false);
        }
    }
};
</script>
