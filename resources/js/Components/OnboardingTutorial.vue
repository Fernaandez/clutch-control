<template>
    <Teleport to="body">
        <Transition name="tutorial-fade">
            <div
                v-if="show"
                class="tutorial-overlay"
                @click.self="handleOverlayClick"
            >
                <div class="tutorial-card">
                    <!-- Close button -->
                    <button class="tutorial-close" @click="skipTutorial" :title="$t('tutorial.skip')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Progress dots -->
                    <div class="tutorial-dots">
                        <button
                            v-for="(step, index) in steps"
                            :key="index"
                            class="tutorial-dot"
                            :class="{ active: index === currentStep, passed: index < currentStep }"
                            @click="goToStep(index)"
                        />
                    </div>

                    <!-- Step content with transition -->
                    <Transition :name="transitionName" mode="out-in">
                        <div :key="currentStep" class="tutorial-step-content">
                            <!-- Icon -->
                            <div class="tutorial-icon-wrap" :style="{ background: steps[currentStep].iconBg }">
                                <span class="tutorial-icon-emoji">{{ steps[currentStep].icon }}</span>
                            </div>

                            <!-- Title -->
                            <h2 class="tutorial-title">{{ $t(steps[currentStep].titleKey) }}</h2>

                            <!-- Description -->
                            <p class="tutorial-description">{{ $t(steps[currentStep].descKey) }}</p>

                            <!-- Permission request block -->
                            <div v-if="steps[currentStep].permission && !permissionGranted" class="tutorial-permission-block">
                                <div class="tutorial-permission-icon">
                                    {{ steps[currentStep].permissionIcon }}
                                </div>
                                <p class="tutorial-permission-text">{{ $t(steps[currentStep].permissionTextKey) }}</p>
                                <button
                                    class="tutorial-permission-btn"
                                    @click="requestPermission(steps[currentStep].permission)"
                                    :disabled="isRequestingPermission"
                                >
                                    <span v-if="isRequestingPermission">⏳ {{ $t('tutorial.requesting') }}</span>
                                    <span v-else>{{ $t(steps[currentStep].permissionBtnKey) }}</span>
                                </button>
                            </div>

                            <!-- Permission already granted indicator -->
                            <div v-if="steps[currentStep].permission && permissionGranted" class="tutorial-permission-ok">
                                ✅ {{ $t('tutorial.permission_granted') }}
                            </div>
                        </div>
                    </Transition>

                    <!-- Navigation buttons -->
                    <div class="tutorial-nav">
                        <button
                            v-if="currentStep > 0"
                            class="tutorial-btn-back"
                            @click="prevStep"
                        >
                            ← {{ $t('tutorial.back') }}
                        </button>
                        <div v-else class="tutorial-btn-spacer" />

                        <button
                            v-if="currentStep < steps.length - 1"
                            class="tutorial-btn-next"
                            @click="nextStep"
                        >
                            {{ $t('tutorial.next') }} →
                        </button>
                        <button
                            v-else
                            class="tutorial-btn-finish"
                            @click="finishTutorial"
                        >
                            🚀 {{ $t('tutorial.start') }}
                        </button>
                    </div>

                    <!-- Step counter -->
                    <p class="tutorial-counter">{{ currentStep + 1 }} / {{ steps.length }}</p>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Capacitor } from '@capacitor/core';

const TUTORIAL_KEY = 'clutch_tutorial_done_v1';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue', 'done']);

const show = ref(false);
const currentStep = ref(0);
const transitionName = ref('tutorial-slide-left');
const isRequestingPermission = ref(false);
const permissionGranted = ref(false);

const steps = [
    {
        icon: '👋',
        iconBg: 'linear-gradient(135deg, rgba(12,225,181,0.15), rgba(12,225,181,0.05))',
        titleKey: 'tutorial.step1_title',
        descKey: 'tutorial.step1_desc',
        permission: null,
    },
    {
        icon: '🏍️',
        iconBg: 'linear-gradient(135deg, rgba(96,165,250,0.15), rgba(96,165,250,0.05))',
        titleKey: 'tutorial.step2_title',
        descKey: 'tutorial.step2_desc',
        permission: null,
    },
    {
        icon: '📍',
        iconBg: 'linear-gradient(135deg, rgba(34,197,94,0.15), rgba(34,197,94,0.05))',
        titleKey: 'tutorial.step3_title',
        descKey: 'tutorial.step3_desc',
        permission: 'location',
        permissionIcon: '🗺️',
        permissionTextKey: 'tutorial.step3_permission_text',
        permissionBtnKey: 'tutorial.step3_permission_btn',
    },
    {
        icon: '📅',
        iconBg: 'linear-gradient(135deg, rgba(251,146,60,0.15), rgba(251,146,60,0.05))',
        titleKey: 'tutorial.step4_title',
        descKey: 'tutorial.step4_desc',
        permission: null,
    },
    {
        icon: '💬',
        iconBg: 'linear-gradient(135deg, rgba(168,85,247,0.15), rgba(168,85,247,0.05))',
        titleKey: 'tutorial.step5_title',
        descKey: 'tutorial.step5_desc',
        permission: 'notifications',
        permissionIcon: '🔔',
        permissionTextKey: 'tutorial.step5_permission_text',
        permissionBtnKey: 'tutorial.step5_permission_btn',
    },
    {
        icon: '🛒',
        iconBg: 'linear-gradient(135deg, rgba(234,179,8,0.15), rgba(234,179,8,0.05))',
        titleKey: 'tutorial.step6_title',
        descKey: 'tutorial.step6_desc',
        permission: null,
    },
    {
        icon: '🚀',
        iconBg: 'linear-gradient(135deg, rgba(12,225,181,0.2), rgba(12,225,181,0.05))',
        titleKey: 'tutorial.step7_title',
        descKey: 'tutorial.step7_desc',
        permission: null,
    },
];

onMounted(() => {
    const done = localStorage.getItem(TUTORIAL_KEY);
    if (!done) {
        // Small delay so the app renders first
        setTimeout(() => {
            show.value = true;
        }, 800);
    }
});

watch(currentStep, (newVal, oldVal) => {
    transitionName.value = newVal > oldVal ? 'tutorial-slide-left' : 'tutorial-slide-right';
    // Reset permission state when changing steps
    permissionGranted.value = false;
    isRequestingPermission.value = false;
});

function goToStep(index) {
    currentStep.value = index;
}

function nextStep() {
    if (currentStep.value < steps.length - 1) {
        currentStep.value++;
    }
}

function prevStep() {
    if (currentStep.value > 0) {
        currentStep.value--;
    }
}

function markDone() {
    localStorage.setItem(TUTORIAL_KEY, '1');
    show.value = false;
    emit('done');
}

function finishTutorial() {
    markDone();
}

function skipTutorial() {
    markDone();
}

function handleOverlayClick() {
    // Don't close on overlay click — user must explicitly skip or finish
}

async function requestPermission(type) {
    if (!Capacitor.isNativePlatform()) {
        // On web, just mark as granted for demo purposes
        permissionGranted.value = true;
        return;
    }

    isRequestingPermission.value = true;

    try {
        if (type === 'location') {
            const { Geolocation } = await import('@capacitor/geolocation');
            const result = await Geolocation.requestPermissions();
            permissionGranted.value = result.location === 'granted' || result.coarseLocation === 'granted';
        } else if (type === 'notifications') {
            const { PushNotifications } = await import('@capacitor/push-notifications');
            const result = await PushNotifications.requestPermissions();
            permissionGranted.value = result.receive === 'granted';
            if (permissionGranted.value) {
                await PushNotifications.register();
            }
        }
    } catch (e) {
        console.error('Permission request failed:', e);
        permissionGranted.value = false;
    } finally {
        isRequestingPermission.value = false;
    }
}
</script>

<style scoped>
.tutorial-overlay {
    position: fixed;
    inset: 0;
    z-index: 99999;
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(6px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.25rem;
    padding-bottom: calc(1.25rem + env(safe-area-inset-bottom));
    padding-top: calc(1.25rem + env(safe-area-inset-top));
}

.tutorial-card {
    background: #161b22;
    border: 1px solid rgba(12, 225, 181, 0.2);
    border-radius: 1.5rem;
    padding: 2rem 1.75rem 1.5rem;
    width: 100%;
    max-width: 400px;
    position: relative;
    box-shadow:
        0 0 0 1px rgba(12,225,181,0.08),
        0 25px 50px rgba(0, 0, 0, 0.6),
        0 0 60px rgba(12,225,181,0.06);
    overflow: hidden;
}

.tutorial-card::before {
    content: '';
    position: absolute;
    top: -60px;
    right: -60px;
    width: 180px;
    height: 180px;
    background: radial-gradient(circle, rgba(12,225,181,0.08) 0%, transparent 70%);
    pointer-events: none;
}

.tutorial-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    color: rgba(156, 163, 175, 0.7);
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 0.5rem;
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    z-index: 10;
}

.tutorial-close:hover {
    background: rgba(239, 68, 68, 0.15);
    border-color: rgba(239, 68, 68, 0.3);
    color: #ef4444;
}

/* Progress dots */
.tutorial-dots {
    display: flex;
    gap: 0.4rem;
    justify-content: center;
    margin-bottom: 1.75rem;
}

.tutorial-dot {
    width: 0.45rem;
    height: 0.45rem;
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.15);
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.tutorial-dot.passed {
    background: rgba(12, 225, 181, 0.4);
    width: 0.45rem;
}

.tutorial-dot.active {
    background: #0ce1b5;
    width: 1.5rem;
    box-shadow: 0 0 8px rgba(12, 225, 181, 0.5);
}

/* Step content */
.tutorial-step-content {
    text-align: center;
    min-height: 280px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
}

.tutorial-icon-wrap {
    width: 5rem;
    height: 5rem;
    border-radius: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(255, 255, 255, 0.06);
    margin-bottom: 0.5rem;
    flex-shrink: 0;
}

.tutorial-icon-emoji {
    font-size: 2.25rem;
    line-height: 1;
    filter: drop-shadow(0 2px 8px rgba(0,0,0,0.3));
}

.tutorial-title {
    font-size: 1.35rem;
    font-weight: 900;
    color: #ffffff;
    letter-spacing: -0.02em;
    line-height: 1.2;
    margin: 0;
}

.tutorial-description {
    font-size: 0.9rem;
    color: rgba(156, 163, 175, 0.9);
    line-height: 1.6;
    margin: 0;
    max-width: 320px;
}

/* Permission block */
.tutorial-permission-block {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 1rem;
    padding: 1rem;
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.25rem;
}

.tutorial-permission-icon {
    font-size: 1.5rem;
}

.tutorial-permission-text {
    font-size: 0.78rem;
    color: rgba(156, 163, 175, 0.8);
    text-align: center;
    margin: 0;
    line-height: 1.5;
}

.tutorial-permission-btn {
    background: rgba(12, 225, 181, 0.12);
    border: 1px solid rgba(12, 225, 181, 0.35);
    color: #0ce1b5;
    border-radius: 0.625rem;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 0.5rem 1rem;
    cursor: pointer;
    transition: all 0.2s;
    letter-spacing: 0.02em;
    text-transform: uppercase;
}

.tutorial-permission-btn:hover:not(:disabled) {
    background: rgba(12, 225, 181, 0.2);
    border-color: rgba(12, 225, 181, 0.6);
    box-shadow: 0 0 12px rgba(12, 225, 181, 0.2);
}

.tutorial-permission-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.tutorial-permission-ok {
    font-size: 0.85rem;
    color: #4ade80;
    font-weight: 600;
    padding: 0.5rem 1rem;
    background: rgba(74, 222, 128, 0.08);
    border: 1px solid rgba(74, 222, 128, 0.2);
    border-radius: 0.625rem;
    margin-top: 0.25rem;
}

/* Navigation */
.tutorial-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 1.5rem;
    gap: 0.75rem;
}

.tutorial-btn-spacer {
    flex: 1;
}

.tutorial-btn-back {
    flex: 1;
    padding: 0.7rem 1rem;
    border-radius: 0.75rem;
    font-weight: 700;
    font-size: 0.82rem;
    cursor: pointer;
    transition: all 0.2s;
    background: transparent;
    border: 1px solid rgba(255,255,255,0.1);
    color: rgba(156, 163, 175, 0.8);
    letter-spacing: 0.02em;
}

.tutorial-btn-back:hover {
    border-color: rgba(255,255,255,0.2);
    color: white;
    background: rgba(255,255,255,0.05);
}

.tutorial-btn-next {
    flex: 2;
    padding: 0.7rem 1.5rem;
    border-radius: 0.75rem;
    font-weight: 800;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.25s;
    background: #0ce1b5;
    border: none;
    color: #07111a;
    letter-spacing: 0.02em;
    box-shadow: 0 4px 15px rgba(12, 225, 181, 0.3);
}

.tutorial-btn-next:hover {
    background: #0ff5c8;
    box-shadow: 0 4px 20px rgba(12, 225, 181, 0.5);
    transform: translateY(-1px);
}

.tutorial-btn-finish {
    flex: 2;
    padding: 0.7rem 1.5rem;
    border-radius: 0.75rem;
    font-weight: 800;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.25s;
    background: linear-gradient(135deg, #0ce1b5, #06d6a0);
    border: none;
    color: #07111a;
    letter-spacing: 0.02em;
    box-shadow: 0 4px 20px rgba(12, 225, 181, 0.4);
}

.tutorial-btn-finish:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 25px rgba(12, 225, 181, 0.55);
}

.tutorial-counter {
    text-align: center;
    font-size: 0.7rem;
    color: rgba(156, 163, 175, 0.4);
    margin-top: 0.75rem;
    margin-bottom: 0;
    letter-spacing: 0.05em;
    font-weight: 600;
}

/* ========================
   TRANSITIONS
   ======================== */

/* Overlay fade */
.tutorial-fade-enter-active,
.tutorial-fade-leave-active {
    transition: opacity 0.4s ease, backdrop-filter 0.4s ease;
}

.tutorial-fade-enter-from,
.tutorial-fade-leave-to {
    opacity: 0;
}

/* Slide left (forward) */
.tutorial-slide-left-enter-active,
.tutorial-slide-left-leave-active {
    transition: opacity 0.25s ease, transform 0.25s ease;
}

.tutorial-slide-left-enter-from {
    opacity: 0;
    transform: translateX(30px);
}

.tutorial-slide-left-leave-to {
    opacity: 0;
    transform: translateX(-30px);
}

/* Slide right (back) */
.tutorial-slide-right-enter-active,
.tutorial-slide-right-leave-active {
    transition: opacity 0.25s ease, transform 0.25s ease;
}

.tutorial-slide-right-enter-from {
    opacity: 0;
    transform: translateX(-30px);
}

.tutorial-slide-right-leave-to {
    opacity: 0;
    transform: translateX(30px);
}
</style>
