<template>
    <div class="ui mini search selection dropdown" v-bind:class="{ loading: isLoading, disabled: isLoading }" ref="regionSelect">
        <input type="hidden" v-bind:value="value" v-on:change="regionSelected">
        <i class="dropdown icon"></i>
        <div class="default text">地域</div>
        <div class="menu">
            <div class="item" v-for="option in availableRegions" :data-value="option.id">#{{ option.id }} {{ option.name }}</div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "RegionSelect",
        props: ["availableRegions", "isLoading", "value"],
        created: function () {
            var query = this.$router.currentRoute.query;

            var selectedRegion = "";

            if (query.hasOwnProperty("region")) {
                selectedRegion = query.region;
            }

            this.$emit("input", selectedRegion);
        },
        mounted: function () {
        },
        updated: function () {
            $(this.$refs.regionSelect).dropdown({
                fullTextSearch: true,
                forceSelection: false,
                placeholder: "auto",
                clearable: true,
            });
            this.setSelected();
        },
        methods: {
            regionSelected: function (event) {
                this.$emit("input", event.target.value);
                this.$emit("region-selected");
            },
            setSelected: function () {
                $(this.$refs.regionSelect).dropdown("set selected", this.value);
            }
        },
    }
</script>

<style scoped>
    .mini.dropdown .item {
        font-size: .78571429em;
    }
</style>