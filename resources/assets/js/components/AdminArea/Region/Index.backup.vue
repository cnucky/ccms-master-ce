<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1>区域</h1>
        </div>

        <div class="eight wide column">
            <button class="ui tiny icon button" v-on:click.prevent="loadRegions" v-bind:class="{ loading: isLoading }">
                <i class="sync icon"></i>
            </button>

            <router-link class="ui teal tiny button" :href="route('regions.create')" :to="route('regions.create')"><i class="plus icon"></i> 添加区域</router-link>

            <button class="ui red inverted tiny button" :disabled="selectedItemCount <= 0"><i class="delete icon"></i> 删除</button>

            <div class="ui mini icon input">
                <input type="text" placeholder="Search..." v-model="filterKey">
                <i class="search icon"></i>
            </div>
        </div>

        <div class="eight wide column"></div>

        <div class="sixteen wide column">
            <table class="ui sortable middle aligned table">
                <thead>
                <tr>
                    <th>
                        <div class="ui fitted checkbox" ref="master_checkbox">
                            <input type="checkbox" name="all_regions">
                            <label></label>
                        </div>
                    </th>
                    <th v-for="(text, key) in columns" @click="sortBy(key)"
                        :class="{ sorted: sortKey === key, ascending: sortOrders[key] > 0, descending: sortOrders[key] <= 0}">{{ text }}</th>
                    <th width="150"></th>
                </tr>
                </thead>
                <tbody v-if="isLoading">
                <tr>
                    <td colspan="6"><div class="ui active centered inline loader"></div></td>
                </tr>
                </tbody>
                <tbody v-else>
                <tr v-for="item in items" ref="list">
                    <td>
                        <div class="ui fitted child checkbox">
                            <input type="checkbox" :name="'regions[' + item.id + ']'" v-model="selected[item.id]">
                        </div>
                    </td>
                    <td>{{ item.id }}</td>
                    <td>{{ item.name }}</td>
                    <td>{{ item.description }}</td>
                    <td>{{ item.created_at }}</td>
                    <td>
                        <dropdown-menu text="操作" :ref="'region-' + item.id">
                            <router-link class="item" :to="route('regions.edit', [item.id])"><i class="edit icon"></i> 编辑</router-link>
                            <a class="item" v-on:click.prevent="destroy(item.id)"><i class="delete icon"></i> 删除</a>
                        </dropdown-menu>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    import DropdownMenu from "../../SemanticUI/DropdownMenu";
    export default {
        name: "Index",
        components: {DropdownMenu},
        data: function () {
            var columns = {
                id: "ID",
                name: "名称",
                description: "描述",
                created_at: "创建于",
            };

            var sortOrders = {};
            Object.keys(columns).forEach(function (key) {
                sortOrders[key] = 1
            });

            return {
                items: [],
                selected: [],
                positive: {},
                negative: {},
                isLoading: false,
                sortKey: "",
                filterKey: "",
                sortOrders: sortOrders,
                columns: columns,
            }
        },
        created: function () {
            this.loadZones();
        },
        mounted: function() {
            $(this.$refs.master_checkbox).checkbox({
                // check all children
                onChecked: () => {
                    var
                        $childCheckbox  = $(this.$refs.list).find('.child.checkbox')
                    ;
                    $childCheckbox.checkbox('check');
                },
                // uncheck all children
                onUnchecked: () => {
                    var
                        $childCheckbox  = $(this.$refs.list).find('.child.checkbox')
                    ;
                    $childCheckbox.checkbox('uncheck');
                }
            });
        },
        updated: function() {
            $(".ui.child.checkbox").checkbox();
        },
        methods: {
            loadZones: function () {
                this.isLoading = true;
                axios.get(route('regions.index'))
                    .then((response) => {
                        var data = response.data;
                        if (data.result) {
                            var regions = data.regions;

                            var selected = {};

                            for (let i = 0; i < regions.length; i++) {
                                selected[regions[i].id] = false;
                            }

                            this.selected = selected;
                            this.items = regions;
                        }
                    })
                    .catch(() => {})
                    .then(() => {
                        this.isLoading = false;
                    })
                ;
            },
            destroy: function (id) {
                var targetDropdownMenu = this.$refs["region-" + id][0];
                if (confirm("确定删除此项？")) {
                    targetDropdownMenu.setLoading(true, "删除中");
                    axios
                        .delete(route('regions.destroy', [id]))
                        .then((response) => {
                            targetDropdownMenu.setDisabled();
                        }).catch(() => {}).then(() => {
                        targetDropdownMenu.clearLoading();
                    })
                    ;
                }
            },
            sortBy: function (key) {
                this.sortKey = key;
                this.sortOrders[key] = this.sortOrders[key] * -1
            },
        },
        computed: {
            selectedItem: function () {
                var selectedItemList = [];
                for (var i in this.selected) {
                    if (this.selected[i]) {
                        selectedItemList.push(i);
                    }
                }

                return selectedItemList;
            },
            selectedItemCount: function () {
                return this.selectedItem.length;
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