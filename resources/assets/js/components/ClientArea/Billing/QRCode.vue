<template>
    <div style="display: inline-block;"></div>
</template>

<script>
    export default {
        name: "QRCode",
        props: {
            text: {
                type: String,
            },
            width: {
                default: 128,
            },
            height: {
                default: 128,
            },
            tradeNo: {
                default: null
            },
        },
        mounted: function () {
            var qrcode = new QRCode(this.$el, {
                text: this.text,
                width: this.width,
                height: this.height,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        },
    }
</script>

<style scoped>

</style>