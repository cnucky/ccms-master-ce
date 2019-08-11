<template>
    <div class="ui grid">
        <div class="ten wide middle aligned column">
            <model-index-refresh-button :is-loading="isLoading" v-on:click="filter"></model-index-refresh-button>

            <model-index-mass-destroy-button :selectedItemCount="selectedItemCount()" v-on:click="massDestroy"></model-index-mass-destroy-button>

            <button class="ui tiny green button" :disabled="!selectedItemCount()" v-on:click="massClose"><i class="check icon"></i> 完成</button>

            <div ref="statusSelect" class="ui mini selection dropdown" v-bind:class="{loading: isLoading}">
                <input type="hidden" v-on:change="statusChanged">
                <i class="dropdown icon"></i>
                <div class="default text">Any status</div>
                <div class="menu">
                    <div class="item" :data-value="-1">Any status</div>
                    <div class="item" :data-value="0" style="color: green;">{{ $t('status.ticket.0') }}</div>
                    <div class="item" :data-value="1" style="color: black;">{{ $t('status.ticket.1') }}</div>
                    <div class="item" :data-value="2" style="color: orange;">{{ $t('status.ticket.2') }}</div>
                    <div class="item" :data-value="3" style="color: blue;">{{ $t('status.ticket.3') }}</div>
                    <div class="item" :data-value="5" style="color: teal;">{{ $t('status.ticket.5') }}</div>
                    <div class="item" :data-value="6" style="color: grey;">{{ $t('status.ticket.6') }}</div>
                </div>
            </div>

            <div ref="prioritySelect" class="ui mini selection dropdown" v-bind:class="{loading: isLoading}">
                <input type="hidden" v-on:change="priorityChanged">
                <i class="dropdown icon"></i>
                <div class="default text">Any priority</div>
                <div class="menu">
                    <div class="item" :data-value="-1">Any priority</div>
                    <div class="item" :data-value="0" style="color: teal;">低</div>
                    <div class="item" :data-value="1" style="color: orange;">中</div>
                    <div class="item" :data-value="2" style="color: red;">高</div>
                </div>
            </div>

            <index-table-search-input v-model="filterKey" v-bind:is-loading="isLoading" v-on:search="filter"></index-table-search-input>
        </div>

        <div class="six wide middle right aligned column">
            <div style="display: inline;">
                <model-index-page-number-input v-model="page" v-bind:paginator="paginator"
                                               v-on:page-change="filter"></model-index-page-number-input>
            </div>

            <div style="display: inline; margin-left: 10px;">
                <model-index-pre-page-item-selector v-model="prePageItem"
                                                    v-on:pre-page-item-change="filter"
                                                    v-bind:is-loading="isLoading"></model-index-pre-page-item-selector>
            </div>
        </div>

        <div class="sixteen wide column">
            <semantic-ui-loader v-bind:is-active="isLoading"></semantic-ui-loader>
            <sortable-table
                    id="region-table"
                    class="sortable unstackable"
                    :data="items"
                    :paginator="paginator"
                    :filter-key="filterKey"
                    :selectable="true"
                    :columns="columns"
                    :column-components="columnComponents"
                    :operable="true"
                    operation-component="admin-ticket-operation-column"
                    :is-loading="isLoading"
                    :sort-disabled="{
                        title: true,
                    }"
                    ref="listTable"
                    v-on:sort-by="sortBy"
                    v-on:prev="prevPage"
                    v-on:next="nextPage"
                    v-on:jump-to="jumpToPage"
            ></sortable-table>
        </div>
    </div>
</template>

<script>
    import Vue from 'vue';
    import indexOperation from "./../../ModelIndex/IndexOperation";
    import pageOperation from "./../../ModelIndex/PageOperation";
    import StatusSelect from "./StatusSelect";
    import MassDestroyButton from "../../ModelIndex/MassDestroyButton";

    Vue.component('admin-ticket-link-column', require("./Column/LinkColumn"));
    Vue.component('ticket-department-column', require("./../../ClientArea/Ticket/Column/DepartmentColumn"));
    Vue.component('ticket-index-user-column', require("./../ComputeInstance/Column/UserColumn"));
    Vue.component('admin-ticket-operation-column', require("./Column/Operation"));
    Vue.component('ticket-status-column', require("./../../Ticket/StautsColumn"));
    Vue.component('ticket-priority-column', require("./../../Ticket/PriorityColumn"));

    export default {
        name: "IndexTable",
        components: {MassDestroyButton, StatusSelect},
        mixins: [indexOperation, pageOperation],
        data: function () {
            return {
                isLoading: false,
                columns: {
                    id: "ID",
                    priority: "",
                    department_id: "部门",
                    user_id: "用户",
                    title: "标题",
                    status: "状态",
                    replied_at: "最后回复于",
                },
                columnComponents: {
                    id: "admin-ticket-link-column",
                    priority: "ticket-priority-column",
                    user_id: "ticket-index-user-column",
                    department_id: "ticket-department-column",
                    title: "admin-ticket-link-column",
                    status: "ticket-status-column",
                    replied_at: "duration-column",
                },

                indexRouteName: "admin.tickets.index",
                massDestroyRouteName : "admin.tickets.massDestroy",

                selectedStatus: null,
                selectedPriority: null,
            };
        },
        created: function () {
            var query = this.$router.currentRoute.query;
            if (query.hasOwnProperty("status"))
                this.selectedStatus = query.status;
            if (query.hasOwnProperty("priority"))
                this.selectedPriority = query.priority;
        },
        mounted: function () {
            $(this.$refs.statusSelect).dropdown({
                placeholder: false,
            });
            if (this.selectedStatus !== null) {
                if (this.$te("status.ticket." + this.selectedStatus)) {
                    $(this.$refs.statusSelect).dropdown("set text", this.$t("status.ticket." + this.selectedStatus));
                }
            }

            $(this.$refs.prioritySelect).dropdown();
            if (this.selectedPriority !== null) {
                var text = "高";
                if (this.selectedPriority === "0")
                    text = "低";
                else if (this.selectedPriority === "1")
                    text = "中";
                $(this.$refs.prioritySelect).dropdown("set text", text);
            }

            this.$nextTick(() => {
                this.filter();
            });
        },
        methods: {
            loadSuccessCallback: function (data) {
                var items = data.tickets;
                this.items = items.data;
                this.paginator = items;
            },
            additionalFilterArguments: function (filterArguments) {
                if (this.selectedStatus !== "" && this.selectedStatus != -1 && this.selectedStatus !== null) {
                    filterArguments.status = this.selectedStatus;
                }
                if (this.selectedPriority !== "" && this.selectedPriority != -1 && this.selectedPriority !== null) {
                    filterArguments.priority = this.selectedPriority;
                }
                return filterArguments;
            },
            statusChanged: function (event) {
                this.selectedStatus = event.target.value;
                this.filter();
            },
            priorityChanged: function (event) {
                this.selectedPriority = event.target.value;
                this.filter();
            },
            massClose: function () {
                this.$root.confirmModal()
                    .withHeader("注意")
                    .withContent("确定把选定的工单标记为已完成吗？")
                    .withOnApprove(() => {
                        this.isLoading = true;
                        var items = this.$refs.listTable.selectedItemList;
                        axios
                            .post(route("admin.tickets.massClose"), { items: items }, {vueInstance: this})
                            .then((response) => {
                                if (response.data.result) {
                                    this.filter();
                                }
                            })
                            .catch(this.$errorToNegativeFloatingMessage)
                            .then(() => {
                                this.isLoading = false;
                            })
                        ;
                    })
                    .show()
                ;
            }
        },
    }
</script>

<style scoped>

</style>