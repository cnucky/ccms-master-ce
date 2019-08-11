<template>
    <td class="long-text" v-if="isLongText" :data-content="entry[keyName]">
        <router-link :to="{name: 'admin.tickets.show', params: {id: entry.id}}">{{ text2Show }}</router-link>
    </td>
    <td v-else>
        <router-link :to="{name: 'admin.tickets.show', params: {id: entry.id}}">{{ text2Show }}</router-link>
    </td>

</template>

<script>
    export default {
        name: "LinkColumn",
        props: ["keyName", "entry"],
        mounted: function () {
            if (this.isLongText) {
                $(this.$el)
                    .popup({
                        distanceAway: 25,
                        hoverable: true,
                    })
                ;
            }
        },
        updated: function () {
            if (this.isLongText) {
                $(this.$el)
                    .popup({
                        distanceAway: 25,
                        hoverable: true,
                    })
                ;
            }
        },
        methods: {
        },
        computed: {
            maxLengthValue: function () {
                if (this.maxLength)
                    return this.maxLength;
                return 20;
            },
            isLongText: function () {
                if (typeof this.entry[this.keyName] === "string")
                    return this.entry[this.keyName].length > this.maxLengthValue;
                return false;
            },
            text2Show: function () {
                if (this.isLongText)
                    return this.entry[this.keyName].substr(0, this.maxLengthValue) + "...";
                return this.entry[this.keyName];
            }
        }
    }
</script>

<style scoped>

</style>