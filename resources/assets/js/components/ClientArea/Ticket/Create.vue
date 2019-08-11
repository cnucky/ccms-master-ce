<template>
    <div class="ui grid">
        <div class="ui sixteen wide column">
            <h1 class="ui header">创建工单</h1>
        </div>

        <div class="ui sixteen wide column">
            <div class="ui very padded no-shadow segment" v-bind:class="{loading: isLoading}">
                <form class="ui form" v-on:submit.prevent="store">
                    <div class="ui two fields">
                        <div class="ui field">
                            <label class="required">部门</label>
                            <div ref="departmentSelect" class="ui selection dropdown">
                                <i class="dropdown icon"></i>
                                <div class="default text">请选择部门</div>
                                <div class="menu">
                                    <div class="item" v-for="ticketDepartment in ticketDepartments" :value="ticketDepartment.id" v-on:click="() => {ticket.department_id = ticketDepartment.id; selectedDepartment = ticketDepartment;}">
                                        {{ ticketDepartment.name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ui field">
                            <label class="required">优先级</label>
                            <select class="ui dropdown" v-model="ticket.priority">
                                <option value="0">低</option>
                                <option value="1">中</option>
                                <option value="2">高</option>
                            </select>
                        </div>
                    </div>

                    <div v-if="ticket.department_id">
                        <div v-if="selectedDepartment.description" class="ui info message">
                            {{ selectedDepartment.description }}
                        </div>

                        <div class="ui field">
                            <label class="required">主题</label>
                            <input type="text" v-model="ticket.title" maxlength="32" required>
                        </div>

                        <div v-if="selectedDepartment.show_relative_service_select" class="ui field">
                            <label>相关服务</label>
                            <relative-service-select v-on:selected="relativeServiceSelected"></relative-service-select>
                        </div>

                        <div class="ui field">
                            <label class="required">详情</label>
                            <textarea v-model="ticket.content" maxlength="65535" required></textarea>
                        </div>

                        <div class="ui field">
                            <button type="submit" class="ui small fluid teal button" v-bind:class="{loading: isSubmitting}" :disable="isSubmitting">提交</button>
                        </div>
                    </div>
                    <div v-else>
                        <div class="ui warning message" style="display: block;">
                            请选择部门
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import RelativeServiceSelect from "./RelativeServiceSelect";
    export default {
        name: "Create",
        components: {RelativeServiceSelect},
        data: function () {
            return {
                isLoading: false,
                isSubmitting: false,
                ticketDepartments: [],
                selectedDepartment: {},
                ticket: {
                    department_id: null,
                    priority: "0",
                    title: "",
                    content: "",
                    product_type: null,
                    relative_service_id: null,
                },
            };
        },
        created: function () {
            this.loadTicketDepartments();
        },
        mounted: function () {
            $(".ui.dropdown").dropdown();
        },
        methods: {
            loadTicketDepartments: function () {
                this.isLoading = true;
                axios.get(route('ticketDepartments.client.index'), {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.ticketDepartments = data.departments;
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
            relativeServiceSelected: function (type, id) {
                this.ticket.product_type = type;
                this.ticket.relative_service_id = id;
            },
            store: function () {
                this.isSubmitting = true;
                axios.post(route("tickets.store"), this.ticket, {vueInstance: this})
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            this.$router.push({name: "tickets.show", params: {id: data.ticket.id}});
                            this.positiveFloatingMessage("工单创建成功");
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