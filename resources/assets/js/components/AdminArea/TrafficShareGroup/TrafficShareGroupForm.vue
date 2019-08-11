<template>
    <form class="ui form" v-on:submit.prevent="$emit('submit')">
        <div class="ui field">
            <label>名称</label>
            <input type="text" :value="name" v-on:input="$emit('name', $event.target.value)" required>
        </div>

        <div class="ui two fields">
            <div class="ui field">
                <label>下行单价</label>
                <div class="ui right labeled input">
                    <label for="rx-price" class="ui label">{{ $store.getters.defaultCurrency.prefix }}</label>
                    <input id="rx-price" type="number" step="0.0001" min="0" max="99.9999" :value="pricePerRxGib" v-on:input="$emit('pricePerRXGiB', $event.target.value)" disabled>
                    <div class="ui basic label">/GiB</div>
                </div>
            </div>
            <div class="ui field">
                <label>上行单价</label>
                <div class="ui right labeled input">
                    <label for="rx-price" class="ui label">{{ $store.getters.defaultCurrency.prefix }}</label>
                    <input id="tx-price" type="number" step="0.0001" min="0" max="99.9999" :value="pricePerTxGib" v-on:input="$emit('pricePerTXGiB', $event.target.value)" required>
                    <div class="ui basic label">/GiB</div>
                </div>
            </div>
        </div>

        <div class="ui field">
            <label>描述</label>
            <textarea rows="3" :value="description" v-on:input="$emit('description', $event.target.value)"></textarea>
        </div>

        <div class="ui field">
            <button type="submit" class="ui small teal fluid button" v-bind:class="{loading: isSubmitting}" :disabled="isSubmitting">保存</button>
        </div>
    </form>
</template>

<script>
    export default {
        name: "TrafficShareGroupForm",
        props: ["isSubmitting", "name", "pricePerRxGib", "pricePerTxGib", "description"],
    }
</script>

<style scoped>

</style>