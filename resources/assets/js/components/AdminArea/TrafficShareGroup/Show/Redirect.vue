<template>
    
</template>

<script>
    export default {
        name: "Redirect",
        props: ["trafficShareGroup"],
        created: function () {
            this.$router.push({name: "trafficShareGroups.zones.index", params: {id: this.trafficShareGroup.id}});
        }
    }
</script>

<style scoped>

</style>