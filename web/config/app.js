

module.exports = {
	// 请求域名
	// BASE_URL: `https://mall.ikiwi.cc`,
	BASE_URL: `https://mall.ikiwi.cc`,
	// 以下配置在不做二开的前提下,不需要做任何的修改
	HEADER: {
		'Content-type': 'application/json',
		//#ifdef H5
		'X-CSRF-TOKEN': navigator.userAgent.toLowerCase().indexOf("micromessenger") !== -1 ? 'wechat' : 'h5',
		//#endif
		//#ifdef MP
		'X-CSRF-TOKEN': 'miniapp',
		//#endif
		//#ifdef APP-PLUS
		'X-CSRF-TOKEN': 'app',
		//#endif
	},
	// 会话token
	TOKEN: 'Authorization',
	
	// 缓存时间 0 永久
	EXPIRE: 0,
	
	// 分页最多显示条数
	LIMIT: 10
}
