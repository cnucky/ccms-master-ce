<template>
    <div id="floating-message" class="floating-message">
        <transition-group name="floating-message-transition" tag="div">
            <template v-for="(messageItem, id) in messages">
                <semantic-ui-delay-callback-message v-on:timeout="timeout" :key="id" :id="id" :delay="messageItem.delay" class="floating" :type="messageItem.type" :messages="messageItem.messages" :closable="true"></semantic-ui-delay-callback-message>
            </template>
        </transition-group>
    </div>
</template>

<script>
    export default {
        name: "FloatingMessage",
        data: function () {
            return {
                messages: {},
            };
        },
        methods: {
            createMessageId: function () {
                return Date.now().toString() + Math.random();
            },
            showMessages: function (type, messages, header = "提示", delay = 5000) {
                if (messages === null || messages === undefined)
                    return;

                this.$set(this.messages, this.createMessageId(), {
                    type: type,
                    header: header,
                    delay: delay,
                    messages: messages
                });
            },
            timeout: function (id) {
                this.$delete(this.messages, id);
            }
        }
    }
</script>

<style scoped>
    #floating-message {
        position: fixed;
        top: 80px;
        right: 40px;
        width: 400px;
        z-index: 3000;
    }

    .floating-message-transition-enter-active {
        transition: all .5s ease;
    }

    .floating-message-transition-leave-active {
        transition: all .5s cubic-bezier(1.0, 0.5, 0.8, 1.0);
    }

    .floating-message-transition-enter, .floating-message-transition-leave-to {
        transform: translateX(50px);
        opacity: 0;
    }

    .floating-message-transition-move {
        transition: transform .5s;
    }
</style>