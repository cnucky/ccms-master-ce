<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-19
 * Time: 下午4:45
 */

return [
    \App\Constants\AdminPermissions::SUPER => "系统管理员（最高权限）",
    // 证书增删改查
    \App\Constants\AdminPermissions::R_CERTIFICATE => "浏览证书",
    \App\Constants\AdminPermissions::CU_CERTIFICATE => "创建&编辑证书",
    \App\Constants\AdminPermissions::D_CERTIFICATE => "删除证书",

    // 地域增删改查
    \App\Constants\AdminPermissions::R_REGION => "浏览地域",
    \App\Constants\AdminPermissions::CU_REGION => "创建&编辑地域",
    \App\Constants\AdminPermissions::D_REGION => "删除地域",

    // 流量共享组增删改查
    \App\Constants\AdminPermissions::R_TRAFFIC_SHARE_GROUP => "浏览流量共享组",
    \App\Constants\AdminPermissions::CU_TRAFFIC_SHARE_GROUP => "创建&编辑流量共享组",
    \App\Constants\AdminPermissions::D_TRAFFIC_SHARE_GROUP => "删除流量共享组",

    // 可用区增删改查
    \App\Constants\AdminPermissions::R_ZONE => "浏览可用区",
    \App\Constants\AdminPermissions::CU_ZONE => "创建&编辑可用区",
    \App\Constants\AdminPermissions::D_ZONE => "删除可用区",

    // 计算节点增删改查
    \App\Constants\AdminPermissions::R_COMPUTE_NODE => "浏览计算节点",
    \App\Constants\AdminPermissions::CU_COMPUTE_NODE => "创建&编辑计算节点",
    \App\Constants\AdminPermissions::D_COMPUTE_NODE => "删除计算节点",

    // 公共镜像增删改查
    \App\Constants\AdminPermissions::R_PUBLIC_IMAGE => "浏览公共镜像",
    \App\Constants\AdminPermissions::CU_PUBLIC_IMAGE => "创建&编辑公共镜像",
    \App\Constants\AdminPermissions::D_PUBLIC_IMAGE => "删除公共镜像",

    // 公共ISO增删改查
    \App\Constants\AdminPermissions::CU_PUBLIC_ISO => "创建&编辑公共ISO",
    \App\Constants\AdminPermissions::D_PUBLIC_ISO => "删除公共ISO",

    // 公共Floppy增删改查
    \App\Constants\AdminPermissions::CU_PUBLIC_FLOPPY => "创建&编辑公共Floppy",
    \App\Constants\AdminPermissions::D_PUBLIC_FLOPPY => "删除公共Floppy",

    // 计算实例规格增删改查
    \App\Constants\AdminPermissions::R_COMPUTE_INSTANCE_PACKAGE => "浏览计算实例规格",
    \App\Constants\AdminPermissions::CU_COMPUTE_INSTANCE_PACKAGE => "编辑计算实例规格",
    \App\Constants\AdminPermissions::D_COMPUTE_INSTANCE_PACKAGE => "删除计算实例规格",

    // IP地址池增删改查
    \App\Constants\AdminPermissions::R_IP_POOL => "浏览IP地址池",
    \App\Constants\AdminPermissions::CU_IP_POOL => "创建&编辑IP地址池",
    \App\Constants\AdminPermissions::D_IP_POOL => "删除IP地址池",

    \App\Constants\AdminPermissions::R_IP_ASSIGNMENT => "浏览IP分配", // 浏览IP分配
    \App\Constants\AdminPermissions::CONVERT_IP_ASSIGNMENT => "转换已分配IP类型", // 转换IP类型
    \App\Constants\AdminPermissions::BIND_IP_ASSIGNMENT => "绑定IP",
    \App\Constants\AdminPermissions::UNBIND_IP_ASSIGNMENT => "解绑已分配IP", // 解绑IP
    \App\Constants\AdminPermissions::ALLOCATE_IP_ASSIGNMENT => "分配IP",
    \App\Constants\AdminPermissions::RELEASE_IP_ASSIGNMENT => "释放已分配IP", // 释放IP
    \App\Constants\AdminPermissions::UPDATE_IP_ASSIGNMENT => "更新已分配IP属性", // 更新IP属性

    \App\Constants\AdminPermissions::R_USER_QUOTA => "浏览用户配额",
    \App\Constants\AdminPermissions::CU_USER_QUOTA => "创建&编辑用户配额",
    \App\Constants\AdminPermissions::D_USER_QUOTA => "删除用户配额",

    \App\Constants\AdminPermissions::R_USER => "浏览用户", // 浏览用户
    \App\Constants\AdminPermissions::ADD_CREDIT_TO_USER => "添加用户余额", // 添加用户余额
    \App\Constants\AdminPermissions::REMOVE_CREDIT_FROM_USER => "扣除用户余额", // 扣除用户余额
    \App\Constants\AdminPermissions::LOGIN_AS_USER => "以用户身份登录", // 以用户身份登录
    \App\Constants\AdminPermissions::SUSPEND_USER => "锁定用户", // 锁定用户
    \App\Constants\AdminPermissions::UNSUSPEND_USER => "解锁用户", // 解锁用户
    \App\Constants\AdminPermissions::CU_USER => "创建&修改用户", // 创建/修改用户
    \App\Constants\AdminPermissions::D_USER => "删除用户", // 删除用户

    \App\Constants\AdminPermissions::R_COMPUTE_INSTANCE => "浏览计算实例", // 浏览计算实例
    \App\Constants\AdminPermissions::OPERATE_COMPUTE_INSTANCE => "操作计算实例", // 操作计算实例
    \App\Constants\AdminPermissions::SUSPEND_COMPUTE_INSTANCE => "锁定计算实例", // 锁定计算实例
    \App\Constants\AdminPermissions::UNSUSPEND_COMPUTE_INSTANCE => "解锁计算实例", // 解锁计算实例
    \App\Constants\AdminPermissions::U_COMPUTE_INSTANCE => "更新计算实例属性", // 更新计算实例属性
    \App\Constants\AdminPermissions::D_COMPUTE_INSTANCE => "删除计算实例", // 删除计算实例

    \App\Constants\AdminPermissions::R_LOCAL_VOLUME => "浏览本地卷", // 浏览本地卷
    \App\Constants\AdminPermissions::ATTACH_LOCAL_VOLUME => "连接本地卷", // 连接本地卷
    \App\Constants\AdminPermissions::DETACH_LOCAL_VOLUME => "分离本地卷", // 分离本地卷
    \App\Constants\AdminPermissions::RELEASE_LOCAL_VOLUME => "释放本地卷", // 释放本地卷
    \App\Constants\AdminPermissions::CHANGE_BUS_LOCAL_VOLUME => "更改本地卷总线",
    \App\Constants\AdminPermissions::TOGGLE_PROTECT_MODE_LOCAL_VOLUME => "开关本地卷保护模式",
    \App\Constants\AdminPermissions::TOGGLE_PRIMARY_BOOTABLE_DISK_LOCAL_VOLUME => "设定首选启动盘",
    \App\Constants\AdminPermissions::RESIZE_LOCAL_VOLUME => "调整本地卷容量",


    \App\Constants\AdminPermissions::R_CURRENCY => "浏览货币", // 浏览货币
    \App\Constants\AdminPermissions::CU_CURRENCY => "创建&编辑货币", // 创建/编辑货币
    \App\Constants\AdminPermissions::D_CURRENCY => "删除货币", // 删除货币

    \App\Constants\AdminPermissions::CRUD_PAYMENT_MODULE_CONFIGURATION => "管理付款模块配置", // 管理付款模块配置

    \App\Constants\AdminPermissions::CUD_TICKET_DEPARTMENT => "创建&编辑&删除工单部门", // 创建/编辑/删除工单部门

    \App\Constants\AdminPermissions::R_TICKET => "浏览工单", // 浏览工单
    \App\Constants\AdminPermissions::MAKE_REPLY_TO_TICKET => "回复工单", // 回复工单
    \App\Constants\AdminPermissions::U_TICKET => "修改工单", // 修改工单
    \App\Constants\AdminPermissions::D_TICKET => "删除工单", // 删除工单

    \App\Constants\AdminPermissions::R_PAYMENT_TRADE => "浏览充值订单", // 浏览充值订单
    \App\Constants\AdminPermissions::MARK_SUCCESS_PAYMENT_TRADE => "为充值订单入账", // 标记充值订单为已完成
    \App\Constants\AdminPermissions::MARK_CANCELLED_PAYMENT_TRADE => "取消充值订单并自动扣除用户余额",
    \App\Constants\AdminPermissions::REFUND_PAYMENT_TRADE => "退款", // 退款
    \App\Constants\AdminPermissions::U_PAYMENT_TRADE => "编辑充值订单", // 编辑充值订单

    \App\Constants\AdminPermissions::CANCEL_PAYMENT_TRADE_REFUND => "取消退款", // 取消退款
    \App\Constants\AdminPermissions::COMMIT_PAYMENT_TRADE_REFUND => "完成退款", // 完成退款
    \App\Constants\AdminPermissions::U_STATUS_PAYMENT_TRADE_REFUND => "更改退款订单状态", // 更改退款订单状态

    \App\Constants\AdminPermissions::U_SELF_ADMIN => "更新自己帐号信息", // 管理员更新自己帐号信息

    \App\Constants\AdminPermissions::R_BILLING_STATISTICS => "浏览财务统计",
];