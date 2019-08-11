<template>
    <flex-container>
        <div style="text-align: center">
            <h2 v-if="this.$router.currentRoute.params.tradeNo" class="ui header">订单号：{{ this.$router.currentRoute.params.tradeNo }}</h2>

            <p>
                系统正在处理您的付款结果，请稍后。
                如果您已付款，但金额未到账，请到用户中心提交工单联系客服。
            </p>
        </div>
    </flex-container>
</template>

<script>
    import FlexContainer from "../../FlexContainer";
    export default {
        name: "PaymentReturn",
        components: {FlexContainer},
    }
</script>

<style scoped>
</style>