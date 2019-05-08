# BaiduAip
百度图片识别相关的包

使用

```

 $ocr = new AipOcr($appId, $apiKey, $secretKey);
 $ocr->receipt(file_get_contents($imagePath));

```

其他的Aip接口也是类似的用法，直接传入所有配置之后获取对象后调用即可。