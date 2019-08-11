<template>
    <div ref="certificateSelect" class="ui selection dropdown">
        <input type="hidden">
        <i class="dropdown icon"></i>
        <div class="text">{{ text }}</div>
        <div class="menu">
            <div class="item" v-for="certificate in certificates" v-on:click="$emit('input', certificate.id)" :data-value="certificate.id">{{ certificate.name }} - {{ certificate.subject }}</div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "CertificateSelect",
        props: ["value", "certificates"],
        mounted: function () {
            $(this.$refs.certificateSelect).dropdown({
                action: "hide",
            });
        },
        computed: {
            text: function () {
                for (var i in this.certificates) {
                    if (this.certificates[i].id === this.value) {
                        return this.certificates[i].name;
                    }
                }
                return "";
            }
        }
    }
</script>

<style scoped>

</style>