<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">
                收支明细
            </h1>
        </div>

        <div class="sixteen wide column">
            <div class="ui three column grid">
                <div class="column">
                    <div class="ui mini fluid labeled input">
                        <div class="ui label" style="line-height: 17px;">
                            起始时间
                        </div>
                        <input type="datetime-local" v-model="filterForm.startDate">
                    </div>
                </div>
                <div class="column">
                    <div class="ui mini fluid labeled input">
                        <div class="ui label" style="line-height: 17px;">
                            结束时间
                        </div>
                        <input type="datetime-local" v-model="filterForm.endDate">
                    </div>
                </div>

                <div class="column">
                    <select ref="typeSelect" class="ui fluid dropdown" v-model="filterForm.type">
                        <option value="-1">全部类型</option>
                        <option value="0">{{ $t("creditRecordType.0") }}</option>
                        <option value="1">{{ $t("creditRecordType.1") }}</option>
                        <option value="2">{{ $t("creditRecordType.2") }}</option>
                        <option value="3">{{ $t("creditRecordType.3") }}</option>
                        <option value="20">{{ $t("creditRecordType.20") }}</option>
                        <option value="21">{{ $t("creditRecordType.21") }}</option>
                        <option value="22">{{ $t("creditRecordType.22") }}</option>
                        <option value="23">{{ $t("creditRecordType.23") }}</option>
                        <option value="25">{{ $t("creditRecordType.25") }}</option>
                        <option value="26">{{ $t("creditRecordType.26") }}</option>
                    </select>
                </div>
            </div>
        </div>

        <form ref="hiddenSubmitForm" class="ui form" style="display: none;" target="_blank" method="post" :action="route('billing.records.exportIndex')">
            <csrf-field></csrf-field>
            <input type="hidden" name="startDate" v-model="startDate">
            <input type="hidden" name="endDate" v-model="endDate">
            <input type="hidden" name="type" v-model="filterForm.type">
            <button type="submit">提交</button>
        </form>

        <div class="sixteen wide middle right aligned column">
            <div class="ui three column grid">
                <div class="column"></div>
                <div class="center aligned column">
                    <button class="ui tiny button" v-on:click="filter" v-bind:class="{loading: isLoading}">查询</button>
                    <button class="ui tiny button" v-on:click="() => {$refs.hiddenSubmitForm.submit();}">导出</button>
                </div>
                <div class="column">
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
            </div>
        </div>

        <div class="sixteen wide column">
            <semantic-ui-loader v-bind:is-active="isLoading"></semantic-ui-loader>
            <sortable-table
                    class="sortable unstackable"
                    :data="items"
                    :paginator="paginator"
                    :filter-key="filterKey"
                    :selectable="true"
                    :columns="columns"
                    :column-components="columnComponents"
                    :operable="false"
                    operation-component="span"
                    :is-loading="isLoading"
                    :use-slot-row="false"
                    :sort-disabled="{
                        description: true,
                    }"
                    ref="listTable"
                    v-on:sort-by="sortBy"
                    v-on:table-created="tableCreated"
                    v-on:prev="prevPage"
                    v-on:next="nextPage"
                    v-on:jump-to="jumpToPage"
                    v-on:edit-item="editItem"
                    v-on:table-updated="tableUpdated"
            >
            </sortable-table>
        </div>
    </div>
</template>

<script>
    import Vue from "vue";
    import indexOperation from "./../../ModelIndex/IndexOperation";
    import pageOperation from "./../../ModelIndex/PageOperation";

    Vue.component("record-index-type-column", require("./../../CreditRecord/TypeColumn"));
    Vue.component("record-index-amount-column", require("./../../CreditRecord/AmountColumn"));

    export default {
        name: "RecordIndex",
        mixins: [indexOperation, pageOperation],
        data: function () {
            return {
                columns: {
                    id: "ID",
                    type: "类型",
                    created_at: "日期",
                    amount: "金额",
                    description: "描述",
                },
                columnComponents: {
                    id: "show-value",
                    type: "record-index-type-column",
                    created_at: "local-time-column",
                    amount: "record-index-amount-column",
                    description: "long-text-column",
                },

                filterForm: {
                    startDate: "",
                    endDate: "",
                    type: "-1",
                },

                indexRouteName: "billing.records.index",
                massDestroyRouteName: "",
            };
        },
        created: function () {
            var query = this.$router.currentRoute.query;
            var startDate = query.startDate;
            var endDate = query.endDate;
            var type = query.type;

            if (startDate) {
                var localStartDate = this.$moment(startDate).local().format("YYYY-MM-DDTHH:mm");
                if (localStartDate !== "Invalid date")
                    this.filterForm.startDate = localStartDate;
            }
            if (endDate) {
                var localEndDate = this.$moment(endDate).local().format("YYYY-MM-DDTHH:mm");
                if (localEndDate !== "Invalid date")
                    this.filterForm.endDate = localEndDate;
            }

            if (typeof type === "string")
                this.filterForm.type = type;
        },
        mounted: function () {
            $(this.$refs.typeSelect).dropdown();
            this.$nextTick(() => {
                this.filter();
            });
        },
        methods: {
            loadSuccessCallback: function (data) {
                this.paginator = data.records;
                this.items = data.records.data;
            },
            tableCreated: function () {
            },
            editItem: function () {
            },
            additionalFilterArguments: function (filterArguments) {
                filterArguments.startDate = this.startDate;
                filterArguments.endDate = this.endDate;
                filterArguments.type = this.filterForm.type;
                return filterArguments;
            }
        },
        computed: {
            startDate: function () {
                var start = this.$moment(this.filterForm.startDate).utcOffset(new Date().getTimezoneOffset()).format("YYYY-MM-DD HH:mm");
                if (start === "Invalid date")
                    return "";
                return start;
            },
            endDate: function () {
                var end = this.$moment(this.filterForm.endDate).utcOffset(new Date().getTimezoneOffset()).format("YYYY-MM-DD HH:mm");
                if (end === "Invalid date")
                    return "";
                return end;
            },
        }
    }
</script>

<style scoped>

</style>