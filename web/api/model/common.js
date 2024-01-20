

import request from "@/utils/request.js";

export default {
	// 发送短信
	sendSms: {
		post: async function(data = {}) {
			return await request.post('common/sendMsg', data);
		}
	},
	// 图片转换
	path2Base64: {
		post: async function(data = {}) {
			return await request.post('common/changeImg', data);
		}
	},
	// 文章列表
	getArticleInfo: {
		get: async function(data = {}) {
			return await request.get('article/detail', data);
		}
	}
}