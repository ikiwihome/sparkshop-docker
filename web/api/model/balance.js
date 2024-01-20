

import request from "@/utils/request.js";

export default {
	// 创建余额充值订单
	createRechargeOrder: {
		post: async function(data = {}) {
			return await request.post('balance/recharge', data);
		}
	},
}