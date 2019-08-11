<template>
    <span>{{ typeText }}</span>
</template>

<script>
    export default {
        name: "Type",
        props: ["typeCode"],
        computed: {
            typeText: function () {
                if (this.$te("creditRecordType." + this.typeCode)) {
                    return this.$t("creditRecordType." + this.typeCode);
                }
                return this.$t("common.unknown");
            },
        }
    }
</script>

<style scoped>

</style>