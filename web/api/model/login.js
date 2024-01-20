

import request from "@/utils/request.js";

export default {
	// 通过密码登录
	loginBySms: {
		post: async function(data = {}) {
			return await request.post('login/loginBySms', data);
		}
	},
	// 通过账号登录
	loginByAccount: {
		post: async function(data = {}) {
			return await request.post('login/loginByAccount', data);
		}
	},
	// 注册账号
	regAccount: {
		post: async function(data = {}) {
			return await request.post('login/reg', data);
		}
	},
	// 授权登录
	miniappAuth: {
		post: async function(data = {}) {
			return await request.post('login/loginByWechat', data);
		}
	}
}