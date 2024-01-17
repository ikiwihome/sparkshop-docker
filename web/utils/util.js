export default {
	
	/**
	 * 获取分享海报
	 * @param array imgList 海报素材
	 * @param string goodsName 素材文字
	 * @param string price 价格
	 * @param string originPrice 原始价格
	 * @param function successFn 回调函数
	 */
	poster2canvas: function(imgList, goodsName, price, originPrice, successFn) {
		let that = this;
		uni.showLoading({
			title: `海报生成中`,
			mask: true
		});
		const ctx = uni.createCanvasContext('myCanvas');
		ctx.clearRect(0, 0, 0, 0);
	
		/**
		 * 只能获取合法域名下的图片信息,本地调试无法获取
		 */
		ctx.fillStyle = '#fff';
		ctx.fillRect(0, 0, 750, 1150);
		uni.getImageInfo({
			src: imgList[0],
			success: function(res) {
				const WIDTH = res.width;
				const HEIGHT = res.height;
				// ctx.drawImage(arr2[0], 0, 0, WIDTH, 1050);
				ctx.drawImage(imgList[1], 0, 0, WIDTH, WIDTH);
				ctx.save();
				let r = 110;
				let d = r * 2;
				let cx = 480;
				let cy = 790;
				ctx.arc(cx + r, cy + r, r, 0, 2 * Math.PI);
				// ctx.clip();
				ctx.drawImage(imgList[2], cx, cy, d, d);
				ctx.restore();
				const CONTENT_ROW_LENGTH = 20;
				let [contentLeng, contentArray, contentRows] = that.textByteLength(goodsName, CONTENT_ROW_LENGTH);
				if (contentRows > 2) {
					contentRows = 2;
					let textArray = contentArray.slice(0, 2);
					textArray[textArray.length - 1] += '……';
					contentArray = textArray;
				}
				ctx.setTextAlign('left');
				ctx.setFontSize(36);
				ctx.setFillStyle('#000');
				// let contentHh = 36 * 1.5;
				let contentHh = 36;
				for (let m = 0; m < contentArray.length; m++) {
					if (m) {
						ctx.fillText(contentArray[m], 50, 1000 + contentHh * m + 18, 1100);
					} else {
						ctx.fillText(contentArray[m], 50, 1000 + contentHh * m, 1100);
					}
				}
				ctx.setTextAlign('left')
				ctx.setFontSize(72);
				ctx.setFillStyle('#DA4F2A');
				ctx.fillText(`￥` + price, 40, 820 + contentHh);
	
				ctx.setTextAlign('left')
				ctx.setFontSize(36);
				ctx.setFillStyle('#999');
	
				if (originPrice) {
					ctx.fillText(`￥` + originPrice, 50, 876 + contentHh);
					var underline = function(ctx, text, x, y, size, color, thickness, offset) {
						var width = ctx.measureText(text).width;
						switch (ctx.textAlign) {
							case "center":
								x -= (width / 2);
								break;
							case "right":
								x -= width;
								break;
						}
	
						y += size + offset;
	
						ctx.beginPath();
						ctx.strokeStyle = color;
						ctx.lineWidth = thickness;
						ctx.moveTo(x, y);
						ctx.lineTo(x + width, y);
						ctx.stroke();
					}
					underline(ctx, `￥` + originPrice, 55, 865, 36, '#999', 2, 0)
				}
				ctx.setTextAlign('left')
				ctx.setFontSize(28);
				ctx.setFillStyle('#999');
				ctx.fillText(`长按或扫描查看`, 490, 1030 + contentHh);
				
				ctx.draw(true, setTimeout(() => {
					uni.canvasToTempFilePath({
						canvasId: 'myCanvas',
						fileType: 'png',
						destWidth: WIDTH,
						destHeight: HEIGHT,
						success: function(res) {
							uni.hideLoading();
							successFn && successFn(res.tempFilePath);
						}
					})
				}, 300));
			},
			fail: function(err) {
				uni.hideLoading();
				uni.showToast({
					title: `无法获取图片信息`
				})
			}
		})
	},
	pathToBase64: function (path) {
	    return new Promise(function(resolve, reject) {
	        if (typeof window === 'object' && 'document' in window) {
	            if (typeof FileReader === 'function') {
	                var xhr = new XMLHttpRequest()
	                xhr.open('GET', path, true)
	                xhr.responseType = 'blob'
	                xhr.onload = function() {
	                    if (this.status === 200) {
	                        let fileReader = new FileReader()
	                        fileReader.onload = function(e) {
	                            resolve(e.target.result)
	                        }
	                        fileReader.onerror = reject
	                        fileReader.readAsDataURL(this.response)
	                    }
	                }
	                xhr.onerror = reject
	                xhr.send()
	                return
	            }
	            var canvas = document.createElement('canvas')
	            var c2x = canvas.getContext('2d')
	            var img = new Image
	            img.onload = function() {
	                canvas.width = img.width
	                canvas.height = img.height
	                c2x.drawImage(img, 0, 0)
	                resolve(canvas.toDataURL())
	                canvas.height = canvas.width = 0
	            }
	            img.onerror = reject
	            img.src = path
	            return
	        }
	        if (typeof plus === 'object') {
	            plus.io.resolveLocalFileSystemURL(getLocalFilePath(path), function(entry) {
	                entry.file(function(file) {
	                    var fileReader = new plus.io.FileReader()
	                    fileReader.onload = function(data) {
	                        resolve(data.target.result)
	                    }
	                    fileReader.onerror = function(error) {
	                        reject(error)
	                    }
	                    fileReader.readAsDataURL(file)
	                }, function(error) {
	                    reject(error)
	                })
	            }, function(error) {
	                reject(error)
	            })
	            return
	        }
	        if (typeof wx === 'object' && wx.canIUse('getFileSystemManager')) {
	            wx.getFileSystemManager().readFile({
	                filePath: path,
	                encoding: 'base64',
	                success: function(res) {
	                    resolve('data:image/png;base64,' + res.data)
	                },
	                fail: function(error) {
	                    reject(error)
	                }
	            })
	            return
	        }
	        reject(new Error('not support'))
	    })
	},
	/**
	 * 生成海报获取文字
	 * @param string text 为传入的文本
	 * @param int num 为单行显示的字节长度
	 * @return array 
	 */
	textByteLength: function(text, num) {
		let strLength = 0;
		let rows = 1;
		let str = 0;
		let arr = [];
		for (let j = 0; j < text.length; j++) {
			if (text.charCodeAt(j) > 255) {
				strLength += 2;
				if (strLength > rows * num) {
					strLength++;
					arr.push(text.slice(str, j));
					str = j;
					rows++;
				}
			} else {
				strLength++;
				if (strLength > rows * num) {
					arr.push(text.slice(str, j));
					str = j;
					rows++;
				}
			}
		}
		arr.push(text.slice(str, text.length));
		return [strLength, arr, rows] //  [处理文字的总字节长度，每行显示内容的数组，行数]
	}
}