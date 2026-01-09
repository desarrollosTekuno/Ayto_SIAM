<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <div class="absolute -translate-x-1/2 -top-16 left-1/2">
        <div
            class="w-38 h-38 rounded-full bg-[#7A1F3D] flex items-center justify-center shadow-md"
        >
            <Link href="/">
                <img
                    src="img/LogoPueblaBlanco.png"
                    alt="Puebla"
                    class="w-30"
                />
            </Link>
        </div>
    </div>
</template>
