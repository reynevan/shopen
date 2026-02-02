<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import ActionButtons from "@shopen/components/admin/ui/ActionButtons.vue";
import ActionButton from "@shopen/components/admin/ui/ActionButton.vue";
import Button from "@shopen/components/admin/ui/Button.vue";
import {router, useForm} from "@inertiajs/vue3";
import {computed, ref} from "vue";
import Panel from "../../../components/admin/ui/Panel.vue";
import PageTitle from "../../../components/admin/ui/PageTitle.vue";

defineOptions({layout: AdminLayout})

const props = defineProps({
    message: {type: Object, required: true},
})

const form = useForm({ message: "" });
const loading = ref(false);
const responseFormVisible = ref(false);

const showResponseForm = () => {
    responseFormVisible.value = true;
};

const deleteMessage = () => {
    loading.value = true;
    router.delete(route("admin.contact-messages.delete", props.message.id), {
        preserveScroll: true
    });
};

const sendResponse = () => {
    loading.value = true;
    form.post(route("admin.contact-messages.respond", props.message), {
        preserveScroll: true,
        onSuccess: () => {
            loading.value = false;
            form.reset();
            responseFormVisible.value = false;
        },
        onFinish: () => (loading.value = false),
    });
};

const hasResponses = computed(() => (props.message?.responses?.length || 0) > 0);

</script>
<template>
    <ActionsPanel back-route="admin.contact-messages.index">
        <template #title>
            <PageTitle>Wiadomość #{{ message.id }}</PageTitle>
        </template>
    </ActionsPanel>
    <section class="max-w-4xl mx-auto">

        <Panel>
            <template #header>
                Szczegóły
            </template>
            <div class="flex gap-4 divide-x divide-light">
                <div class="pr-2">
                    <div class="text-xs uppercase text-neutral-500">Nadawca</div>
                    <div class="text-sm"><i class="bi bi-person"></i> {{ message.name }}</div>
                    <div class="text-sm"><i class="bi bi-envelope-at"></i> {{ message.email }}</div>
                    <div class="text-sm" v-if="message.phone"><i class="bi bi-phone"></i> {{ message.phone }}</div>
                </div>
                <div class="pl-2">
                    <div class="text-xs uppercase text-neutral-500">Data wysłania</div>
                    <div class="text-sm"><i class="bi bi-calendar-event"></i> {{ message.created_at }}</div>
                </div>
            </div>
        </Panel>

        <Panel>
            <template #header>
                Temat
            </template>
            <div class="text-base leading-relaxed">
                {{ message.subject }}
            </div>
        </Panel>

        <Panel>
            <template #header>
                <div class="flex items-center justify-between">
                    <div>Wiadomość</div>
                    <span class="text-xs uppercase">od użytkownika</span>
                </div>
            </template>
            <div class="prose prose-sm max-w-none prose-headings:mt-4 prose-p:my-2 prose-ul:my-2 prose-ol:my-2 leading-relaxed whitespace-pre-wrap text-lg"
                v-html="message.message"
            ></div>
        </Panel>

        <Panel v-if="hasResponses">
            <template #header>
                Odpowiedzi
            </template>
            <div class="divide-y divide-neutral-200 space-y-4">
                <div v-for="response in message.responses" :key="response.id">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
                        <div class="flex items-center gap-2">
                            <div>
                                <div class="flex gap-2 divide-x divide-light">
                                    <div class="text-sm pr-2" v-if="response.user?.first_name && response.user?.last_name">
                                        {{ response.user?.first_name }} {{ response.user?.last_name }}
                                    </div>
                                    <div class="text-sm font-medium" v-if="response.user?.email">{{ response.user?.email }}</div>
                                </div>
                                <div class="text-xs text-neutral-500">Udzielono odpowiedzi</div>
                            </div>
                        </div>
                        <div class="text-sm text-neutral-500">
                            {{ response.created_at }}
                        </div>
                    </div>
                    <div class="border border-neutral-200 bg-neutral-50/50 px-3 py-3 text-neutral-800 whitespace-pre-wrap"
                        v-html="response.message"
                    />
                </div>
            </div>
        </Panel>


        <!-- Akcja: Wyślij odpowiedź -->
        <div class="flex justify-end w-full mt-6">
            <Button
                type="primary"
                size="md"
                @click="showResponseForm"
                v-show="!responseFormVisible"
            >
                <i class="bi bi-arrow-return-right rotate-180"></i>
                <span>Wyślij odpowiedź</span>
            </Button>
        </div>

        <Panel v-show="responseFormVisible">
            <template #header>
                Odpowiedź
            </template>
            <div class="space-y-3">
                <label class="block text-sm font-medium text-neutral-700">Treść wiadomości</label>
                <textarea
                    v-model="form.message"
                    rows="6"
                    class="input mb-4 py-1"
                    placeholder="Napisz odpowiedź dla użytkownika..."
                ></textarea>
                <div class="flex justify-end">
                    <ActionButtons>
                        <ActionButton type="cancel" @click="() => responseFormVisible = false">Anuluj</ActionButton>
                        <ActionButton
                            type="mail"
                            :loading="loading"
                            @click="sendResponse">
                            Wyślij
                        </ActionButton>
                    </ActionButtons>
                </div>
            </div>
        </Panel>
    </section>
</template>