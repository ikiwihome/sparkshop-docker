<template>
	<view>
		<view class="collect-list">
			<view class="collect-item" v-for="item in withdrawalLog" :key="item.id">
				<view class="goods-content" style="width: 60%;">
					<view class="goods-title">
						提现方式：{{ item.type }}
					</view>
					<view class="goods-price">提现时间：{{ item.create_time }}</view>
				</view>
				<view class="goods-content" style="width: 40%;">
					<view class="goods-title">
						提现金额: <span style="color:#e93323;margin-left: 3px;">{{ item.apply_amount }}</span>
					</view>
					<view class="goods-price" style="font-size: 13px;">
						状态:
						<span style="color:#909399;margin-left: 3px;" v-if="item.status == 1">待审批</span>
						<span style="color:#67C23A;margin-left: 3px;" v-if="item.status == 2">通过</span>
						<span style="color:#e93323;margin-left: 3px;" v-if="item.status == 3">拒绝</span>
					</view>
				</view>
			</view>
		</view>
		<xw-empty :isShow="withdrawalLog.length == 0" text="暂无推广人员" textColor="#777777" style="margin: 0 auto;"></xw-empty>
		<view class="loadmore" @click="more()" style="background: #fff;"  v-if="loadingType == 'more'"><uni-text style="font-size: 14px;color: rgb(144, 147, 153);">点击加载更多</uni-text></view>
		<view class="loadmore" v-if="loadingType == 'noMore'"><uni-text style="font-size: 14px;color: rgb(144, 147, 153);">我是有底线的</uni-text></view>
	</view>
</template>

<script>
	import {
		goLogin,
		checkLogin
	} from '@/libs/login';
	export default {
		data() {
			return {
				loadingType: 'more', // 加载更多状态
				withdrawalLog: [], // 提现记录
				form: {
					page: 1,
					limit: 10
				},
				totalPage: 1
			}
		},
		onShow() {
			if (!checkLogin()) {
				goLogin()
			}
		},
		mounted() {
			this.getAgentWithdrawal()
		},
		methods: {
			// 获取提现记录
			async getAgentWithdrawal(type) {
				uni.showLoading({
					title: '请求中..',
				})
				let res = await this.$api.agent.withdrawal.get(this.form)
				
				uni.hideLoading()
				if (type == 'more') {
					this.withdrawalLog = res.data.data.concat(this.withdrawalLog)
				} else {
					this.totalPage = res.data.last_page
					this.withdrawalLog = res.data.data
				}
				
				if (this.form.page == this.totalPage || this.totalPage == 0) {
					this.loadingType = 'noMore'
				}
			},
			// 加载更多
			more() {
				this.form.page += 1
				this.getAgentWithdrawal('more')
			}
		}
	}
</script>

<style lang="scss" scoped>
	.collect-list {
		height: 100%;
		width: 100%;
		background: #fff;
		border-top: 1px solid #eee;
	}
	.collect-item {
		margin-left: 15px;
		border-bottom: 1px solid #eee;
		height: 60px;
		display: flex;
		align-items: center;
		flex-wrap: nowrap;		
	}
	.goods-content {
		width: 50%;
		height: 100%;
		padding: 10px 0px 5px 10px;
		.goods-title {
			text-overflow: -o-ellipsis-lastline;
			overflow: hidden;
			text-overflow: ellipsis;
			display: -webkit-box;
			-webkit-line-clamp: 2;
			line-clamp: 2;					
			-webkit-box-orient: vertical;
			font-size: 14px;
		}
		.goods-price {
			margin-top: 5px;
			color: #666;
			font-size: 12px;
		}
	}
	.loadmore {
		display: flex;
		flex-direction: row;
		height: 41px;
		align-items: center;
		justify-content: center;
		margin-top: 10px;
	}
</style>