<template>
    <div class="ui mini right labeled input">
        <label for="page-number-input" class="ui label">第</label>
        <input type="number" :value="value" min="1" :max="maxPage" id="page-number-input" v-on:change="validate" v-on:blur="validate" v-on:keyup.enter="validate">
        <div class="ui basic label">页</div>
    </div>
</template>

<script>
    export default {
        name: "PageNumberInput",
        props: ["value", "paginator"],
        data: function () {
            return {
            }
        },
        created: function () {
            var query = this.$router.currentRoute.query;

            var page = 1;
            if (query.hasOwnProperty("page")) {
                var userInputPage = parseInt(query.page);
                if (!Number.isNaN(userInputPage))
                    page = userInputPage;
            }

            this.$emit('input', page);
        },
        methods: {
            isUserInputValueValid: function () {
                var userInputValue = parseInt(event.target.value);

                if (Number.isNaN(userInputValue) || userInputValue < 1 || userInputValue > this.maxPage)
                    return false;
                return userInputValue;
            },
            validate: function (event) {
                var userInputValue = this.isUserInputValueValid();

                if (userInputValue === false) {
                    event.target.value = this.value;
                    this.negativeFloatingMessage("页码必须小于或等于" + this.maxPage + "，且大于0");
                    return;
                }

                if (this.value == userInputValue)
                    return;

                this.$emit('input', userInputValue);
                this.$emit('page-change', userInputValue);
            }
        },
        computed: {
            maxPage: function () {
                return this.paginator.last_page;
            }
        }
    }
</script>

<style scoped>
    input {
        width: 75px;
    }
</style>