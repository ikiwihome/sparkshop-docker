<template>
	<view class="content">
		<view class="row b-b">
			<text class="tit">提现方式</text>
			<uni-data-checkbox v-model="form.type" :localdata="applyWay" selectedColor="#e93323" ></uni-data-checkbox>
		</view>
		<view class="row b-b" v-if="form.type == '银行卡'">
			<text class="tit">卡号</text>
			<input class="input" type="text" v-model="form.card_no" placeholder="请输入卡号" placeholder-class="placeholder" />
		</view>
		<view class="row b-b" v-if="form.type == '银行卡'">
			<text class="tit">持卡人名</text>
			<input class="input" type="text" v-model="form.name" placeholder="请输入持卡人名" placeholder-class="placeholder" />
		</view>
		<view class="row b-b" v-if="form.type == '银行卡'"> 
			<text class="tit">银行</text>
			<input class="input" type="text" v-model="form.bank" placeholder="请输入银行" placeholder-class="placeholder" />
		</view>
		<view class="row b-b" v-if="form.type != '银行卡'">
			<text class="tit">账号</text>
			<input class="input" type="text" v-model="form.account" placeholder="请输入提现账号" placeholder-class="placeholder" />
		</view>
		<view class="row b-b">
			<text class="tit">提现金额</text>
			<input class="input" type="number" v-model="form.apply_amount" placeholder="请输入提现金额" placeholder-class="placeholder" />
		</view>
		<view class="row b-b" style="height: 150px;" v-if="form.type != '银行卡'">
			<text class="tit">收款码</text>
			<uni-file-picker
				fileMediatype="image"
				limit="1"
				@delete="deleteFile"
				@select="selectFile"
				title="最多选择1张图片">
			</uni-file-picker>
		</view>
		
		<p style="font-size: 12px;padding-left: 15px;margin-top: 10px;">当前最多可提现： <span style="color:#e93323">{{ agentInfo.residue_amount }}</span> &nbsp;&nbsp;元</p>
		<button class="add-btn" @click="confirm">提现</button>
	</view>
</template>

<script>
	import {
		BASE_URL
	} from '@/config/app';
	
	export default {
		data() {
			return {
				form: {
					type: '微信',
					card_no: '',
					name: '',
					bank: '',
					apply_amount: '',
					account: '',
					receive_qrcode: ''
				},
				agentInfo: {},
				applyWay: []
			}
		},
		onLoad(option) {
			this.getBaseInfo()
		},
		methods: {
			async getBaseInfo() {
				let res = await this.$api.agent.getAgentInfo.get()
				this.agentInfo = res.data
				
				let finalWay = [];
				res.data.way.forEach((item, index) => {
					if (index == 0) {
						this.form.type = item
					}
					
					finalWay.push({text: item, value: item})
				})
				
				this.applyWay = finalWay
			},
			deleteFile(file) {
				this.form.receive_qrcode = ''
			},
			// 选择文件
			selectFile(file) {
				this.fileUploadEnd = false
				uni.showLoading({
					title: '上传中...'
				});
				uni.uploadFile({
					url: BASE_URL + '/api/Common/uploadFile',
					filePath: file.tempFilePaths[0],
					name: 'file',
					success: (res) => {
						setTimeout(() => {
							uni.hideLoading();
						}, 2000);
						let resFile = JSON.parse(res.data)
						this.form.receive_qrcode = resFile.data.url
						this.fileUploadEnd = true
						this.$tool.msg('上传成功', 1500, false, 'error')
					}
				});
			},
			// 提交
			async confirm() {
			
				let res = await this.$api.agent.apply.post(this.form)
				if (res.code === 0) {
					this.$tool.msg('操作成功', 1500, false, 'success')
					setTimeout(() => {
						uni.navigateTo({url: '/pages/agent/withdrawal'})  
					}, 800)
				} else {
					this.$tool.msg(res.msg, 1500, false, 'error')
				}
			}
		}
	}
</script>

<style lang="scss">
	page{
		background: $page-color-base;
		padding-top: 16upx;
	}

	.row{
		display: flex;
		align-items: center;
		position: relative;
		padding:0 30upx;
		height: 110upx;
		background: #fff;
		
		.tit{
			flex-shrink: 0;
			font-size: 30upx;
			color: $font-color-dark;
			width: 150upx;
		}
		.input{
			flex: 1;
			font-size: 30upx;
			color: $font-color-dark;
		}
		.icon-shouhuodizhi{
			font-size: 36upx;
			color: $font-color-light;
		}
	}
	.default-row{
		margin-top: 16upx;
		.tit{
			flex: 1;
		}
		switch{
			transform: translateX(16upx) scale(.9);
		}
	}
	.add-btn{
		display: flex;
		align-items: center;
		justify-content: center;
		width: 690upx;
		height: 80upx;
		margin: 60upx auto;
		font-size: $font-lg;
		color: #fff;
		background-color: $base-color;
		border-radius: 10upx;
		box-shadow: 1px 2px 5px rgba(219, 63, 96, 0.4);
	}
	.title {
		font-size: 14px;
		font-weight: bold;
		margin: 20px 0 5px 0;
	}

	.data-pickerview {
		height: 400px;
		border: 1px #e5e5e5 solid;
	}

	.popper__arrow {
		top: -6px;
		left: 50%;
		margin-right: 3px;
		border-top-width: 0;
		border-bottom-color: #EBEEF5;
	}
	 .popper__arrow {
	    top: -6px;
	    left: 50%;
	    margin-right: 3px;
	    border-top-width: 0;
	    border-bottom-color: #EBEEF5;
	}
	.file-picker__box {width: 60px !important;padding-top: 60px !important;}
</style>
