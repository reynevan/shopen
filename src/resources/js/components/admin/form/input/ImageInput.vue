<script setup>

defineProps({
    id: {type: String, required: true},
})
const emits = defineEmits(['selected'])

const onFileChange = (payload) => {
    const file = payload.target.files[0];
    if (file) {
        emits('selected', file);
    }
}
</script>

<template>

    <div
        class="mt-2 flex justify-center items-center rounded-lg border border-dashed border-gray-900/25 hover:border-gray-900/35 transition-border relative w-[180px] h-[180px]">
        <label :for="id" class="group cursor-pointer block text-center py-10 px-6">
            <i class="bi bi-card-image text-4xl text-gray-900/25 group-hover:text-gray-900/35 transition-all"></i>
            <div class="mt-4 flex text-sm/6 text-gray-600">
                <div
                    class="relative cursor-pointer rounded-md font-semibold text-indigo-600 group-hover:text-indigo-500 transition-all">
                    <span>Upload a file or drag and drop</span>
                    <input :id="id" name="file-upload" type="file" accept="image/*" class="sr-only"
                           @change="onFileChange">
                </div>
            </div>
        </label>
    </div>

</template>

<style scoped>

</style>