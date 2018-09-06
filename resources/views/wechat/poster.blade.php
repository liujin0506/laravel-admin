<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>推广海报</title>

        <!-- Styles -->
        <style>
            html, body {
                font-family: PingFangSC-Regular, sans-serif;
                background-color: #fff;
                color: #636b6f;
                font-weight: 100;
                margin: 0;
                width: 350px;
                height: 500px;
            }
            .main {
                width: 350px;
                height: 500px;
                background: #ffffff;
                position: relative;
            }
            .main .thumb {
                width: 100%;
            }
            .main .content {
                padding: 10px;
                position: absolute;
                width: 180px;
                float: left;
                background: #ffffff;
            }
            .main .title {
                padding: 10px;
                height: 38px;
                font-size: 14px;
                overflow:hidden;
                text-overflow:ellipsis;
                display:-webkit-box;
                -webkit-box-orient:vertical;
                -webkit-line-clamp:2;
                color: #000000;
            }
            .main .content .realprice {
                font-size: 12px;
            }
            .main .content .realprice span {
                text-decoration: line-through;
            }
            .main .content .discount {
                font-size: 13px;
                color: #ff0000;
            }
            .main .content .discount span {
                font-size: 15px;
            }
            .main .content .scan {
                font-size: 12px;
                margin-top: 20px;
            }
            .main .qrcode {
                width: 150px;
                float: right;
                text-align: center;
            }
            .main .qrcode div {
                width: 150px;
                margin-top: -8px;
                font-size: 12px;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="main">
            <img class="thumb" src="{{ $thumb }}"/>
            <div class="title">
                {{ $title }}
            </div>
            <div class="content">
                <div class="realprice">
                    原价 <span>￥{{ $real_price }}</span>
                </div>
                <div class="discount">
                    券后价：<span>￥{{ $new_price }}</span>
                </div>
                <div class="scan">长按扫描二维码查看商品</div>
            </div>
            <div class="qrcode">
                {!! QrCode::size(90)->margin(0)->generate($url); !!}
            </div>
        </div>
    </body>
</html>
