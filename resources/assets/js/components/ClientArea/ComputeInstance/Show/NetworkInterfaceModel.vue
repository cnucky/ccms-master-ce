<template>
    <span>[{{ this.text }}]</span>
</template>

<script>
    export default {
        name: "NetworkInterfaceModel",
        props: ["networkInterface"],
        computed: {
            text: function () {
                switch (parseInt(this.networkInterface.model)) {
                    case 0:
                        return "virtio";
                    case 1:
                        return "e1000";
                    case 2:
                        return "rtl8139";
                    default:
                        return "unknow";
                }
            }
        }
    }
</script>

<style scoped>

</style>