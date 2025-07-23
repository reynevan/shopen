import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useVariantStore = defineStore('variant', () => {
    const attributes = ref([])
    const variants = ref([])
    const selectedOptions = ref({})
    const errors = ref({})

    const selectedVariant = computed(() => {
        if (!variants.value) {
            return null;
        }
        return variants.value.find(variant => {
            return Object.entries(variant.attributes).every(
                ([code, optionId]) => selectedOptions.value[code] === optionId
            )
        }) || null
    })

    const isOptionSelected = (attributeCode, optionId) => {
        return selectedOptions.value[attributeCode] === optionId;
    }

    const isOptionDisabled = (attributeCode, optionId) => {
        const selected = { ...selectedOptions.value }

        delete selected[attributeCode]

        const hasOtherSelections = Object.keys(selected).length > 0
        if (!hasOtherSelections) return false

        const matchingVariants = variants.value.filter(variant => {
            return Object.entries(selected).every(([code, id]) => {
                return variant.attributes[code] === id
            })
        })

        return !matchingVariants.some(variant => variant.attributes[attributeCode] === optionId)
    }

    function setAttributesAndVariants(attr, vars) {
        attributes.value = attr
        variants.value = vars
        selectedOptions.value = {}
        errors.value = {}
    }

    function selectOption(attributeCode, optionId) {
        selectedOptions.value[attributeCode] = optionId
        errors.value[attributeCode] = ''
    }

    function validate() {
        errors.value = {};
        let valid = true;
        attributes.value.forEach(attr => {
            const code = attr.attribute.code;
            if (!selectedOptions.value[code]) {
                errors.value[code] = 'Wybierz opcję';
                valid = false;
            }
        })
        return valid;
    }

    return {
        attributes,
        variants,
        selectedOptions,
        errors,
        selectedVariant,
        isOptionDisabled,
        isOptionSelected,
        setAttributesAndVariants,
        selectOption,
        validate
    }
})
