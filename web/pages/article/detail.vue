<template>
	<div class="article-box">
		<div class="title">{{ articleInfo.title }}</div>
		<div class="description">
			<div class="label">官方</div>
			<div class="item">{{ articleInfo.create_time }}</div>
			<div class="item">{{ articleInfo.views }}</div>
		</div>
		<div class="content">
			<rich-text :nodes="articleInfo.content"></rich-text>
		</div>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				id: 0,
				articleInfo: {}
			}
		},
		onLoad(options) {
			this.id = options.id
			this.getArticleInfo()
		},
		mounted() {
			
		},
		methods: {
			// 获取文章列表
			async getArticleInfo() {
				let res = await this.$api.common.getArticleInfo.get({id: this.id})
				this.articleInfo = res.data
				
				uni.setNavigationBarTitle({
					title: res.data ? res.data.title : '文章详情'
				});
			}
		}
	}
</script>

<style>
	uni-page-body,page {
		background-color: #fff;
		min-height: 100%;
	}
	.article-box .title {
		padding: 0 15px;
		font-size: 17px;
		color: #282828;
		font-weight: 700;
		margin: 22px 0 11px 0;
		line-height: 1.5;
	}
	.description {
		margin: 0 15px;
		padding-bottom: 12px;
		align-items: center;
		display: flex;
	}
	.description .label {
		font-size: 12px;
		color: #b1b2b3;
	}
	.description .item {
		margin-left: 13px;
		font-size: 12px;
		color: #b1b2b3;
	}
	.content {
		padding: 0 15px;
		font-size: 14px;
		color: #8a8b8c;
		line-height: 1.7;
	}
</style>