<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">更新证书</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui top attached info message">
                当前所保存的证书与私钥已隐藏，若无需更改证书，留空即可
            </div>
            <div class="ui very padded bottom attached segment">
                <div class="ui form" style="margin-bottom: 15px;">
                    <div class="ui field">
                        <label>指纹</label>
                        <input type="text" :value="certificate.fingerprint" readonly>
                    </div>
                    <div class="ui field">
                        <label>Subject</label>
                        <input type="text" :value="certificate.subject" readonly>
                    </div>
                    <div class="ui field">
                        <label>有效至</label>
                        <input type="text" :value="certificate.valid_to" readonly>
                    </div>
                </div>
                <certificate-form
                        :is-submitting="isSubmitting"
                        :name="certificateForm.name"
                        :private-key="certificateForm.privateKey"
                        :certificate="certificateForm.certificate"
                        v-on:name="(value) => {certificateForm.name = value}"
                        v-on:privateKey="(value) => {certificateForm.privateKey = value}"
                        v-on:certificate="(value) => {certificateForm.certificate = value}"
                        v-on:submit="update"></certificate-form>
            </div>
        </div>
    </div>
</template>

<script>
    import CertificateForm from "./CertificateForm";
    export default {
        name: "Edit",
        components: {CertificateForm},
        data: function () {
            return {
                isLoading: true,
                isSubmitting: false,
                certificate: {},
                certificateForm: {},
            };
        },
        created: function () {
            this.load();
        },
        methods: {
            load: function () {
                axios.get(route("certificates.edit", [this.certificateId]), {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.certificate = data.certificate;
                            this.certificateForm = _.cloneDeep(data.certificate);
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isLoading = false;
                    })
                ;
            },
            update: function () {
                this.isSubmitting = true;
                axios.patch(route("certificates.update", [this.certificateId]), this.certificateForm, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.certificate = data.certificate;
                            this.positiveFloatingMessage("证书信息更新成功");
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isSubmitting = false;
                    })
                ;
            }
        },
        computed: {
            certificateId: function () {
                return this.$router.currentRoute.params.id;
            }
        }
    }
</script>

<style scoped>

</style>