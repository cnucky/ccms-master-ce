<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">添加证书</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment">
                <certificate-form
                        :is-submitting="isSubmitting"
                        :name="certificateForm.name"
                        :private-key="certificateForm.privateKey"
                        :certificate="certificateForm.certificate"
                        v-on:name="(value) => {certificateForm.name = value}"
                        v-on:privateKey="(value) => {certificateForm.privateKey = value}"
                        v-on:certificate="(value) => {certificateForm.certificate = value}"
                        v-on:submit="store"></certificate-form>
            </div>
        </div>
    </div>
</template>

<script>
    import CertificateForm from "./CertificateForm";
    export default {
        name: "Create",
        components: {CertificateForm},
        data: function () {
            return {
                isSubmitting: false,
                certificateForm: {
                    name: "",
                    privateKey: "",
                    certificate: "",
                },
            };
        },
        methods: {
            store: function () {
                this.isSubmitting = true;
                axios.post(route("certificates.store"), this.certificateForm, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.$router.push({name: "certificates.edit", params: {id: data.certificate.id}});
                            this.positiveFloatingMessage("证书添加成功");
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
        }
    }
</script>

<style scoped>

</style>