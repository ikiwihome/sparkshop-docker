

import request from "@/utils/request.js";

export default {
	// 收藏列表
	myCollect: {
		get: async function(data = {}) {
			return await request.get('userCollect/myCollect', data);
		}
	},
	// 添加收藏
	add: {
		post: async function(data = {}) {
			return await request.post('userCollect/add', data);
		}
	},
	// 删除收藏
	remove: {
		get: async function(data = {}) {
			return await request.get('userCollect/remove', data);
		}
	},
	// 根据商品id移除
	removeByGoods: {
		get: async function(data = {}) {
			return await request.get('userCollect/removeByGoodsId', data);
		}
	}
}