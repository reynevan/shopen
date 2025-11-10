<script setup>

import {ref} from "vue";
import Input from "@shopen/components/admin/form/input/Input.vue";
import Select from "@shopen/components/admin/form/input/Select.vue";
import {Link, router, useForm} from "@inertiajs/vue3";
import ActionButton from "@shopen/components/admin/ui/ActionButton.vue";
import Toggle from "@shopen/components/admin/form/input/Toggle.vue";
import FormField from "@shopen/components/admin/form/FormField.vue";
import {useFlash} from "../../../../../composables/useFlash";
import ErrorMessages from "../../../../../components/admin/form/ErrorMessages.vue";

const props = defineProps({
    variant: {type: Object},
    product: {type: Object},
    attributes: {type: Array},
    index: {type: Number}
})

const flash = useFlash()

const emits = defineEmits(['onRemove'])

const getDefaultFormData = () => {
    const data = {
        attributes: {
            name: props.variant.attributes.name ?? '',
        },
        sku: props.variant.sku ?? '',
        price: {
            price: props.variant.price.price ?? null
        },
        parent_id: props.product.id,
        uses_stock: props.variant.uses_stock ?? false,
        stock_qty: props.variant.stock_qty ?? 0,
    }

    for (const i in props.product.configurable_attributes) {
        let attr = props.product.configurable_attributes[i]
        data.attributes[attr.code] = props.variant.attributes[attr.code]
    }

    return data
}

const form = useForm(getDefaultFormData())

const editing = ref(props.variant.editing)
const loading = ref(false)
const errors = ref({})

const toggleEdit = () => {
    editing.value = !editing.value
}

const cancel = () => {
    editing.value = false
    form.reset()
}

const getOptions = (code) => {
    return props.attributes.find((attribute) => attribute.code === code).options
}

const remove = () => {
    if (!props.variant.id) {
        emits('onRemove')
        return
    }
    if (!confirm('Na pewno chcesz usunąć ten produkt?')) {
        return;
    }
    router.delete(route('admin.products.delete', props.variant.id), {
        preserveState: true,
        preserveScroll: true,
        only: ['variants'],
    })
}

const save = () => {
    loading.value = true
    errors.value = {}
    let request
    if (props.variant.id) {
        request = updateProduct()
    } else {
        request = createProduct()
    }
    request.then(() => {
        editing.value = false
        loading.value = false
    }).catch(error => {
        loading.value = false
        if (error.response.data.errors) {
            errors.value = error.response.data.errors
        } else {
            flash.error(error.response.data.message)
        }
    })
}

const updateProduct = () => {
    return axios.put(route('admin.products.update-variant', props.variant.id), form.data())
}

const createProduct = () => {
    return axios.post(route('admin.products.store-variant'), form.data())

}
</script>

<template>
    <tr>
        <td>
            <span v-if="!editing">
                {{ form.attributes.name }}
            </span>
            <Input v-else v-model="form.attributes.name" placeholder="Nazwa" :error="errors['attributes.name']"/>
            <ErrorMessages :errors="errors['attributes.name']"/>
        </td>
        <td>
            <span v-if="!editing">
                {{ form.sku }}
            </span>
            <Input v-else v-model="form.sku" placeholder="SKU" :error="errors?.sku"/>
            <ErrorMessages :errors="errors?.sku"/>
        </td>
        <td>
            <span v-if="!editing">
                {{ form.price.price }}
            </span>
            <Input v-else type="number" v-model="form.price.price" placeholder="Cena" :error="errors['price.price']"/>
            <ErrorMessages :errors="errors['price.price']"/>
        </td>
        <td v-for="attribute in product.configurable_attributes">
            {{ product.attributes[attribute.code] }}
            <Select :id="variant.id + '-' + attribute.code"
                    :error="errors['attributes.' + attribute.code]"
                    :options="getOptions(attribute.code)"
                    :disabled="!editing"
                    :placeholder="attribute.name"
                    v-model="form.attributes[attribute.code]"/>
            <ErrorMessages :errors="errors['attributes.' + attribute.code]"/>
            <ErrorMessages :errors="errors?.variant"/>
        </td>
        <td>
            <div v-if="editing">
                <FormField label="Stan magazynowy" :label-for="index + '-uses_stock'">
                    <Toggle v-model="form.uses_stock" :id="index + '-uses_stock'"/>
                </FormField>
                <FormField v-show="form.uses_stock" label="Ilość w magazynie" :label-for="index + '-stock_qty'">
                    <Input v-model="form.stock_qty" type="number" :id="index + '-stock_qty'" min="0"/>
                    <ErrorMessages :errors="errors?.stock_qty"/>
                </FormField>
            </div>
            <div v-else>
                <div v-if="form.uses_stock">Ilość w magazynie: {{ form.stock_qty }}</div>
                <div v-else>
                    <i class="bi bi-x-lg"></i>
                </div>
            </div>
        </td>
        <td>
            <div class="flex divide-x">
                <ActionButton @click="save" type="accept" :disabled="!editing" title="Zapisz" :loading="loading">Zapisz</ActionButton>
                <ActionButton @click="toggleEdit" type="edit" v-if="!editing" title="Edytuj" :loading="loading">Edytuj</ActionButton>
                <ActionButton @click="cancel" type="cancel" v-if="editing" title="Anuluj" :loading="loading">Anuluj</ActionButton>
                <ActionButton @click="remove" type="remove" title="Usuń produkt" :loading="loading">Usuń</ActionButton>
                <Link v-if="variant.id" :href="route('admin.products.edit', variant.id)">
                    <ActionButton type="view" :loading="loading">Szczegóły</ActionButton>
                </Link>
            </div>
        </td>
    </tr>
</template>