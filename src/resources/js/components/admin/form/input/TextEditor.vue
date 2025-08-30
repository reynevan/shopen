<script setup>

import {ref, onMounted, shallowRef} from "vue";
import Button from "@shopen/components/admin/ui/Button.vue";
import editorCssUrl from '@resources/css/app.css?url'

const EditorComponent = shallowRef(null);
const model = defineModel();

const image_upload_handler = (blobInfo, progress) => {
    return new Promise((resolve, reject) => {
        const formData = new FormData()
        formData.append('file', blobInfo.blob(), blobInfo.filename())

        axios.post('/admin/api/upload-image', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            },
            onUploadProgress: (e) => {
                if (e.lengthComputable) {
                    progress((e.loaded / e.total) * 100)
                }
            }
        })
            .then((response) => {
                const data = response.data

                if (!data || typeof data.location !== 'string') {
                    reject('Invalid JSON response: ' + JSON.stringify(data))
                    return
                }

                resolve(data.location)
            })
            .catch((error) => {
                if (error.response && error.response.status === 403) {
                    reject({ message: 'HTTP Error: 403', remove: true })
                } else {
                    reject('Image upload failed: ' + (error.message || 'Unknown error'))
                }
            })
    })
}

const init = {
    plugins: 'advlist anchor autolink charmap code fullscreen help image insertdatetime link lists media preview searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | styles | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
    height: 500,
    automatic_uploads: true,
    images_reuse_filename: true,
    file_picker_types: 'image',
    document_base_url: '/',
    relative_urls: false,
    remove_script_host: true,
    convert_urls: true,
    content_css: editorCssUrl,
    body_class: 'prose prose-sm max-w-none',
    images_upload_handler: image_upload_handler,
    file_picker_callback(cb, value, meta) {
        if (meta.filetype === 'image') {
            const input = document.createElement('input')
            input.setAttribute('type', 'file')
            input.setAttribute('accept', 'image/*')
            input.onchange = () => {
                const file = input.files[0]
                const reader = new FileReader()
                reader.onload = () => {
                    const id = 'blobid' + new Date().getTime()
                    const blobCache = tinymce.activeEditor.editorUpload.blobCache
                    const base64 = reader.result.split(',')[1]
                    const blobInfo = blobCache.create(id, file, base64)
                    blobCache.add(blobInfo)
                    cb(blobInfo.blobUri(), { title: file.name })
                }
                reader.readAsDataURL(file)
            }
            input.click()
        }
    },
};

const editing = ref(false);

const toggleEdit = () => {
    editing.value = !editing.value;
}

onMounted(async () => {
    const editorModule = await import('@tinymce/tinymce-vue');

    EditorComponent.value = editorModule.default;
});
</script>

<template>
    <div class="w-full">
        <div class="mb-2">
            <Button size="sm" type="primary" @click="toggleEdit">Edytuj</Button>
        </div>
        <div v-if="!editing"
             class="border border-light w-full px-4 py-2 rounded overflow-y-auto prose prose-sm max-w-none"
             v-html="model"></div>
        <div v-if="EditorComponent && editing">
            <component
                v-model="model"
                id="uuid"
                :init="init"
                :is="EditorComponent"
                />
        </div>
    </div>
</template>