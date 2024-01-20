

import request from "@/utils/request.js";

export default {
	// 获取余额记录
	getList: {
		get: async function(data = {}) {
			return await request.get('balanceLog/index', data);
		}
	},
	// 基础信息
	getBaseInfo: {
		get: async function(data = {}) {
			return await request.get('balanceLog/getTotalInfo', data);
		}
	}
}