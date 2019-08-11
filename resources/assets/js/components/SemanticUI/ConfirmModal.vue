<template>
    <confirm-modal-e-l
            v-if="showModal"
            :modal-class="modalClass"
            :header="header"
            :content="content"
            :text-content="textContent"
            :cancel-class="cancelClass"
            :with-cancel-button="withCancelButton"
            :cancel-text="cancelText"
            :approve-class="approveClass"
            :confirm-text="confirmText"
            :icon-class="iconClass"
            :on-approve="onApprove"
            :on-deny="onDeny"
            v-on:hide="() => {showModal = false;}"
    ></confirm-modal-e-l>
</template>

<script>
    import ConfirmModalEL from "./ConfirmModalEL";
    export default {
        name: "ConfirmModal",
        components: {ConfirmModalEL},
        data: function () {
            return {
                showModal: false, // Recreate the modal element, or the modal won't work after shown several times.

                header: "",
                content: "",
                textContent: "",
                withCancelButton: true,
                onApprove: this.hide,
                onDeny: this.hide,
                cancelClass: "",
                approveClass: "",
                cancelText: "",
                confirmText: "",
                iconClass: "",
                modalClass: "",
            };
        },
        methods: {
            init: function () {
                this.withCancelButton = true;
                this.header = "";
                this.content = "";
                this.textContent = "";
                this.onDeny = this.hide;
                this.onApprove = this.hide;
                this.cancelText = "取消";
                this.cancelClass = "green";
                this.approveClass = "red";
                this.confirmText = "确认";
                this.iconClass = "exclamation icon";
                this.modalClass = "";

                return this;
            },
            hide: function () {
                this.showModal = false;
            },
            withoutCancelButton: function () {
                this.withCancelButton = false;
                return this;
            },
            withHeader: function (header) {
                this.header = header;
                return this;
            },
            withContent: function (content) {
                this.content = content;
                return this;
            },
            withTextContent: function (textContent) {
                this.textContent = textContent;
                return this;
            },
            withOnDeny: function (callback) {
                this.onDeny = callback;
                return this;
            },
            withOnApprove: function (callback) {
                this.onApprove = callback;
                return this;
            },
            /*
            withOnHidden: function (callback) {
                this.onHidden = callback;
                return this;
            },
            */
            withCancelText: function (text) {
                this.cancelText = text;
                return this;
            },
            withConfirmText: function (text) {
                this.confirmText = text;
                return this;
            },
            withCancelClass: function (classValue) {
                this.cancelClass = classValue;
                return this;
            },
            withConfirmClass: function (classValue) {
                this.approveClass = classValue;
                return this;
            },
            withIconClass: function (classValue) {
                this.iconClass = classValue;
                return this;
            },
            withModalClass: function (modalClass) {
                this.modalClass = modalClass;
                return this;
            },
            show: function () {
                this.showModal = true;
            },
        }
    }
</script>

<style scoped>
    .ui.modal > .content {
        line-height: 1.4em;
    }
</style>