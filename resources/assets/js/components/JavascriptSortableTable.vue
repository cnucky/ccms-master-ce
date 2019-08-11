<template>
    <table>
        <thead>
        <tr>
            <th v-if="selectable">
                <div class="ui fitted checkbox" ref="master_checkbox">
                    <input type="checkbox" name="all_regions">
                    <label></label>
                </div>
            </th>

            <th v-for="(text, key) in columns" @click="sortBy(key)"
                :class="{ sorted: sortKey === key, ascending: sortOrders[key] > 0, descending: sortOrders[key] <= 0}">{{ text }}</th>

            <th v-if="operable" :width="operationColumnLength"></th>
        </tr>
        </thead>
        <tbody v-if="isLoading">
            <tr>
                <td colspan="6"><div class="ui active centered inline loader"></div></td>
            </tr>
        </tbody>
        <slide-fade-transition-group v-else tag="tbody" ref="tbody">
            <tr v-for="entry in filteredData" :key="entry.id">
                <td v-if="selectable">
                    <div class="ui fitted child checkbox">
                        <input type="checkbox" v-model="isItemSelected[entry[itemKey]]">
                    </div>
                </td>
                <td v-for="(text, key) in columns">
                    <component v-bind:is="columnComponents[key]" :key-name="key" :entry="entry"></component>
                </td>
                <td v-if="operable">
                    <component v-bind:is="operationComponent" :entry="entry" v-on:remove-entry="destroy"></component>
                </td>
            </tr>
        </slide-fade-transition-group>
    </table>
</template>

<script>
    export default {
        name: "JavascriptSortableTable",
        props: {
            data: [Object, Array],
            columns: Object,
            columnComponents: Object,
            filterKey: String,
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
            operationComponent: String,
            isLoading: Boolean,
        },
        data: function () {
            var sortOrders = {};
            Object.keys(this.columns).forEach(function (key) {
                sortOrders[key] = 1
            });

            var isItemSelected = {};
            if (this.selectable) {
                Object.keys(this.data).forEach((key) => {
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
        computed: {
            filteredData: function () {
                var sortKey = this.sortKey;
                var filterKey = this.filterKey && this.filterKey.toLowerCase();
                var order = this.sortOrders[sortKey] || 1;
                var data = this.data;
                if (filterKey) {
                    data = data.filter(function (row) {
                        return Object.keys(row).some(function (key) {
                            return String(row[key]).toLowerCase().indexOf(filterKey) > -1
                        })
                    })
                }
                if (sortKey) {
                    data = data.slice().sort(function (a, b) {
                        a = a[sortKey];
                        b = b[sortKey];
                        return (a === b ? 0 : a > b ? 1 : -1) * order
                    })
                }
                return data
            },
            selectedItemList: function () {
                var selectedItemList = [];
                for (var i in this.isItemSelected) {
                    if (this.isItemSelected[i]) {
                        selectedItemList.push(i);
                    }
                }

                return selectedItemList;
            },
            selectedItemCount: function () {
                return this.selectedItemList.length;
            }
        },
        methods: {
            sortBy: function (key) {
                this.sortKey = key;
                this.sortOrders[key] = this.sortOrders[key] * -1
            },
            destroy: function (entry) {
                for (var i in this.data) {
                    console.log(this.data[i][this.itemKey] + "===" + entry[this.itemKey] + " => " + (this.data[i][this.itemKey] === entry[this.itemKey]));
                    if (this.data[i][this.itemKey] === entry[this.itemKey]) {
                        if (Array.isArray(this.data)) {
                            this.data.splice(i, 1);
                        } else {
                            this.$delete(this.data, i);
                        }
                        console.log(this.data);
                        break;
                    }
                }
            }
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