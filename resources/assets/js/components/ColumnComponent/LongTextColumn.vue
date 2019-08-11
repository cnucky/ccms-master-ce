<template>
    <td class="long-text" v-if="isLongText" :data-content="entry[keyName]" v-on:click="showLongText">
        {{ text2Show }}
    </td>
    <td v-else>
        {{ text2Show }}
    </td>
</template>

<script>
    export default {
        name: "LongTextColumn",
        props: ["keyName", "entry", "maxLength"],
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
            showLongText: function () {
                this.confirmModal()
                    .withModalClass("small")
                    .withHeader("内容")
                    .withTextContent(this.entry[this.keyName])
                    .show()
                ;
            }
        },
        computed: {
            maxLengthValue: function () {
                if (this.maxLength)
                    return this.maxLength;
                return 128;
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
    td.long-text {
        cursor: pointer;
    }
</style>