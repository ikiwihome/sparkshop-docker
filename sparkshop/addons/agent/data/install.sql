DROP TABLE IF EXISTS `<#PREFIX#>agent_goods`;
CREATE TABLE `<#PREFIX#>agent_goods` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
    `goods_id` int(11) DEFAULT '0' COMMENT '商品的id',
    `percent` decimal(10,2) DEFAULT '0.00' COMMENT '提成比例',
    `create_time` datetime DEFAULT NULL COMMENT '创建时间',
    `update_time` datetime DEFAULT NULL COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY `idx_goods` (`goods_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='分销商品表';

DROP TABLE IF EXISTS `<#PREFIX#>agent_level`;
CREATE TABLE `<#PREFIX#>agent_level` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
    `level` int(3) DEFAULT '1' COMMENT '等级值',
    `name` varchar(155) DEFAULT '' COMMENT '等级名称',
    `percent` decimal(10,4) DEFAULT '0.0000' COMMENT '上浮分成比例',
    `level_up_way` tinyint(2) DEFAULT '1' COMMENT '升级方式 1:满足任意条件 2:满足全部条件',
    `config` text COMMENT '配置信息',
    `create_time` datetime DEFAULT NULL COMMENT '创建时间',
    `update_time` datetime DEFAULT NULL COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY `idx_level` (`level`,`percent`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='分销等级';

DROP TABLE IF EXISTS `<#PREFIX#>agent_user`;
CREATE TABLE `<#PREFIX#>agent_user` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
    `user_id` int(11) DEFAULT '0' COMMENT '用户id',
    `level_id` int(11) DEFAULT '0' COMMENT '推广等级',
    `parent_spread_id` int(11) DEFAULT '0' COMMENT '上级推广员id',
    `spread_num` int(11) DEFAULT '0' COMMENT '下级人员数',
    `bind_time` datetime DEFAULT NULL COMMENT '绑定时间',
    `agent_order_num` int(11) DEFAULT '0' COMMENT '提成单量',
    `agent_order_amount` decimal(12,2) DEFAULT '0.00' COMMENT '提成订单金额',
    `agent_pay_amount` decimal(12,2) DEFAULT '0.00' COMMENT '提成实际支付金额',
    `total_bonus` decimal(10,2) DEFAULT '0.00' COMMENT '总佣金数',
    `self_buy_num` int(11) DEFAULT '0' COMMENT '自购订单总数',
    `self_buy_amount` decimal(12,2) DEFAULT '0.00' COMMENT '自购订单金额',
    `self_buy_pay_amount` decimal(12,2) DEFAULT '0.00' COMMENT '自购实际支付总金额',
    `draw_amount` decimal(10,2) DEFAULT '0.00' COMMENT '已提款金额',
    `draw_num` int(11) DEFAULT '0' COMMENT '已提款次数',
    `residue_amount` decimal(10,2) DEFAULT '0.00' COMMENT '剩余可提款金额',
    `create_time` datetime DEFAULT NULL COMMENT '创建时间',
    `update_time` datetime DEFAULT NULL COMMENT '更新时间',
     PRIMARY KEY (`id`),
     KEY `idx_users` (`parent_spread_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='分销员表';

DROP TABLE IF EXISTS `<#PREFIX#>agent_order`;
CREATE TABLE `<#PREFIX#>agent_order` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
    `order_id` int(11) DEFAULT '0' COMMENT '订单id',
    `order_no` varchar(55) DEFAULT '' COMMENT '订单号',
    `buy_user_id` int(11) DEFAULT '0' COMMENT '购买人id',
    `agent_user_id` int(11) DEFAULT '0' COMMENT '返佣人id',
    `order_amount` decimal(10,2) DEFAULT '0.00' COMMENT '订单金额',
    `pay_amount` decimal(10,2) DEFAULT '0.00' COMMENT '支付金额',
    `agent_percent` varchar(155) DEFAULT '' COMMENT '返佣比例',
    `spread_amount` decimal(10,2) DEFAULT '0.00' COMMENT '返佣金额',
    `status` tinyint(2) DEFAULT '1' COMMENT '1:待结算  2:冻结中 3:已退款 4:已结算',
    `create_time` datetime DEFAULT NULL COMMENT '创建时间',
    `update_time` datetime DEFAULT NULL COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY `idx_order` (`agent_user_id`,`status`,`create_time`) USING BTREE,
    KEY `idx_order_id` (`order_id`,`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='分销订单';

DROP TABLE IF EXISTS `<#PREFIX#>agent_rebate_frozen`;
CREATE TABLE `<#PREFIX#>agent_rebate_frozen` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
    `order_id` int(11) DEFAULT '0' COMMENT '关联的订单id',
    `overdue_time` datetime DEFAULT NULL COMMENT '可解冻日期',
    `agent_order_id` int(11) DEFAULT NULL COMMENT '关联的推广订单id',
    `user_id` int(11) DEFAULT '0' COMMENT '结算人',
    `spread_amount` decimal(10,2) DEFAULT '0.00' COMMENT '结算金额',
    `create_time` datetime DEFAULT NULL COMMENT '创建时间',
    PRIMARY KEY (`id`),
    KEY `idx_search` (`order_id`,`overdue_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='佣金冻结表';

DROP TABLE IF EXISTS `<#PREFIX#>agent_withdrawal`;
CREATE TABLE `<#PREFIX#>agent_withdrawal` (
   `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
   `user_id` int(11) DEFAULT '0' COMMENT '申请人id',
   `type` varchar(15) DEFAULT '' COMMENT '提现方式',
   `card_no` varchar(55) DEFAULT '' COMMENT '卡号',
   `name` varchar(155) DEFAULT '' COMMENT '持卡人名',
   `bank` varchar(155) DEFAULT '' COMMENT '所属银行',
   `apply_amount` decimal(12,2) DEFAULT '0.00' COMMENT '提现金额',
   `account` varchar(255) DEFAULT '' COMMENT '微信或支付宝账号',
   `receive_qrcode` varchar(155) DEFAULT '' COMMENT '收款码',
   `status` tinyint(2) DEFAULT NULL COMMENT '状态 1:申请中 2:通过 3:拒绝',
   `desc` varchar(255) DEFAULT '' COMMENT '打款备注',
   `create_time` datetime DEFAULT NULL COMMENT '创建时间',
   `update_time` datetime DEFAULT NULL COMMENT '更新时间',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='提现申请';