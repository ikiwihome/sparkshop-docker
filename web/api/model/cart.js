

import request from "@/utils/request.js";

export default {
	// 购物车数量
	num: {
		get: async function(data = {}) {
			return await request.get('index/cartNum', data);
		}
	},
	// 添加购物车
	add: {
		post: async function(data = {}) {
			return await request.post('cart/add', data);
		}
	},
	// 获取我的购物车
	myCartList: {
		get: async function(data = {}) {
			return await request.get('cart/list', data);
		}
	},
	// 删除购物车
	remove: {
		get: async function(data = {}) {
			return await request.get('cart/remove', data);
		}
	},
	// 清空购物车
	clearCart: {
		get: async function(data = {}) {
			return await request.get('cart/clearCart', data);
		}
	},
	// 改变购物车商品数量
	changeNum: {
		post: async function(data = {}) {
			return await request.post('cart/changeNum', data);
		}
	}
}