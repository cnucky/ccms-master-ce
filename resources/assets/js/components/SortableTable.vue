<template>
    <table class="ui table">
        <thead>
        <tr>
            <!-- Show master checkbox if selectable-->
            <th class="one wide" v-if="selectable">
                <div class="ui fitted checkbox" ref="masterCheckbox">
                    <input type="checkbox" v-on:change="masterCheckboxChange" :checked="isAllItemSelected">
                    <label></label>
                </div>
            </th>

            <template v-for="(value, key) in columns">
                <template v-if="typeof value === 'string'">
                    <th @click="sortBy(key)"
                        :class="[{ sorted: sortKey === key}, sortOrders[key] ? 'ascending' : 'descending' ]">{{ value }}</th>
                </template>
                <template v-else>
                    <th v-for="(subValue, subKey) in value" >{{ subValue }}</th>
                </template>
            </template>

            <!-- Show operation column if operable -->
            <th v-if="operable" :width="operationColumnLength"></th>
        </tr>
        </thead>
        <tbody v-if="Object.keys(data).length || forceShowTableBody" ref="tbody">
            <template v-if="useSlotRow">
                <slot></slot>
            </template>
            <template v-else>
                <component v-for="entry in data" :key="entry.id" :is="tableRowComponent" :entry="entry">
                    <td v-if="selectable">
                        <div class="ui fitted child checkbox">
                            <input type="checkbox" v-model="isItemSelected[entry[itemKey]]">
                            <label></label>
                        </div>
                    </td>
                    <template v-for="(value, key) in columns">
                        <component v-if="typeof value === 'string'" :parent-app="$parent" :key="key" v-bind:is="columnComponents[key]" :key-name="key" :entry="entry"></component>
                        <template v-else>
                            <component v-for="(subValue, subKey) in value" :parent-app="$parent" :key="key" v-bind:is="columnComponents[key][subKey]" :key-name="subKey" :entry="entry[key]"></component>
                        </template>
                    </template>
                    <template v-if="operable">
                        <component v-bind:is="operationComponent" :parent-app="$parent" :entry="entry" v-on:remove-entry="destroy" v-on:edit-item="editItem"></component>
                    </template>
                </component>
            </template>
        </tbody>
        <tbody v-else>
        <tr>
            <td class="middle center aligned" style="height: 200px; color: gray;" :colspan="totalColumnCount">
                <h2>Oh！似乎没找到任何一条像样的记录。</h2>
            </td>
        </tr>
        </tbody>
        <tfoot v-if="paginator">
        <tr>
            <th :colspan="totalColumnCount" class="middle right aligned">
                <semantic-ui-pagination class="small" v-on:prev="$emit('prev')" v-on:next="$emit('next')" v-on:jump-to="(page) => { this.$emit('jump-to', page) }" :paginator="paginator"></semantic-ui-pagination>
            </th>
        </tr>
        </tfoot>
    </table>
</template>

<script>
    export default {
        name: "SortableTable",
        props: {
            data: [Object, Array],
            paginator: {
                type: Object,
                default: undefined,
            },
            columns: Object,
            columnComponents: Object,
            selectable: {
                type: Boolean,
                default: true,
            },
            itemKey: {
                type: String,
                default: "id",
            },
            operable: {
                type: Boolean,
                default: true,
            },
            operationColumnLength: {
                type: Number,
                default: 150,
            },
            tableRowComponent: {
                type: String,
                default: "tr",
            },
            forceShowTableBody: {
                type: Boolean,
                default: false,
            },
            useSlotRow: {
                type: Boolean,
                default: false,
            },
            operationComponent: String,
            isLoading: Boolean,
            sortDisabled: {
                default: false,
            },
            dataKeysGetter: {
                type: Function,
                default: function () {
                    return Object.keys(this.data);
                }
            }
        },
        data: function () {
            var sortOrders = {};
            Object.keys(this.columns).forEach(function (key) {
                sortOrders[key] = 1
            });

            var isItemSelected = {};
            if (this.selectable) {
                this.dataKeysGetter().forEach((key) => {
                    var itemKeyValue = this.data[key][this.itemKey];
                    isItemSelected[itemKeyValue] = false;
                });
            }

            return {
                sortKey: '',
                sortOrders: sortOrders,
                isItemSelected: isItemSelected,
            }
        },
        created: function() {
            var query = this.$router.currentRoute.query;
            if (query.hasOwnProperty("sortKey")) {
                this.sortKey = query.sortKey;
                if (query.hasOwnProperty("isAsc"))
                    this.$set(this.sortOrders, this.sortKey, query.isAsc === "1" ? 1 : 0);
            }

            this.$emit("table-created");
        },
        mounted: function() {
            this.$nextTick(() => {
                if (this.selectable) {
                    $(this.$refs.masterCheckbox).checkbox();
/*
                    $(this.$refs.masterCheckbox).checkbox({
                        // check all children
                        onChecked: () => {
                            var
                                $childCheckbox = $(this.$refs.tbody.$el).find('.child.checkbox')
                            ;
                            $childCheckbox.checkbox('check');
                        },
                        // uncheck all children
                        onUnchecked: () => {
                            var
                                $childCheckbox = $(this.$refs.tbody.$el).find('.child.checkbox')
                            ;
                            $childCheckbox.checkbox('uncheck');
                        }
                    });
*/
                }
            })
        },
        updated: function() {
            if (this.selectable) {
                $(".ui.child.checkbox").checkbox();
            }
            this.$emit("table-updated");
        },
        watch: {
            data: function (newData, oldData) {
                // Delete the item in this.isItemSelected which has been deleted
                var dataKeys = {};
                if (this.selectable) {
                    this.dataKeysGetter().forEach((key) => {
                        var itemKeyValue = this.data[key][this.itemKey];
                        dataKeys[itemKeyValue] = true;
                    });
                }

                for (var i in this.isItemSelected) {
                    if (!dataKeys.hasOwnProperty(i))
                        this.$delete(this.isItemSelected, i);
                }
            },
        },
        computed: {
            selectedItemList: function () {
                var selectedItemList = [];
                // Put all selected item into selectedItemList
                Object.keys(this.isItemSelected).forEach((key) => {
                    if (this.isItemSelected[key])
                        selectedItemList.push(key);
                });
                return selectedItemList;
            },
            selectedItemCount: function () {
                return this.selectedItemList.length;
            },
            isAllItemSelected: function () {
                return this.dataKeysGetter().length === this.selectedItemCount;
            },
            isMasterCheckboxChecked: function () {
                return this.isAllItemSelected;
            },
            totalColumnCount: function () {
                var count = Object.keys(this.columns).length;
                if (this.selectable)
                    ++count;
                if (this.operable)
                    ++count;
                return count;
            },
        },
        methods: {
            itemKeyValue: function (item) {
                return item[this.itemKey];
            },
            sortBy: function (key) {
                if (this.sortDisabled === true)
                    return;
                else if (typeof this.sortDisabled === "object" && this.sortDisabled.hasOwnProperty(key))
                    return;
                this.sortKey = key;
                this.$set(this.sortOrders, key, !this.sortOrders[key] ? 1 : 0);
                this.$emit("sort-by", key, this.sortOrders[key]);
            },
            changeSelectStatusByItem: function (item, selectStatus) {
                var itemKeyValue = this.itemKeyValue(item);
                this.$set(this.isItemSelected, itemKeyValue, selectStatus);
            },
            changeAllItemSelectStatus: function (selectStatus) {
                this.dataKeysGetter().forEach((key) => {
                    this.changeSelectStatusByItem(this.data[key], selectStatus);
                });
            },
            selectAllItem: function () {
                this.changeAllItemSelectStatus(true);
            },
            unselectAllItem: function () {
                this.isItemSelected = {};
            },
            masterCheckboxChange: function () {
                if (!this.isMasterCheckboxChecked)
                    this.selectAllItem();
                else
                    this.unselectAllItem();
            },
            editItem: function (item) {
                this.$emit('edit-item', item);
            },
            destroy: function (entry) {
                var keyValue = entry[this.itemKey];
                this.destroyByKey(keyValue);
            },
            findItemIndexByKeyValue: function (key) {
                var index = -1;
                this.dataKeysGetter().some((i) => {
                    // Search the key value equal to the entry key value
                    if (this.data[i][this.itemKey] == key) {
                        index = i;
                        return true;
                    }
                    return false;
                });

                return index;
            },
            findItemIndex: function (item) {
                return this.findItemIndexByKeyValue(item.id);
            },
            destroyByKey: function (key) {
                this.dataKeysGetter().some((i) => {
                    // Search the key value equal to the entry key value
                    if (this.data[i][this.itemKey] == key) {
                        if (Array.isArray(this.data)) {
                            this.data.splice(i, 1);
                        } else {
                            this.$delete(this.data, i);
                        }
                        this.$delete(this.isItemSelected, key);
                        return true;
                    }
                    return false;
                });
            },
        }
    }
</script>

<style scoped>
    .ui.table td {
        vertical-align: middle;
    }

    .ui.table .checkbox {
        margin: 0 0 0 0;
        width: 17px;
        height: 17px;
        display: block;
    }
</style>