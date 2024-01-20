

import request from "@/utils/request.js";

export default {
	// 获取可领取的优惠券列表
	couponList: {
		get: async function(data = {}) {
			return await request.get('addons/coupon/api.index/getCouponList', data);
		}
	},
	// 获取我的优惠券
	validCoupon: {
		post: async function(data = {}) {
			return await request.post('addons/coupon/api.userCoupon/getMyValid', data);
		}
	},
	// 领取优惠券
	receive: {
		post: async function(data = {}) {
			return await request.post('addons/coupon/api.userCoupon/receive', data);
		}
	},
	// 我的优惠券
	myCouponList: {
		get: async function(data = {}) {
			return await request.get('addons/coupon/api.userCoupon/myCoupon', data);
		}
	},
}