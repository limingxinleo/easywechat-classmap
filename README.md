# EasyWeChat ClassMap

[![PHPUnit](https://github.com/limingxinleo/easywechat-classmap/actions/workflows/test.yml/badge.svg)](https://github.com/limingxinleo/easywechat-classmap/actions/workflows/test.yml)

```
composer require limingxinleo/easywechat-classmap
```

`EasyWeChat 6.x` 使用的是 `Symfony Http Client`，在常驻内存的服务中使用时，`HttpClient` 会因为多个协程共用，而报错。

## 使用

`Hyperf` 框架下，直接引入即可。
