<template>
    <div class="ui gird">
        <div class="sixteen wide column">
            <h3 class="ui header">{{ operationName }}余额</h3>
            <div class="ui very padded no-shadow segment">
                <credit-record-form
                        :is-submitting="isSubmitting"
                        :amount="creditRecordForm.amount"
                        :description="creditRecordForm.description"
                        v-on:amount="(value) => { creditRecordForm.amount = value }"
                        v-on:description="(value) => { creditRecordForm.description = value }"
                        v-on:submit="submit"
                ></credit-record-form>
            </div>
        </div>
    </div>
</template>

<script>
    import CreditRecordForm from "./CreditRecordForm";
    export default {
        name: "CreditOperation",
        components: {CreditRecordForm},
        props: ["userData"],
        data: function () {
            return {
                isSubmitting: false,
                operationName: "",
                creditRecordForm: {
                    amount: 0,
                    description: "",
                },
                routeName: "",
            };
        },
        methods: {
            submit: function () {
                this.isSubmitting = true;
                this.$axiosPost(route(this.routeName, [this.userData.user.id]), this.creditRecordForm, (data) => {
                    this.$router.push({name: 'users.credit', params: {id: this.userData.user.id}});
                    this.creditRecordForm.amount = 0;
                    this.creditRecordForm.description = "";
                    this.userData.user.credit = data.newCredit;
                    this.positiveFloatingMessage("操作成功");
                }, () => {
                    this.isSubmitting = false;
                });
            }
        }
    }
</script>

<style scoped>

</style>