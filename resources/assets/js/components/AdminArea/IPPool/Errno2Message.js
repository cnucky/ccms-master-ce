export default {
    methods: {
        errno2Message: function (data) {
            switch (data.errno) {
                case 20005:
                    this.negativeFloatingMessage("首IP在地址池 #" + data.pool.id + " " + data.pool.human_readable_first_usable_ip + "-" + data.pool.human_readable_last_usable_ip + "内");
                    break;
                case 20006:
                    this.negativeFloatingMessage("末IP在地址池 #" + data.pool.id + " " + data.pool.human_readable_first_usable_ip + "-" + data.pool.human_readable_last_usable_ip + "内");
                    break;
                case 20008:
                    this.negativeFloatingMessage("有" + data.count + "个已分配的子网位于当前设定的可用IP范围外");
                    break;
                default:
                    this.negativeFloatingMessage(data.message);
            }
        }
    }
}