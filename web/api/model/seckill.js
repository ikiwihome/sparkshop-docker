

import request from "@/utils/request.js";

export default {
	// 秒杀商品列表
	list: {
		get: async function(data = {}) {
			return await request.get('addons/seckill/api.index/index', data);
		}
	},
	// 秒杀商品详情
	detail: {
		get: async function(data = {}) {
			return await request.get('addons/seckill/api.index/detail', data);
		}
	},
	// 购买商品信息
	goodsInfo: {
		post: async function(data = {}) {
			return await request.post('addons/seckill/api.index/goodsInfo', data);
		}
	},
	// 秒杀试算
	trail: {
		post: async function(data = {}) {
			return await request.post('addons/seckill/api.index/trail', data);
		}
	},
	// 创建订单
	createOrder: {
		post: async function(data = {}) {
			return await request.post('addons/seckill/api.index/createOrder', data);
		}
	},
}