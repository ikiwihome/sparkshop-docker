

import request from "@/utils/request.js";

export default {
	// 获取二维码
	getQrCode: {
		post: async function(data = {}) {
			return await request.post('addons/agent/api.index/getQrCode', data);
		}
	},
	// 获取基础信息
	getAgentInfo: {
		get: async function(data = {}) {
			return await request.get('addons/agent/api.index/getAgentInfo', data);
		}
	},
	// 获取我推广的用户
	getAgentUsers: {
		get: async function(data = {}) {
			return await request.get('addons/agent/api.index/getAgentUsers', data);
		}
	},
	// 提现申请
	apply: {
		post: async function(data = {}) {
			return await request.post('addons/agent/api.index/apply', data);
		}
	},
	// 提现记录
	withdrawal: {
		get: async function(data = {}) {
			return await request.get('addons/agent/api.index/withdrawal', data);
		}
	},
}