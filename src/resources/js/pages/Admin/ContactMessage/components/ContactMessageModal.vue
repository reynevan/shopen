<script setup>
import BaseModal from "@shopen/components/admin/ui/BaseModal.vue";
import ActionButton from "@shopen/components/admin/ui/ActionButton.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import ActionButtons from "@shopen/components/admin/ui/ActionButtons.vue";
import Button from "@shopen/components/admin/ui/Button.vue";

const props = defineProps({
    message: { type: Object, required: true },
    show: { type: Boolean, default: false },
});

const emits = defineEmits(["onClose"]);

const form = useForm({ message: "" });
const loading = ref(false);
const responseFormVisible = ref(false);

const showResponseForm = () => {
    responseFormVisible.value = true;
};

const deleteMessage = () => {
    loading.value = true;
    router.delete(route("admin.contact-messages.delete", props.message.id), {
        preserveScroll: true,
        onSuccess: () => {
            loading.value = false;
            emits("onClose");
        },
        onFinish: () => (loading.value = false),
    });
};

const sendResponse = () => {
    loading.value = true;
    form.post(route("admin.contact-messages.respond", props.message), {
        preserveScroll: true,
        onSuccess: () => {
            loading.value = false;
            emits("onClose");
        },
        onFinish: () => (loading.value = false),
    });
};

// Estetyczne formaty (opcjonalnie, jeżeli dostajesz już sformatowaną datę – zostaw jak jest)
const hasResponses = computed(() => (props.message?.responses?.length || 0) > 0);
</script>

<template>
    <BaseModal :show="show" @onClose="emits('onClose')" class="w-full max-w-5xl">
        <!-- Header -->
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="text-xl font-semibold tracking-tight">Wiadomość kontaktowa</div>
                    <span class="inline-flex items-center bg-neutral-100 px-2.5 py-0.5 text-xs font-medium text-neutral-700 border border-neutral-200">
                        ID #{{ message.id }}
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-sm text-neutral-500">
                    <div class="inline-flex items-center gap-1">
                        <i class="bi bi-calendar-event"></i>
                        <span>{{ message.created_at }}</span>
                    </div>
                    <div class="hidden sm:block h-4 w-px bg-neutral-200"></div>
                    <div class="inline-flex items-center gap-1">
                        <i class="bi bi-envelope-at"></i>
                        <span class="truncate max-w-[220px]" :title="message.email">{{ message.email }}</span>
                    </div>
                </div>
            </div>
        </template>

        <!-- Body -->
        <template #default>
            <!-- Nadawca i metadane -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border border-neutral-200 bg-white ">
                    <div class="px-4 py-3 border-b border-neutral-200 bg-neutral-50/60">
                        <div class="text-xs font-medium uppercase tracking-wide text-neutral-600">Nadawca</div>
                    </div>
                    <div class="px-4 py-3 space-y-1.5">
                        <div class="text-sm font-medium text-neutral-900">{{ message.name }}</div>
                        <div class="text-sm text-neutral-600">{{ message.email }}</div>
                    </div>
                </div>

                <div class="border border-neutral-200 bg-white ">
                    <div class="px-4 py-3 border-b border-neutral-200 bg-neutral-50/60">
                        <div class="text-xs font-medium uppercase tracking-wide text-neutral-600">Szczegóły</div>
                    </div>
                    <div class="px-4 py-3 space-y-1.5">
                        <div class="text-xs uppercase text-neutral-500">Data utworzenia</div>
                        <div class="text-sm text-neutral-900">{{ message.created_at }}</div>
                    </div>
                </div>
            </div>

            <!-- Temat -->
            <div class="mt-6 border border-neutral-200 bg-white ">
                <div class="px-4 py-3 border-b border-neutral-200 bg-neutral-50/60">
                    <div class="text-xs font-medium uppercase tracking-wide text-neutral-600">Temat</div>
                </div>
                <div class="px-4 py-4">
                    <div class="text-base font-medium text-neutral-900 leading-relaxed">
                        {{ message.subject }}
                    </div>
                </div>
            </div>

            <!-- Treść wiadomości -->
            <div class="mt-4 border border-neutral-200 bg-white ">
                <div class="px-4 py-3 border-b border-neutral-200 bg-neutral-50/60 ">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-medium uppercase tracking-wide text-neutral-600">Wiadomość</div>
                        <span class="text-[11px] text-neutral-400">od użytkownika</span>
                    </div>
                </div>
                <div class="px-4 py-4">
                    <div
                        class="prose prose-sm max-w-none prose-headings:mt-4 prose-p:my-2 prose-ul:my-2 prose-ol:my-2
                   text-neutral-800 leading-relaxed whitespace-pre-wrap"
                        v-html="message.message"
                    />
                </div>
            </div>

            <!-- Odpowiedzi -->
            <div v-if="hasResponses" class="mt-6 border border-neutral-200 bg-white ">
                <div class="px-4 py-3 border-b border-neutral-200 bg-neutral-50/60">
                    <div class="text-xs font-medium uppercase tracking-wide text-neutral-600">Odpowiedzi</div>
                </div>

                <div class="divide-y divide-neutral-200">
                    <div
                        v-for="response in message.responses"
                        :key="response.id"
                        class="px-4 py-4"
                    >
                        <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
                            <div class="flex items-center gap-2">
                                <div>
                                    <div class="text-sm font-medium text-neutral-900">{{ response.user?.email }}</div>
                                    <div class="text-xs text-neutral-500">Udzielono odpowiedzi</div>
                                </div>
                            </div>
                            <div class="text-sm text-neutral-500">
                                {{ response.created_at }}
                            </div>
                        </div>
                        <div
                            class="border border-neutral-200 bg-neutral-50/50 px-3 py-3 text-neutral-800 whitespace-pre-wrap"
                            v-html="response.message"
                        />
                    </div>
                </div>
            </div>

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

            <!-- Formularz odpowiedzi -->
            <div
                class="mt-4 border border-neutral-200 bg-white "
                v-show="responseFormVisible"
            >
                <div class="px-4 py-3 border-b border-neutral-200 bg-neutral-50/60 ">
                    <div class="text-xs font-medium uppercase tracking-wide text-neutral-600">Odpowiedź</div>
                </div>
                <div class="px-4 py-4 space-y-3">
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
                                @click="sendResponse"
                            >
                                Wyślij
                            </ActionButton>
                        </ActionButtons>
                    </div>
                </div>
            </div>
        </template>

        <!-- Buttons -->
        <template #buttons>
            <div class="flex justify-end w-full">
                <ActionButton
                    @click="deleteMessage"
                    type="remove"
                    :loading="loading"
                >
                    Usuń
                </ActionButton>
            </div>
        </template>
    </BaseModal>
</template>