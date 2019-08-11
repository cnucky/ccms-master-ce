<template>
    <div class="ui longer modal" ref="modal">
        <i class="close icon"></i>
        <div class="header" v-if="header.length > 0">
            {{ header }}
        </div>
        <div class="content" v-bind:class="{scrolling: !noScroll}">
            <semantic-ui-message type="negative" v-bind:messages="errors"></semantic-ui-message>
            <semantic-ui-dynamic-message ref="message"></semantic-ui-dynamic-message>

            <slot></slot>

            <div class="ui checkbox" v-if="!noStaySelect" ref="stay" style="margin-top: 20px;">
                <input type="checkbox" v-model="notStay">
                <label>操作成功后关闭此窗口</label>
            </div>
        </div>
        <div class="actions">
            <div class="ui small cancel button">
                取消
            </div>
            <div class="ui small green right labeled icon button" v-bind:class="{ loading: isLoading }" :disabled="isLoading" v-on:click.prevent="onApprove">
                {{ submitButtonTextValue }}
                <i class="checkmark icon"></i>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "FormModal",
        props: ["modelName", "itemName", "isEditing", "isLoading", "customHeader", "noScroll", "submitButtonText", "noStaySelect"],
        data: function () {
            return {
                notStay: true,
                errors: {},
            };
        },
        created: function () {
        },
        mounted: function () {
            $(this.$refs.stay).checkbox();
        },
        methods: {
            init: function () {
                this.errors = {};
                this.$refs.message.init();
            },
            show: function () {
                this.init();
                $(this.$refs.modal).modal({
                    closable: false,
                    allowMultiple: true,
                }).modal('show');
            },
            submit: function () {
                this.init();
                // this.isLoading = true;
                this.$emit("submit");
            },
            positiveMessage: function (message) {
                this.$refs.message
                    .init()
                    .positiveMessage(message)
                ;
            },
            negativeMessage: function (message) {
                this.$refs.message
                    .init()
                    .negativeMessage(message)
                ;
            },
            hideIfNotStay: function (text = "保存成功") {
                if (this.notStay) {
                    // this.closing = true;
                    if (text !== null)
                        this.positiveFloatingMessage(text);
                    $(this.$refs.modal).modal('hide');
                    /*
                                        setTimeout(() => {
                                            this.closing = false;
                                            $(this.$refs.modal).modal('hide');
                                        }, 1000);
                    */
                }
            },
            hide: function () {
                $(this.$refs.modal).modal('hide');
            },
            onApprove: function () {
                if (this.isLoading) {
                    return;
                }
                this.$emit('submit');
            }
        },
        computed: {
            header: function () {
                if (this.customHeader)
                    return this.customHeader;
                if (this.isEditing)
                    return this.itemName + " - 编辑";
                return "添加" + this.modelName;
            },
            submitButtonTextValue: function () {
                if (this.submitButtonText)
                    return this.submitButtonText;
                return "保存";
            }
        }
    }
</script>

<style scoped>

</style>