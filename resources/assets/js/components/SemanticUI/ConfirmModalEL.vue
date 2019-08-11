<template>
    <div id="confirm-modal" class="ui tiny modal" v-bind:class="modalClass" ref="modal">
        <!-- <i class="close icon"></i> -->
        <div class="header" v-if="header.length > 0">
            {{ header }}
        </div>
        <div v-if="textContent.length" class="content">{{ textContent }}</div>
        <div v-else class="content" v-html="content">
        </div>
        <div class="actions">
            <div class="ui tiny cancel button" v-bind:class="cancelClass" v-if="withCancelButton">
                {{ cancelText }}
            </div>
            <div class="ui tiny ok right labeled icon button" v-bind:class="approveClass">
                {{ confirmText }}
                <i v-bind:class="iconClass"></i>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ConfirmModalEL",
        props: {
            modalClass: {
                default: [],
            },
            header: {
                default: "",
            },
            content: {
                default: "",
            },
            textContent: {
                default: "",
            },
            cancelClass: {
                default: "green",
            },
            withCancelButton: {
                default: true,
            },
            cancelText: {
                default: "取消",
            },
            approveClass: {
                default: "red"
            },
            confirmText: {
                default: "确认",
            },
            iconClass: {
                default: "exclamation icon"
            },
            onApprove: {
                default: () => {},
            },
            onDeny: {
                default: () => {},
            },
        },
        mounted: function () {
            $(this.$refs.modal).modal({
                onDeny: this.onDeny,
                onApprove: this.onApprove,
                onHidden: () => {
                    this.$emit("hide");
                },
                allowMultiple: true,
            }).modal('show');
        }
    }
</script>

<style scoped>
    .ui.modal > .content {
        line-height: 1.4em;
    }
</style>