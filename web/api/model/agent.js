// +----------------------------------------------------------------------
// | SparkShop 坚持做优秀的商城系统
// +----------------------------------------------------------------------
// | Copyright (c) 2022~2099 http://sparkshop.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( https://opensource.org/licenses/mit-license.php )
// +----------------------------------------------------------------------
// | Author: NickBai
// +----------------------------------------------------------------------

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