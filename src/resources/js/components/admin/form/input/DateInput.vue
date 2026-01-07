<script setup>

import VueDatePicker from "@vuepic/vue-datepicker";
import '@vuepic/vue-datepicker/dist/main.css'

const model = defineModel();

</script>

<template>
    <VueDatePicker v-model="model"
                   :enable-time-picker="false"
                   :clearable="true"
                   format="dd-MM-yyyy"
                   model-type="dd-MM-yyyy"
                   select-text="Wybierz"
                   :auto-apply="true"
                   locale="pl-PL"
                   :day-names="['Pon', 'Wt', 'Śr', 'Czw', 'Pt', 'Sob', 'Nie']"
                   cancel-text="Anuluj"/>
</template>