<template>
    <div class="ui tiny teal pointing dropdown button" ref="dropdown_menu" v-bind:class="{ loading: isLoading, disabled: isLoading || isDisabled }">
        <div class="text">{{ currentText }}</div>
        <i class="dropdown icon"></i>
        <div class="menu">
            <slot></slot>
        </div>
    </div>
</template>

<script>
    export default {
        name: "DropdownMenu",
        props: ["text"],
        data: function () {
            return {
                isLoading: false,
                loadingText: null,
                isDisabled: false,
                disabledText: null,
            }
        },
        mounted: function () {
            $(this.$refs.dropdown_menu).dropdown({
                on: "hover",
                placeholder: false,
                action: "hide",
            });
        },
        methods: {
            setLoading: function (loadingText = null) {
                this.isLoading = true;
                this.loadingText = loadingText;
            },
            clearLoading: function () {
                this.isLoading = false;
                this.loadingText = null;
            },
            setDisabled: function (disabledText = "已删除") {
                this.isDisabled = true;
                this.disabledText = disabledText;
            }
        },
        computed: {
            currentText: function () {
                if (this.isLoading && typeof this.loadingText === "string") {
                    return this.loadingText;
                } else if (this.isDisabled) {
                    return this.disabledText;
                }
                return this.text;
            }
        }
    }
</script>

<style scoped>

</style>