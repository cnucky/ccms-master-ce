<template>
    <div class="ui message" v-on:mouseenter="$emit('mouseenter')" v-on:mouseleave="$emit('mouseleave')" :class="type" v-if="messages && Object.keys(messages).length > 0" ref="messageContainer" v-on:click="close">
        <i v-if="closable" class="close icon" v-on:click="close"></i>
        <div v-if="header">
            {{ header }}
        </div>
        <div v-if="typeof messages === 'string'">
            {{ messages }}
        </div>
        <ul v-else class="list">
            <template v-for="message in messages">
                <template v-if="Array.isArray(message)">
                    <li v-for="singleMessage in message">{{ singleMessage }}</li>
                </template>
                <template v-else>
                    <li>{{ message }}</li>
                </template>
            </template>
        </ul>
    </div>
</template>

<script>
    export default {
        name: "BasicMessage",
        props: ["type", "messages", "header", "closable"],
        methods: {
            close: function () {
                // this.messages = {};
                this.$emit('close');
            }
        }
    }
</script>

<style scoped>
    .ui.message {
        cursor: pointer;
        padding: 25px 25px;
    }

    .list li {
        word-wrap: break-word;
    }
</style>