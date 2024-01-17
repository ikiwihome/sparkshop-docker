<template>
	<view>
		<!-- 订单状态 -->
		<view class="address-section" style="background: #fff;">
			<view class="status-title" v-if="orderInfo.status == 1">
				<view v-if="orderInfo.refund_type == 2 && orderInfo.step == 3">
					<text style="color:#e93323">等待退货</text>
					<view style="font-size: 13px;margin-top: 10px;">请将商品邮寄到以下的地址：</view>
					<view class="address-title" style="margin-top: 5px;">收件人：{{ orderInfo.refund_address.receive_user }}</view>
					<view class="address-title">收件人电话: {{ orderInfo.refund_address.receive_phone }}</view>
					<view class="address-title">收件人地址: {{ orderInfo.refund_address.receive_address }}</view>
				</view>
				<text style="color:#e93323" v-else>商家审核中,请耐心等待</text>
			</view>
			<view class="status-title" style="color:#e93323" v-if="orderInfo.status == 2">退款已完成</view>
			<view class="status-title" style="color:#e93323" v-if="orderInfo.status == 3">退款申请被拒绝</view>
			<view class="status-title" v-if="orderInfo.status == 3" style="margin-top: 10px;font-size: 13px">{{ orderInfo.unrefund_reason }}</view>
			<view class="status-title" style="color:#e93323" v-if="orderInfo.status == 4">退款申请已取消</view>
			<view class="status-title" style="margin-top: 10px;font-size: 13px">{{ orderInfo.create_time }}</view>
			<image class="a-bg" src="@/static/bg_line.png"></image>
		</view>

		<view class="goods-section">
			<!-- 商品列表 -->
			<view v-for="item,index in orderInfo.order_detail" :key="index">
				<view class="g-item">
					<image :src="item.logo"></image>
					<view class="right">
						<text class="title clamp" style="font-size: 26rpx;">{{ item.goods_name }}</text>
						<text class="spec">
							<text v-if="item.rule != 0">{{ item.rule.split('※').join(' ') }}</text>
						</text>
						<view class="price-box">
							<text class="price" style="color:#e93323">￥ {{ item.price }}</text>
							<text class="number">x {{ item.apply_refund_num }}</text>
						</view>
					</view>
				</view>
			</view>
		</view>

		<view class="yt-list">
			<view class="yt-list-cell">
				<text class="cell-tit clamp">下单编号:</text>
				<text class="cell-tip">{{ orderInfo.order_no }}</text>
			</view>
			<view class="yt-list-cell">
				<text class="cell-tit clamp">下单时间:</text>
				<text class="cell-tip">{{ orderInfo.orderInfo.create_time }}</text>
			</view>
			<view class="yt-list-cell">
				<text class="cell-tit clamp">支付方式:</text>
				<text class="cell-tip">{{ orderInfo.orderInfo.pay_way }}</text>
			</view>
			<view class="yt-list-cell">
				<text class="cell-tit clamp">支付状态:</text>
				<text class="cell-tip">{{ orderInfo.orderInfo.pay_status }}</text>
			</view>
			<view class="yt-list-cell">
				<text class="cell-tit clamp">买家留言:</text>
				<text class="cell-tip">{{ orderInfo.orderInfo.remark }}</text>
			</view>
			
		</view>
		
		<view class="yt-list">
			<view class="yt-list-cell">
				<text class="cell-tit clamp">收货人:</text>
				<text class="cell-tip">{{ address.user_name }}</text>
			</view>
			<view class="yt-list-cell">
				<text class="cell-tit clamp">联系电话:</text>
				<text class="cell-tip">{{ address.phone }}</text>
			</view>
			<view class="yt-list-cell">
				<text class="cell-tit clamp">收货地址:</text>
				<text class="cell-tip" style="width:200px;word-break: break-all;">{{ address.province }}{{ address.city }}{{ address.county }}{{ address.detail }}</text>
			</view>
		</view>
		
		<view class="yt-list">
			<view class="yt-list-cell">
				<text class="cell-tit clamp">商品总价:</text>
				<text class="cell-tip">￥{{ orderInfo.order_price }}</text>
			</view>
			<view class="yt-list-cell">
				<text class="cell-tit clamp">退款金额: </text>
				<text class="cell-tip"><text class="real_pay">￥{{ orderInfo.refund_price }}</text></text>
			</view>
		</view>
		
		<!-- 底部 -->
		<view class="footer">
			<view class="price-content" v-if="orderInfo.status == 1"></view>
			<view class="action-box b-t" v-if="orderInfo.status == 1">
				<button class="action-btn recom" @click="cancelRefund()">取消申请</button>
				<button class="action-btn recom" @click="complate()" v-if="orderInfo.refund_type == 2 && orderInfo.step == 3">填写退货</button>
			</view>
		</view>
		
	</view>
</template>

<script>
	export default {
		data() {
			return {
				refundId: 0,
				orderInfo: {
					orderInfo: {}
				},
				userVipOpen: 0,
				couponOpen: 0,
				address: {}
			}
		},
		onLoad(option) {
			this.refundId = option.refund_id
			this.getConfig()
			this.getOrderInfo()
		},
		created() {
			uni.$on('refreshData', () => {
				this.getOrderInfo()
			})
		},
		methods: {
			// 获取基础配置
			async getConfig() {
				let res = await this.$api.order.getConfig.get()
				this.userVipOpen = res.data.userVip
				this.couponOpen = res.data.coupon
			},
			// 订单信息
			async getOrderInfo() {
				let res = await this.$api.orderRefund.orderDetail.get({
					refund_id: this.refundId
				});
				
				if (res.code == 0) {
					this.orderInfo = res.data
					this.address = this.orderInfo.orderInfo.address
				} else {
					this.$tool.msg(res.msg);
				}
			},
			// 取消退款订单
			async cancelRefund() {
				uni.showLoading({
					title: '处理中'
				});
				let res = await this.$api.orderRefund.cancelRefund.get({
					id: this.refundId
				})
				uni.hideLoading()
				if (res.code == 0) {
					this.$tool.msg(res.msg);
					setTimeout(() => {
						uni.$emit('refreshData');
						uni.navigateBack(1)
					}, 800)
				} else {
					this.$tool.msg(res.msg);
					setTimeout(() => {
						this.getOrderInfo()
					}, 800)
				}
			},
			// 填写收货地址
			complate() {
				uni.redirectTo({
					url: '/pages/refund/refundExpress?refund_id=' + this.refundId
				})
			}
		}
	}
</script>

<style lang="scss">
	page {
		background: $page-color-base;
		padding-bottom: 100upx;
	}
	.status-title {
		margin-left: 20px;
		color: #303133;
	}
	.address-section {
		padding: 30upx 0;
		background: #fff;
		position: relative;
	
		.order-content {
			display: flex;
			align-items: center;
		}
	
		.icon-shouhuodizhi {
			flex-shrink: 0;
			display: flex;
			align-items: center;
			justify-content: center;
			width: 90upx;
			color: #888;
			font-size: 44upx;
		}
	
		.cen {
			display: flex;
			flex-direction: column;
			flex: 1;
			width: 90%;
			font-size: 28upx;
			color: $font-color-dark;
		}
	
		.name {
			font-size: 34upx;
			margin-right: 24upx;
			margin-left: 30px;
		}
	
		.address {
			margin-top: 16upx;
			margin-right: 20upx;
			margin-left: 30px;
			overflow: hidden;
			text-overflow: ellipsis;
			white-space: nowrap;
			display: block;
			color: $font-color-light;
		}
	
		.icon-you {
			font-size: 32upx;
			color: $font-color-light;
			margin-right: 30upx;
		}
	
		.a-bg {
			position: absolute;
			left: 0;
			bottom: 0;
			display: block;
			width: 100%;
			height: 5upx;
		}
	}
	
	.goods-section {
		margin-top: 16upx;
		background: #fff;
		padding-bottom: 1px;

		.g-header {
			display: flex;
			align-items: center;
			height: 84upx;
			padding: 0 30upx;
			position: relative;
		}

		.logo {
			display: block;
			width: 50upx;
			height: 50upx;
			border-radius: 100px;
		}

		.name {
			font-size: 30upx;
			color: $font-color-base;
			margin-left: 24upx;
		}

		.g-item {
			display: flex;
			margin: 20upx 30upx;

			image {
				flex-shrink: 0;
				display: block;
				width: 140upx;
				height: 140upx;
				border-radius: 4upx;
			}

			.right {
				flex: 1;
				padding-left: 24upx;
				overflow: hidden;
			}

			.title {
				font-size: 30upx;
				color: $font-color-dark;
			}

			.spec {
				color: $font-color-light;
				font-size:24rpx;
				height: 30rpx;
				display: block;
			}

			.price-box {
				display: flex;
				align-items: center;
				font-size: 32upx;
				color: $font-color-dark;
				padding-top: 10upx;

				.price {
					margin-bottom: 4upx;
				}
				.number{
					font-size: 26upx;
					color: $font-color-base;
					margin-left: 20upx;
				}
			}

			.step-box {
				position: relative;
			}
		}
	}
	.yt-list {
		margin-top: 16upx;
		background: #fff;
	}

	.yt-list-cell {
		display: flex;
		align-items: center;
		padding: 10upx 30upx 10upx 40upx;
		line-height: 50upx;
		position: relative;

		&.cell-hover {
			background: #fafafa;
		}

		&.b-b:after {
			left: 30upx;
		}

		.cell-icon {
			height: 32upx;
			width: 32upx;
			font-size: 22upx;
			color: #fff;
			text-align: center;
			line-height: 32upx;
			background: #f85e52;
			border-radius: 4upx;
			margin-right: 12upx;

			&.hb {
				background: #ffaa0e;
			}

			&.lpk {
				background: #3ab54a;
			}

		}

		.cell-more {
			align-self: center;
			font-size: 24upx;
			color: $font-color-light;
			margin-left: 8upx;
			margin-right: -10upx;
		}

		.cell-tit {
			flex: 1;
			font-size: 26upx;
			color: $font-color-dark;
			margin-right: 10upx;
		}

		.cell-tip {
			font-size: 26upx;
			color: $font-color-dark;

			&.disabled {
				color: $font-color-light;
			}

			&.active {
				color: $base-color;
			}
			&.red{
				color: $base-color;
			}
		}

		&.desc-cell {
			.cell-tit {
				max-width: 90upx;
			}
		}

		.desc {
			flex: 1;
			font-size: $font-base;
			color: $font-color-dark;
		}
	}
	
	/* 支付列表 */
	.pay-list{
		padding-left: 40upx;
		margin-top: 16upx;
		background: #fff;
		.pay-item{
			display: flex;
			align-items: center;
			padding-right: 20upx;
			line-height: 1;
			height: 110upx;	
			position: relative;
		}
		.icon-weixinzhifu{
			width: 80upx;
			font-size: 40upx;
			color: #6BCC03;
		}
		.icon-alipay{
			width: 80upx;
			font-size: 40upx;
			color: #06B4FD;
		}
		.icon-xuanzhong2{
			display: flex;
			align-items: center;
			justify-content: center;
			width: 60upx;
			height: 60upx;
			font-size: 40upx;
			color: $base-color;
		}
		.tit{
			font-size: 32upx;
			color: $font-color-dark;
			flex: 1;
		}
	}
	
	.footer{
		position: fixed;
		left: 0;
		bottom: 0;
		z-index: 995;
		display: flex;
		align-items: center;
		width: 100%;
		height: 90upx;
		justify-content: space-between;
		font-size: 30upx;
		background-color: #fff;
		z-index: 998;
		color: $font-color-base;
		box-shadow: 0 -1px 5px rgba(0,0,0,.1);
		.price-content{
			padding-left: 30upx;
		}
		.price-tip{
			color: $base-color;
			margin-left: 8upx;
		}
		.price{
			font-size: 36upx;
			color: $base-color;
		}
		.submit{
			display:flex;
			align-items:center;
			justify-content: center;
			width: 280upx;
			height: 100%;
			color: #fff;
			font-size: 32upx;
			background-color: $base-color;
		}
	}
	
	.action-box{
		display: flex;
		justify-content: flex-end;
		align-items: center;
		height: 100upx;
		position: relative;
		padding-right: 30upx;
	}
	.action-btn{
		width: 160upx;
		height: 60upx;
		margin: 0;
		margin-left: 24upx;
		padding: 0;
		text-align: center;
		line-height: 60upx;
		font-size: $font-sm + 2upx;
		color: $font-color-dark;
		background: #fff;
		border-radius: 100px;
		&:after{
			border-radius: 100px;
		}
		&.recom{
			background: #fff9f9;
			color: $base-color;
			&:after{
				border-color: #e93323;
			}
		}
	}
	.real_pay {
		color: #e93323 !important;
		font-weight: 700;
		margin-left: 10px;
		font-size: 15px !important;
	}
	.address-title {
		font-size: 13px;
	}
</style>
