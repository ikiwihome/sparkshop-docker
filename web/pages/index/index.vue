<template>
	<div class="page-index diy-body" :style="bgStyle">
		<!-- 小程序头部兼容 -->
		<!-- #ifdef MP -->
		<view class="mp-search-box">
			<input class="ser-input" type="text" placeholder="输入关键字搜索" disabled @click="search"/>
		</view>
		<!-- #endif -->
		<div style="width: 100%;height: 53px;"></div>
		<div class="diy-item" v-for="item,index in diyData" :key="index">
			<!-- 商品列表 -->
			<div class="component-item" v-if="item.type == 'goods'">
				<div class="goods-list"
					 :style="{'background': item.bgColor, 'padding-left': item.bgMargin + 'px', 'padding-right': item.bgMargin + 'px'}">
					<div :class="[item.lineNum == 1 ? 'goods-item1' : item.lineNum == 2 ? 'goods-item2' : 'goods-item3']"
						 v-for="item2 in goodsList" :key="item2.id"
						 :style="{'border-radius' : item.bgStyle == 'circle' ? '10px' : ''}" @click="navToDetailPage(item2)">
						<div class="goods-img">
							<img :src="item2.slider_image" mode="widthFix" />
						</div>
						<div class="goods-info">
							<div class="goods-title" v-if="item.show_title == 1">
								{{ item2.name }}
							</div>
							<div class="old-price" v-if="item.show_origin_price == 1">￥ {{item2.original_price }}
							</div>
							<div class="goods-price" v-if="item.show_price == 1"
								 :style="{'color': item.priceColor}">￥ {{ item2.price }}
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- 商品分类  -->
			<div class="component-item" v-if="item.type == 'cate'">
				<div class="goods-cate-box" :style="{
                    'background': 'linear-gradient(90deg, ' + item.bgColor1 + ' 0%, ' + item.bgColor2 + ' 100%)',
                    'margin-top': item.pageMargin + 'px'
                }">
					<div class="goods-cate">
						<div class="item" v-for="item2,index2 in goodsCate" :key="item2.id"
							 :class="{'on': index2 == 0}" :style="{'color': item.fontColor}">{{item2.name }}<span></span></div>
					</div>
				</div>
			</div>
			<!-- 公告 -->
			<div class="component-item" v-if="item.type == 'notice'">
				<div class="notice-diy" :style="{'background': item.bgColor, 'border-radius': item.bgStyle == 'circle' ? '6px' : '0px', 'margin-top': item.pageMargin + 'px', 'padding-left': item.bgMargin + 'px', 'padding-right': item.bgMargin + 'px'}">
					<div class="notice-img">
						<img :src="domain + item.icon" style="width: 90px;height: 30px;"/>
					</div>
					<div class="notice-content-box">
						<swiper class="swiper" circular autoplay="true" interval="10000" duration="1000" :vertical="item.slider == 1">
							<block v-for="(notice,nIndex) in item.content" :key='nIndex'>
								<swiper-item catchtouchmove='catchTouchMove'>
									<view class="notice-content" :style="{'color': item.fontColor, 'font-size': item.fontSize + 'px', 'text-align': item.textAlign}" @click="navTo(notice.link)">
										{{ notice.title }}
									</view>
								</swiper-item>
							</block>
						</swiper>
					</div>
				</div>
			</div>
			<!-- 标题 -->
			<div class="component-item" v-if="item.type == 'cTitle'">
				<div class="title-diy"
					 :style="{'background': item.bgColor, 'border-radius': item.bgStyle == 'circle' ? '6px' : '0px', 'margin-top': item.pageMargin + 'px', 'padding-left': item.bgMargin + 'px', 'padding-right': item.bgMargin + 'px'}">
					<div class="title-content"
						 :style="{'color': item.fontColor, 'font-size': item.fontSize + 'px', 'text-align': item.textAlign}">
						{{ item.content }}
					</div>
				</div>
			</div>
			<!-- 辅助空白 -->
			<div class="component-item" v-if="item.type == 'blank'">
				<div class="blank-diy"
					 :style="{'background': item.bgColor, 'height': item.height + 'px'}"></div>
			</div>
			<!-- 辅助线 -->
			<div class="component-item" v-if="item.type == 'cLine'">
				<div class="line-box">
					<div class="box" :style="{
                    'border-bottom': item.height + 'px ' + ((item.lineType == 1) ? 'dashed ' : (item.lineType == 2) ? 'solid ' : 'dotted ') + item.lineColor,
                    'margin-left': item.margin + 'px',
                    'margin-right': item.margin + 'px',
                    'margin-top': item.padding + 'px'
                }"></div>
				</div>
			</div>
			<!-- 新闻列表 -->
			<div class="component-item" v-if="item.type == 'news'">
				<div :style="{'padding-left': item.bgMargin + 'px', 'padding-right': item.bgMargin + 'px'}"
					 style="width: 100%">
					<div class="list-wrapper" :style="{'background': item.bgColor}">
						<div class="item" :class="{'on': item.textAlign == 'left'}"
							 v-for="item2 in articleData" :key="item2.id"
							 :style="{'border-radius': item.bgStyle == 'circle' ? '10px' : '0px'}" @click="goArticle(item2.id)">
							<div class="empty-img-box">
								<img :src="item2.img" style="width: 100%;height: 78px;" v-if="item2.img != ''"/>
							</div>
							<div class="info">
								<div class="title line2">{{ item2.title }}</div>
								<div class="time">{{ item2.create_time }}</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- 导航 -->
			<div class="component-item" v-if="item.type == 'cSlider'">
				<div class="home-slider-box">
					<swiper class="swiper" circular autoplay="true" interval="10000" duration="1000" :indicator-dots="true">
						<block v-for="(item2,index) in sliderList">
							<swiper-item catchtouchmove='catchTouchMove'>
								<div class="slider-box"
									 :style="{'padding-left': item.borderMargin + 'px', 'padding-right': item.borderMargin + 'px', 'margin-top': item.pageMargin + 'px'}" @click="sliderToDetailPage(item2)">
										<img :src="item2.pic_url" :style="{'border-radius': item.sliderStyle == 'circle' ? '6px' : '0px'}">
								</div>
							</swiper-item>
						</block>
					</swiper>
				</div>
			</div>
			<!-- 导航组 -->
			<div class="component-item" v-if="item.type == 'sliderGroup'">
				<div class="diy-menu-box"
					 :style="{'padding-left': item.borderMargin + 'px', 'padding-right': item.borderMargin + 'px', 'margin-top': item.pageMargin + 'px'}">
					<div class="menu-list" :style="{'background': item.bgColor}">
						<div class="menu-item"
							 :class="{'four' : item.num == 4, 'five': item.num == 5}"
							 v-for="(item2,index2) in item.content" :key="index2" @click="navTo(item2.link)">
							<div class="img-box">
								<i :class="item2.icon"
								   :style="{'font-size': item.iconSize + 'px', 'color': item.iconColor}"></i>
							</div>
							<p style="color: rgb(51, 51, 51);"
							   :style="{'font-size': item.fontSize + 'px'}">{{ item2.title }}</p>
						</div>
					</div>
				</div>
			</div>
			<!-- 图片魔方 -->
			<div class="component-item" v-if="item.type == 'picMagic'">
				<div class="magic-box">
					<div class="diy-style-1" v-if="item.style == 1"
						 :style="{'background': item.bgColor, 'border-radius': (item.bgStyle == 'circle') ? '12px' : '0px', 'margin-left': item.bgMargin + 'px', 'margin-right': item.bgMargin + 'px', 'margin-top': item.pageMargin + 'px'}">
						<div class="item-box"
							 :style="{'margin-left': item.bgMargin + 'px', 'margin-right': item.bgMargin + 'px'}" @click="navTo(item.images[0].href)">
							<div class="empty-img-box" v-if="!item.images[0]">
								<i class="el-icon-picture"></i>
							</div>
							<img :src="item.images[0].url" mode="widthFix" v-else />
						</div>
					</div>
					<div class="diy-style-2" v-if="item.style == 2"
						 :style="{'background': item.bgColor, 'border-radius': (item.bgStyle == 'circle') ? '12px' : '0px', 'margin-top': item.pageMargin + 'px'}">
						<div class="item-box"
							 :style="{'margin-left': item.bgMargin + 'px', 'margin-right': item.bgMargin + 'px'}">
							<div class="style-2-item" @click="navTo(item.images[0].href)">
								<div class="empty-img-box" v-if="!item.images[0]">
									<i class="el-icon-picture"></i>
								</div>
								<img :src="item.images[0].url" mode="widthFix" v-else/>
							</div>
							<div class="style-2-item" @click="navTo(item.images[1].href)">
								<div class="empty-img-box" v-if="!item.images[1]">
									<i class="el-icon-picture"></i>
								</div>
								<img :src="item.images[1].url" mode="widthFix" v-else />
							</div>
						</div>
					</div>
					<div class="diy-style-3" v-if="item.style == 3"
						 :style="{'background': item.bgColor, 'border-radius': (item.bgStyle == 'circle') ? '12px' : '0px', 'margin-top': item.pageMargin + 'px'}">
						<div class="item-box"
							 :style="{'margin-left': item.bgMargin + 'px', 'margin-right': item.bgMargin + 'px'}">
							<div class="style-3-item" @click="navTo(item.images[0].href)">
								<div class="empty-img-box" v-if="!item.images[0]">
									<i class="el-icon-picture"></i>
								</div>
								<img :src="item.images[0].url" mode="widthFix" v-else />
							</div>
							<div class="style-3-item" @click="navTo(item.images[1].href)">
								<div class="empty-img-box" v-if="!item.images[1]">
									<i class="el-icon-picture"></i>
								</div>
								<img :src="item.images[1].url" mode="widthFix" v-else />
							</div>
							<div class="style-3-item" @click="navTo(item.images[2].href)">
								<div class="empty-img-box" v-if="!item.images[2]">
									<i class="el-icon-picture"></i>
								</div>
								<img :src="item.images[2].url" mode="widthFix" v-else />
							</div>
						</div>
					</div>
					<div class="diy-style-4" v-if="item.style == 4"
						 :style="{'background': item.bgColor, 'border-radius': (item.bgStyle == 'circle') ? '12px' : '0px', 'margin-top': item.pageMargin + 'px'}">
						<div class="item-box"
							 :style="{'margin-left': item.bgMargin + 'px', 'margin-right': item.bgMargin + 'px'}">
							<div class="style-left" style="border-right:none" @click="navTo(item.images[0].href)">
								<div class="empty-img-box" v-if="!item.images[0]">
									<i class="el-icon-picture"></i>
								</div>
								<img :src="item.images[0].url" mode="widthFix" v-else />
							</div>
							<div class="style-right">
								<div class="style-top" style="border-bottom:none" @click="navTo(item.images[1].href)">
									<div class="empty-img-box" v-if="!item.images[1]">
										<i class="el-icon-picture"></i>
									</div>
									<img :src="item.images[1].url" mode="widthFix" v-else />
								</div>
								<div class="style-down" @click="navTo(item.images[2].href)">
									<div class="empty-img-box" v-if="!item.images[2]">
										<i class="el-icon-picture"></i>
									</div>
									<img :src="item.images[2].url" mode="widthFix" v-else />
								</div>
							</div>
						</div>
					</div>
					<div class="diy-style-5" v-if="item.style == 5"
						 :style="{'background': item.bgColor, 'border-radius': (item.bgStyle == 'circle') ? '12px' : '0px', 'margin-top': item.pageMargin + 'px'}">
						<div class="item-box"
							 :style="{'margin-left': item.bgMargin + 'px', 'margin-right': item.bgMargin + 'px'}">
							<div class="style-5-item" @click="navTo(item.images[0].href)">
								<div class="empty-img-box" v-if="!item.images[0]">
									<i class="el-icon-picture"></i>
								</div>
								<img :src="item.images[0].url" mode="widthFix" v-else />
							</div>
							<div class="style-5-item" @click="navTo(item.images[1].href)">
								<div class="empty-img-box" v-if="!item.images[1]">
									<i class="el-icon-picture"></i>
								</div>
								<img :src="item.images[1].url" mode="widthFix" v-else />
							</div>
							<div class="style-5-item" @click="navTo(item.images[2].href)">
								<div class="empty-img-box" v-if="!item.images[2]">
									<i class="el-icon-picture"></i>
								</div>
								<img :src="item.images[2].url" mode="widthFix" v-else />
							</div>
							<div class="style-5-item" @click="navTo(item.images[3].href)">
								<div class="empty-img-box" v-if="!item.images[3]">
									<i class="el-icon-picture"></i>
								</div>
								<img :src="item.images[3].url" mode="widthFix" v-else />
							</div>
						</div>
					</div>
					<div class="diy-style-6" v-if="item.style == 6"
						 :style="{'background': item.bgColor, 'border-radius': (item.bgStyle == 'circle') ? '12px' : '0px', 'margin-top': item.pageMargin + 'px'}">
						<div class="item-box"
							 :style="{'margin-left': item.bgMargin + 'px', 'margin-right': item.bgMargin + 'px'}">
							<div class="style-left" style="border-right:none">
								<div class="style-top" style="border-bottom:none" @click="navTo(item.images[0].href)">
									<div class="empty-img-box" v-if="!item.images[0]">
										<i class="el-icon-picture"></i>
									</div>
									<img :src="item.images[0].url" mode="widthFix" v-else />
								</div>
								<div class="style-down" @click="navTo(item.images[1].href)">
									<div class="empty-img-box" v-if="!item.images[1]">
										<i class="el-icon-picture"></i>
									</div>
									<img :src="item.images[1].url" mode="widthFix" v-else />
								</div>
							</div>
							<div class="style-right">
								<div class="style-top" style="border-bottom:none" @click="navTo(item.images[2].href)">
									<div class="empty-img-box" v-if="!item.images[2]">
										<i class="el-icon-picture"></i>
									</div>
									<img :src="item.images[2].url" mode="widthFix" v-else />
								</div>
								<div class="style-down" @click="navTo(item.images[3].href)">
									<div class="empty-img-box" v-if="!item.images[3]">
										<i class="el-icon-picture"></i>
									</div>
									<img :src="item.images[3].url" mode="widthFix" v-else />
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- 优惠券 -->
			<div class="component-item" v-if="item.type == 'coupon' && couponList.length > 0">
				<div class="coupon-box"
					 :style="{'background': item.bgColor, 'border-radius': item.bgStyle == 'circle' ? '10px' : '0px', 'margin': '0px ' + item.bgMargin + 'px', 'margin-top': item.pageMargin + 'px'}">
					<scroll-view scroll-x="true" scroll-left="120" class="coupon-scroll-box">
						<view style="display: flex;">
							<div class="coupon-item" v-for="item2 in couponList" :key="item2.id" :class="{'gray': item2.received == 1}">
								<div class="left">
									<div class="num">
										<div v-if="item2.type == 1"><span>￥</span> {{ item2.amount }}</div>
										<div v-else>{{ item2.discount * 100 }}折</div>
									</div>
									<div class="txt">{{ item2.name }}</div>
								</div>
								<div class="right" @click="receiveCoupon(item2)" v-if="item2.received == 0">立即领取</div>
								<div class="right" v-else>已领取</div>
								<div class="roll up-roll" :style="{'background': item.bgColor}"></div>
								<div class="roll down-roll" :style="{'background': item.bgColor}"></div>
							</div>
						</view>
					</scroll-view>
				</div>
			</div>
			<!-- 秒杀 -->
			<div class="component-item" v-if="item.type == 'seckill'">
				<div class="seckill-div"
					 :style="{'background': item.bgColor, 'margin-top': item.pageMargin + 'px'}">
					<div class="seckill-box"
						 :style="{'border-radius': item.bgStyle == 'circle' ? '10px' : '0px', 'margin': '0px ' + item.bgMargin + 'px'}">
						<div class="title">
							<div class="left">
								<img :src="domain + '/static/admin/default/image/spike-icon-002.gif'" alt="">
								<p>限时秒杀</p>
								<div class="time">
									<span style="background: rgba(252, 60, 62, 0.09); color: rgb(233, 51, 35);">{{ timer.hour }}</span><em>:</em>
									<span style="background: rgba(252, 60, 62, 0.09); color: rgb(233, 51, 35);">{{ timer.minute }}</span><em>:</em>
									<span style="background: rgba(252, 60, 62, 0.09); color: rgb(233, 51, 35);">{{ timer.seconds }}</span>
								</div>
							</div>
							<div class="right" @click="moreSeckill()" >更多</div>
						</div>
						<div class="seckill-list">
							<div class="seckill-item" v-for="item2 in seckillList" :key="item2.id" @click="navToSeckillDetailPage(item2)">
								<div class="img-box">
									<div class="empty-img-box">
										<img :src="item2.pic"/>
									</div>
								</div>
								<div class="seckill-title"
									 :style="{'font-size': item.fontSize + 'px', 'color': item.fontColor}"
									 v-if="item.show_title == 1">{{ item2.name }}
								</div>
								<div class="price" v-if="item.show_price == 1">
									<span class="label" style="background: rgb(233, 51, 35);">抢</span>
									<span class="num-label" style="color: rgb(233, 51, 35);">￥</span>
									<span class="num" style="color: rgb(233, 51, 35);">{{ item2.seckill_price }}</span>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import './home.css'
	import {
		BASE_URL
	} from '@/config/app';
	
	export default {
		data() {
			return {
				domain: BASE_URL,
				articleData: [],
				goodsList: [],
				goodsCate: [],
				couponList: [],
				sliderList: [],
				seckillList: [],
				bgStyle: "",
				diyBg: {},
				diyData: [],
				seckillHour: 0,
				timer: {
					hour: 0,
					minute: 0,
					seconds: 0
				},
				menuMap: ['/pages/index/index', '/pages/category/category', '/pages/cart/cart', '/pages/user/user']
			}
		},
		// 分享给好友
		onShareAppMessage(res) {
			return {
				title: 'SparkShop商城',
				path: '/pages/index/index'
			}
		},
		// 分享到朋友圈
		onShareTimeline() {
			return {
				title: 'SparkShop商城',
				path: '/pages/index/index'
			};
		},
		onShow() {
			this.timeShow()
		},
		mounted() {
			this.getHomeData()
		},
		methods: {
			// 获取首页数据
			async getHomeData() {
				let res = await this.$api.home.home.get()
				this.diyBg = res.data.diyBg
				this.diyData = res.data.diyData
				
				this.diyBg.bgImg = this.domain + this.diyBg.bgImg
				this.handleHomeBg(this.diyBg)
				
				 // 处理几个特殊的
				this.diyData.forEach(item => {
					if (item.type == 'goods') {
						this.getGoodList(item)
					} else if (item.type == 'cate') {
						this.getGoodsCate()
					} else if (item.type == 'news') {
						this.getArticleList(item)
					} else if (item.type == 'coupon') {
						this.getCouponList()
					} else if (item.type == 'cSlider') {
						this.getSliderList()
					} else if (item.type == 'seckill') {
						this.getSeckillList()
					}
				})
			},
			// 首页背景样式处理器
			handleHomeBg(data) {
				if (data.bgType == 2) {
					this.bgStyle = 'background-image: url(' + data.bgImg + ');'
					if (data.bgStyle == 1) {
						this.bgStyle += 'background-size: 100%;background-repeat: no-repeat;'
					} else if (data.bgStyle == 2) {
						this.bgStyle += 'background-size: 100%;background-repeat: repeat;'
					} else {
						this.bgStyle += 'background-size: 100% 100%;background-repeat: no-repeat;'
					}
				} else {
					this.bgStyle = 'background:' + data.bgColor;
				}
			},
			// 页面跳转
			navTo(url) {
				if (this.menuMap.indexOf(url) != -1) {
					uni.switchTab({
						url
					})
				} else {
					uni.navigateTo({
						url
					})
				}
			},
			// 导航跳转
			sliderToDetailPage(item) {
				if (item.type == 1) {
					uni.navigateTo({
						url: `${item.target_url}`
					})
				} else {
					window.location.href = item.target_url
				}
			},
			// 普通商品详情页
			navToDetailPage(item) {
				let id = item.id;
				uni.navigateTo({
					url: `/pages/product/product?id=${id}`
				})
			},
			// 秒杀商品详情
			navToSeckillDetailPage(item) {
				uni.navigateTo({
					url: '/pages/seckill/product?id=' + item.id
				})
			},
			// 获取商品数据
			async getGoodList(row) {
				let res = await this.$api.home.getGoodList.get({
					num: row.num,
					cate_id: row.cate_id,
					sortType: row.sortType
				});
				this.goodsList = res.data
			},
			// 获取商品分类
			async getGoodsCate() {
				let res = await this.$api.home.getGoodsCate.get()
				this.goodsCate = res.data
			},
			// 获取文章信息
			async getArticleList(row) {
				let res = await this.$api.home.getArticleList.get({limit: row.num, cate_id: row.cate_id})
				this.articleData = res.data
			},
			// 获取可领取的优惠券
			async getCouponList() {
				let res = await this.$api.coupon.couponList.get({goods_id: 0})
				this.couponList = res.data
			},
			// 领取优惠券
			async receiveCoupon(row) {
				let res = await this.$api.coupon.receive.post({id: row.id})
				if (res.code == 0) {
					this.$tool.msg(res.msg)
					this.getCouponList()
				} else {
					this.$tool.msg(res.msg, 3000, false, 'error')
				}
			},
			// 获取幻灯数据
			async getSliderList() {
				let res = await this.$api.home.getSliderList.get({position: 1})
				this.sliderList = res.data
			},
			// 获取秒杀列表
			async getSeckillList() {
				let res = await this.$api.home.getSeckillList.get()
				this.seckillList = res.data.seckillList
				this.seckillHour = res.data.seckillHour
			},
			// 更多的秒杀商品
			moreSeckill() {
				uni.navigateTo({
					url: `/pages/seckill/index`
				})
			},
			// 当前时间
			timeShow() {
				var t = null;
				let self = this
				t = setTimeout(time, 1000); //开始运行
				function time() {
				  clearTimeout(t);  // 清除定时器
				  let dt = new Date();
					
				  self.timer.hour = (dt.getHours() < 10) ? '0' + dt.getHours() : dt.getHours()
				  self.timer.minute = (dt.getMinutes() < 10) ? '0' + dt.getMinutes() : dt.getMinutes()
				  self.timer.seconds = (dt.getSeconds() < 10) ? '0' + dt.getSeconds() : dt.getSeconds()
				  
				  t = setTimeout(time, 1000); //设置定时器，循环运行
				}
			},
			// h5 搜索
			onNavigationBarSearchInputClicked: async function(e) {
				uni.navigateTo({
					url: `/pages/index/search`
				})
			},
			// 小程序搜索
			search() {
				uni.navigateTo({
					url: `/pages/index/search`
				})
			},
			// 文章详情
			goArticle(id) {
				uni.navigateTo({
					url: '/pages/article/detail?id=' + id
				})
			}
		}
	}
</script>

<style lang="scss">
/* #ifdef MP */
.mp-search-box{
	position:absolute;
	left: 0;
	top: 30upx;
	z-index: 9999;
	width: 100%;
	padding: 0 20upx;
	.ser-input{
		flex:1;
		height: 56upx;
		line-height: 56upx;
		text-align: center;
		font-size: 28upx;
		color:$font-color-base;
		border-radius: 20px;
		background-color: rgb(246, 249, 255);
	}
}
page{
	.cate-section{
		position:relative;
		z-index:5;
		border-radius:16upx 16upx 0 0;
		margin-top:-20upx;
	}
	.carousel-section{
		padding: 0;
		.titleNview-placing {
			padding-top: 0;
			height: 0;
		}
		.carousel{
			.carousel-item{
				padding: 0;
			}
		}
		.swiper-dots{
			left:45upx;
			bottom:40upx;
		}
	}
}
/* #endif */
</style>