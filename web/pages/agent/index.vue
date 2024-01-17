<template>
	<view class="my-promotion">
		<view class="header">
			<view class='name acea-row row-center-wrapper'>
				<!-- 当前佣金 -->
				<view>
					<view class="user-msg">
						<image class="avatar" :src="userInfo.avatar" mode=""></image>
						<view class="nickname line1">{{ userInfo.nickname }}</view>
						<view class="level line1">
							<text>{{ userInfo.level }}</text>
						</view>
					</view>
				</view>
			</view>
			<view class='num'>{{ userInfo.residue_amount }}</view>
			<view class='profit' style="width: 91%;">
				<view class='item' @click="withdrawal()">
					<view>累积已提<text class='iconfont icon-xiangyou'></text></view>
					<view class='money'>{{ userInfo.draw_amount }}</view>
				</view>
			</view>
		</view>
		<view @click="navTo('/pages/agent/apply')" hover-class="none" class='bnt bg-color'>立即提现</view>
		<view class="list" style="display: flex;">
			<view class="item acea-row" @click="navTo('/pages/agent/users')" >
				<uni-icons type="staff-filled" size="36" color="$icon-color" class="icon-class"></uni-icons>
				<text style="margin-top: 10px;">我推广的</text>
			</view>
			<view class="item acea-row" style="margin-left: 10px;" @click="navTo('/pages/agent/withdrawal')" >
				<uni-icons type="wallet-filled" size="36" color="$icon-color" class="icon-class"></uni-icons>
				<text style="margin-top: 10px;">提现记录</text>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				userInfo: {
					avatar: '',
					nickname: '小白',
					residue_amount: 0,
					draw_amount: 0,
					level: ''
				}
			}
		},
		mounted() {
			this.getBaseInfo()
		},
		methods: {
			navTo(url) {
				uni.navigateTo({  
					url
				})  
			},
			// 提现
			withdrawal() {
				
			},
			// 获取基础信息
			async getBaseInfo() {
				let res = await this.$api.agent.getAgentInfo.get()
				this.userInfo.avatar = res.data.user.avatar
				this.userInfo.nickname = res.data.user.nickname
				this.userInfo.residue_amount = res.data.residue_amount
				this.userInfo.level = res.data.level.name
				this.userInfo.draw_amount = res.data.draw_amount
			},
		}
	}
</script>

<style scoped lang="scss">
	.my-promotion .header {
		background-repeat: no-repeat;
		background-size: 100% 100%;
		width: 100%;
		height: 460rpx;
		background-image: url(@/static/agent.png);
		padding-bottom: 20rpx;
		background-color: #e93323;
	}
	.bg-color {
		background-color: #e93323 !important;
	}
	.icon-class {color: $icon-color}
	.my-promotion .header .name {
		font-size: 30rpx;
		color: #fff;
		padding-top: 37rpx;
		position: relative;

		.distribution {
			height: 52rpx;
			background-color: #ccc;
			font-size: 24rpx;
			color: #fff;
			position: absolute;
			right: 0;
			border-radius: 30rpx 0 0 30rpx;
			padding: 0 5rpx 0 10rpx;

			&.on {
				background-color: #FFF9E3;
				color: #D16739;
			}

			.iconfont {
				font-size: 18rpx;
			}

			.icon-dengjitubiao {
				font-size: 32rpx;
				margin-right: 10rpx;
			}
		}

		.user-msg {
			display: flex;
			align-items: center;
			justify-content: center;
			flex-direction: column;

			.nickname {
				font-size: 32rpx;
				margin: 10rpx 0;
				max-width: 8rem;
			}

			.level {
				font-size: 18rpx;
				padding: 4rpx 10rpx;
				background: linear-gradient(135deg, #FE960F 0%, #e93323 100%);
				border-radius: 6rpx;
				transform: scale(0.9);
				display: flex;
				align-items: center;
				max-width: 10rem;
				.icon-xiangyou {
					transform: scale(0.7);
					font-size: 28rpx;
				}
			}

			image {
				width: 100rpx;
				height: 100rpx;
				border-radius: 50%;
			}
		}
	}

	.my-promotion .header .name .record {
		font-size: 26rpx;
		color: rgba(255, 255, 255, 0.8);
		position: absolute;
		right: 20rpx;
	}

	.my-promotion .header .name .record .iconfont {
		font-size: 25rpx;
		margin-left: 10rpx;
		vertical-align: 2rpx;
	}

	.my-promotion .header .num {
		text-align: center;
		color: #fff;
		margin-top: 8rpx;
		font-size: 90rpx;
		font-family: 'Guildford Pro';
	}

	.my-promotion .header .profit {
		padding: 0 20rpx;
		margin-top: 20rpx;
		font-size: 24rpx;
		color: rgba(255, 255, 255, 0.8);
	}

	.my-promotion .header .profit .item {
		min-width: 200rpx;
		text-align: right;
	}

	.my-promotion .header .profit .item .iconfont {
		font-size: 18rpx;
		color: #fff;
		margin-top: 5rpx;
	}

	.my-promotion .header .profit .item .money {
		font-size: 34rpx;
		color: #fff;
		margin-top: 5rpx;
	}

	.my-promotion .bnt {
		font-size: 28rpx;
		color: #fff;
		width: 258rpx;
		border-radius: 50rpx;
		text-align: center;
		line-height: 68rpx;
		margin: -32rpx auto 0 auto;

		border: 16rpx solid #f5f5f5;
	}
	.my-promotion .list {
		padding: 0 20rpx 50rpx 20rpx;
		margin-top: 10rpx;
	}

	.my-promotion .list .item {
		width: 345rpx;
		height: 220rpx;
		border-radius: 20rpx;
		background-color: #fff;
		margin-top: 20rpx;
		font-size: 30rpx;
		color: #666;
	}
	.acea-row {
		align-items: center;
		justify-content: center;
		flex-direction: column;
		display: flex;
		flex-wrap: wrap;
		cursor: pointer;
	}
</style>