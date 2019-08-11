<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">#{{ ticket.id }} 工单详情</h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui very padded no-shadow segment" v-bind:class="{loading: isLoading}">
                <div class="ui one column grid">
                    <column name="标题" :inline="true" :dt-style="dtStyle">{{ ticket.title }}</column>
                </div>
                <div class="ui two column grid">
                    <column name="用户" :inline="true" :dt-style="dtStyle">
                        <template v-if="ticket.user"><router-link to="/non-exists">#{{ ticket.user.id }} {{ ticket.user.name }}</router-link></template>
                    </column>
                    <column name="优先级" :inline="true" :dt-style="dtStyle"><priority :priority="ticket.priority"></priority></column>
                    <column name="状态" :inline="true" :dt-style="dtStyle">
                        <status-select v-if="ticket.hasOwnProperty('status')" :is-changing-status="isChangingStatus" :value="ticket.status" v-on:input="changeStatus"></status-select>
                    </column>
                    <column name="部门" :inline="true" :dt-style="dtStyle"><department-select v-if="ticket.hasOwnProperty('department')" :is-changing-department="isChangingDepartment" :departments="departments" :selected-department="ticket.department" v-on:input="changeDepartment"></department-select></column>
                    <column name="创建于" :inline="true" :dt-style="dtStyle"><local-time :time="ticket.created_at"></local-time></column>
                    <column name="最后回复于" :inline="true" :dt-style="dtStyle"><local-time :time="ticket.replied_at"></local-time></column>
                </div>
                <relative-service :dt-style="dtStyle" :product-type="ticket.product_type" :relative-service="relativeService"></relative-service>
                <div class="ui divider" style="margin-top: 30px;"></div>
                <form class="ui form" style="margin-top: 30px;" v-on:submit.prevent="makeReply">
                    <div class="ui field">
                        <label>添加回复</label>
                        <textarea v-model="replyContent" required></textarea>
                    </div>
                    <div class="ui field">
                        <button type="submit" class="ui tiny button" style="display: block; margin-left: auto; margin-right: auto;" v-bind:class="{loading: isSubmittingReply}" :disable="isSubmittingReply">提交</button>
                    </div>
                </form>
            </div>
        </div>

        <div v-for="reply in replies" class="sixteen wide column">
            <div v-if="reply.admin_name" class="ui top attached warning message">
                <router-link v-if="reply.admin_id" :to="{name: 'admins.index', query: {search: reply.admin_id}}">{{ reply.admin_name }} - <b>客服</b></router-link>
                <template v-else>{{ reply.admin_name }} - <b>客服</b></template>
                <span style="float: right"><local-time :time="reply.created_at"></local-time></span>
            </div>
            <div v-else class="ui top attached message">
                <router-link :to="{name: 'users.show', params: {id: ticket.user_id}}">{{ ticket.user.name }} - <b>客户</b></router-link>
                <span style="float: right"><local-time :time="reply.created_at"></local-time></span>
            </div>
            <div class="ui very padded bottom attached segment">
                <div style="white-space: pre;">{{ reply.content }}</div>
            </div>
        </div>

        <div class="sixteen wide column" style="margin-bottom: 100px;"></div>
    </div>
</template>

<script>
    import Status from "../../Ticket/Status";
    import LocalTime from "../../LocalTime";
    import Column from "../../ClientArea/ComputeInstance/Show/Information/Column";
    import StatusSelect from "./StatusSelect";
    import DepartmentSelect from "./DepartmentSelect";
    import Priority from "../../Ticket/Priority";
    import RelativeService from "../../Ticket/AdminRelativeService";
    export default {
        name: "Show",
        components: {RelativeService, Priority, DepartmentSelect, StatusSelect, Column, LocalTime, Status},
        data: function () {
            return {
                dtStyle: {width: '90px'},
                isLoading: false,
                isChangingStatus: false,
                isChangingDepartment: false,
                isSubmittingReply: false,
                ticket: {},
                replies: [],
                departments: [],
                replyContent: "",
                relativeService: null,
            };
        },
        created: function () {
            this.isLoading = true;
            axios.get(route("admin.tickets.show", [this.ticketId]), {vueInstance: this})
                .then((response) => {
                    var data = response.data;
                    if (data.result) {
                        this.ticket = data.ticket;
                        this.replies = data.replies;
                        this.departments = data.departments;
                        this.relativeService = data.relativeService;
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
        mounted: function () {
        },
        methods: {
            makeReply: function () {
                this.isSubmittingReply = true;
                axios.post(route("admin.tickets.makeReply", [this.ticketId]), {content: this.replyContent}, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.replies.unshift(data.reply);
                            this.replyContent = "";
                            this.ticket.status = 1;
                            this.positiveFloatingMessage("回复成功");
                        } else {
                            this.$globalErrnoHandler(data);
                        }
                    })
                    .catch(this.$axiosCatchError2Console)
                    .then(() => {
                        this.isSubmittingReply = false;
                    })
                ;
            },
            changeStatus: function (newValue) {
                if (this.ticket.status === newValue)
                    return;
                this.confirmModal().init()
                    .withHeader("提示")
                    .withContent("确定更改状态为[" + this.$t("status.ticket." + newValue) + "]？")
                    .withOnApprove(() => {
                        this.isChangingStatus = true;
                        axios.patch(route("admin.tickets.changeStatus", [this.ticketId]), {status: newValue}, {vueInstance: this})
                            .then((response) => {
                                var data = response.data;
                                if (data.result) {
                                    this.ticket.status = newValue;
                                    this.positiveFloatingMessage("工单状态更改成功");
                                } else {
                                    this.$globalErrnoHandler(data);
                                }
                            })
                            .catch(this.$axiosCatchError2Console)
                            .then(() => {
                                this.isChangingStatus = false;
                            })
                        ;
                    })
                    .show()
                ;
            },
            changeDepartment: function (newDepartment) {
                if (this.ticket.department_id === newDepartment.id)
                    return;
                this.confirmModal().init()
                    .withHeader("提示")
                    .withContent("确定更改部门为[" + newDepartment.name + "]？")
                    .withOnApprove(() => {
                        this.isChangingDepartment = true;
                        axios.patch(route("admin.tickets.changeDepartment", [this.ticketId]), {department_id: newDepartment.id}, {vueInstance: this})
                            .then((response) => {
                                var data = response.data;
                                if (data.result) {
                                    this.ticket.department_id = newDepartment.id;
                                    this.ticket.department = newDepartment;
                                    this.positiveFloatingMessage("工单部门更改成功");
                                } else {
                                    this.$globalErrnoHandler(data);
                                }
                            })
                            .catch(this.$axiosCatchError2Console)
                            .then(() => {
                                this.isChangingDepartment = false;
                            })
                        ;
                    })
                    .show()
                ;
            }
        },
        computed: {
            ticketId: function () {
                return this.$router.currentRoute.params.id;
            }
        },
        watch: {
            ticket: function (newValue, oldValue) {
                // $(this.$refs.statusSelect).dropdown("set selected", newValue.status.toString());
            }
        }
    }
</script>

<style scoped>

</style>