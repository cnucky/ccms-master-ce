<template>
    <span>{{ this.text }}</span>
</template>

<script>
    export default {
        name: "VolumeBus",
        props: ["volume"],
        computed: {
            text: function () {
                switch (parseInt(this.volume.bus)) {
                    case 0:
                        return "virtio";
                    case 1:
                        return "scsi";
                    case 2:
                        return "sata";
                    case 3:
                        return "ide";
                    default:
                        return "unknow";
                }
            }
        }
    }
</script>

<style scoped>

</style>