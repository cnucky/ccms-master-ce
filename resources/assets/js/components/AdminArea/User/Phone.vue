<template>
    <span>
        <template v-if="country && $store.getters.countryPhoneCode(country)"><i :class="country + ' flag'"></i>+{{ $store.getters.countryPhoneCode(country) }} </template>{{ phone }}
    </span>
</template>

<script>
    export default {
        name: "Phone",
        props: ["country", "phone"],
    }
</script>

<style scoped>

</style>