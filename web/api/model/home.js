

import request from "@/utils/request.js";

export default {
	// 首页数据
	home: {
		get: async function(data = {}) {
			return await request.get('index/index', data);
		}
	},
	// 信息搜索
	search: {
		post: async function(data = {}) {
			return await request.post('index/search', data);
		}
	},
	// 获取diy商品数据
	getGoodList: {
		get: async function(data = {}) {
			return await request.get('addons/diy/api.index/getGoodsList', data);
		}
	},
	// 获取diy商品数据
	getGoodsCate: {
		get: async function(data = {}) {
			return await request.get('addons/diy/api.index/getGoodsCate', data);
		}
	},
	// 获取diy文章数据
	getArticleList: {
		get: async function(data = {}) {
			return await request.get('addons/diy/api.index/getArticle', data);
		}
	},
	// 获取幻灯数据
	getSliderList: {
		get: async function(data = {}) {
			return await request.get('addons/diy/api.index/getSlider', data);
		}
	},
	// 获取秒杀列表
	getSeckillList: {
		get: async function(data = {}) {
			return await request.get('addons/diy/api.index/getSeckillList', data);
		}
	}
}