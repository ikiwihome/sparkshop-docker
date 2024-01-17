<template>
	<view>
		<view class="collect-list">
			<view class="collect-item" v-for="item in agentUsers" :key="item.id">
				<view class="goods-img">
					<image :src="item.avatar" style="width:40px;height: 40px;"/>
				</view>
				<view class="goods-content">
					<view class="goods-title">
						{{ item.nickname }}
					</view>
					<view class="goods-price">加入时间：{{ item.agent_bind_time }}</view>
				</view>
				<view style="font-size: 12px;">
					推广 <br/>{{ item.agent_user_num }} 人
				</view>
			</view>
		</view>
		<xw-empty :isShow="agentUsers.length == 0" text="暂无推广人员" textColor="#777777" style="margin: 0 auto;"></xw-empty>
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
				agentUsers: [], // 推广的人员
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
			this.getAgentUser()
		},
		methods: {
			// 获取我的推广用户
			async getAgentUser(type) {
				uni.showLoading({
					title: '请求中..',
				})
				let res = await this.$api.agent.getAgentUsers.get(this.form)
				
				uni.hideLoading()
				if (type == 'more') {
					this.agentUsers = res.data.data.concat(this.agentUsers)
				} else {
					this.totalPage = res.data.last_page
					this.agentUsers = res.data.data
				}
				
				if (this.form.page == this.totalPage || this.totalPage == 0) {
					this.loadingType = 'noMore'
				}
			},
			// 加载更多
			more() {
				this.form.page += 1
				this.getAgentUser('more')
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
	.goods-img {
		height: 40px;
		width: 40px;
		text-align: center;
	}
	.goods-content {
		width: calc(100% - 127px);
		height: 100%;
		padding: 10px 0px 5px 10px;
		margin-left: 30px;
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