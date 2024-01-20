

import request from "@/utils/request.js";

export default {
	// 获取用户订单列表
	orderList: {
		get: async function(data = {}) {
			return await request.get('orderRefund/index', data);
		}
	},
	// 订单详情
	orderDetail: {
		get: async function(data = {}) {
			return await request.get('orderRefund/getDetail', data);
		}
	},
	// 退款试算
	refundTrail: {
		post: async function(data = {}) {
			return await request.post('orderRefund/refundTrail', data);
		}
	},
	// 提交退款
	refund: {
		post: async function(data = {}) {
			return await request.post('orderRefund/refund', data);
		}
	},
	// 取消退款
	cancelRefund: {
		get: async function(data = {}) {
			return await request.get('orderRefund/cancelRefund', data);
		}
	},
	// 退款物流
	refundExpress: {
		post: async function(data = {}) {
			return await request.post('orderRefund/refundExpress', data);
		}
	},
}