<template>
    <td>
        {{ this.regionName }} - {{ this.zoneName }}
    </td>
</template>

<script>
    export default {
        name: "RegionColumn",
        props: ["parentApp", "keyName", "entry"],
        computed: {
            regionId: function () {
                return this.zone.region_id;
            },
            region: function () {
                if (this.parentApp.availableRegions.hasOwnProperty(this.regionId))
                    return this.parentApp.availableRegions[this.regionId];
                return null;

            },
            regionName: function () {
                if (this.region)
                    return this.region.name;
                return "未知地域";
            },
            zoneId: function () {
                return this.entry.zone_id;
            },
            zone: function () {
                if (this.parentApp.availableZones.hasOwnProperty(this.zoneId))
                    return this.parentApp.availableZones[this.zoneId];
                return null;
            },
            zoneName: function () {
                if (this.zone)
                    return this.zone.name;
                return "未知可用区";
            }
        }
    }
</script>

<style scoped>

</style>