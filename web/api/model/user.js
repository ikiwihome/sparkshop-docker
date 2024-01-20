

import request from "@/utils/request.js";

export default {
	// 获取个人中心基础信息
	getUserBaseInfo: {
		get: async function(data = {}) {
			return await request.get('user/index', data);
		}
	},
	// 获取个人基础信息
	getUserInfo: {
		get: async function(data = {}) {
			return await request.get('user/info', data);
		}
	},
	// 更新基础信息
	update: {
		post: async function(data = {}) {
			return await request.post('user/update', data);
		}
	},
	// 更换手机号
	changePhone: {
		post: async function(data = {}) {
			return await request.post('user/changePhone', data);
		}
	},
	// 更改密码
	changePassword: {
		post: async function(data = {}) {
			return await request.post('user/changePassword', data);
		}
	},
}