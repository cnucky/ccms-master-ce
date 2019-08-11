<template>
    <div class="ui mini icon input" v-bind:class="{loading: isLoading}">
        <input type="text" placeholder="Search..." v-on:focus="$emit('focus')" v-on:blur="$emit('blur')" v-on:keyup.enter="search" v-bind:value="value" v-on:input="input">
        <i v-if="showClearIconButton" class="circular times link icon" v-on:click="clear"></i>
        <i v-else class="circular search link icon" v-on:click="search"></i>
    </div>
</template>

<script>
    export default {
        name: "IndexTableSearchInput",
        props: ["isLoading", "value"],
        data: function () {
            return {
                changed: false,
            }
        },
        created: function () {
            var query = this.$router.currentRoute.query;

            this.$emit('input', query.search);
        },
        methods: {
            input: function (event) {
                this.changed = true;
                this.$emit('input', event.target.value)
            },
            clear: function (event) {
                this.$emit('input', '');
                this.$emit('search');
            },
            search: function () {
                if (this.isLoading)
                    return;
                this.$emit('search');
                this.changed = false;
            }
        },
        computed: {
            showClearIconButton: function () {
                return !this.changed && this.value && this.value.length;
            }
        }
    }
</script>

<style scoped>

</style>