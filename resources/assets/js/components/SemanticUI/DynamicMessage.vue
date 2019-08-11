<template>
    <semantic-ui-message v-bind:type="type" v-bind:messages="messages" v-bind:header="header"></semantic-ui-message>
</template>

<script>
    export default {
        name: "DynamicMessage",
        data: function () {
            return {
                type: "",
                messages: {},
                header: null,
            };
        },
        methods: {
            init: function () {
                this.type = "";
                this.messages = {};
                this.header = null;
                return this;
            },
            withHeader: function (header = null) {
                this.header = header;
                return this;
            },
            withType: function (type) {
                this.type = type;
                return this;
            },
            withMessages: function (messages) {
                this.messages = messages;
                return this;
            },
            showMessages: function (type, messages) {
                this
                    .init()
                    .withType(type)
                    .withMessages(messages)
                ;
            },
            positiveMessage: function (messages) {
                this.showMessages("positive", messages);
            },
            negativeMessages: function (messages) {
                this.showMessages("negative", messages);
            },
        }
    }
</script>

<style scoped>

</style>