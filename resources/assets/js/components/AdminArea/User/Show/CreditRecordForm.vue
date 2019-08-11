<template>
    <form class="ui form" v-on:submit.prevent="$emit('submit')">
        <div class="ui field">
            <label>金额</label>
            <div class="ui labeled input">
                <label for="amount" class="ui label">{{ $store.getters.defaultCurrency.prefix }}</label>
                <input id="amount" type="number" step="0.0001" min="0" max="999999999.9999" required :value="amount" v-on:input="$emit('amount', $event.target.value)">
            </div>
        </div>
        <div class="ui field">
            <label>备注</label>
            <textarea maxlength="65535" :value="description" v-on:input="$emit('description', $event.target.value)"></textarea>
        </div>
        <div class="ui field">
            <button type="submit" class="ui small teal fluid button" v-bind:class="{loading: isSubmitting}" :disalbed="isSubmitting">提交</button>
        </div>
    </form>
</template>

<script>
    export default {
        name: "CreditRecordForm",
        props: ["isSubmitting", "amount", "description"],
    }
</script>

<style scoped>

</style>