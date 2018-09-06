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
                height: 120px;
                width: 180px;
                float: left;
                background: #ffffff;
            }
            .main .content .title {
                font-size: 13px;
                overflow:hidden;
                text-overflow:ellipsis;
                display:-webkit-box;
                -webkit-box-orient:vertical;
                -webkit-line-clamp:3;
                color: #000000;
            }
            .main .content .title span {
                background: #ff0000;
                color: #ffffff;
            }
            .main .content .realprice {
                margin-top: 20px;
                font-size: 12px;
            }
            .main .content .realprice span {
                text-decoration: line-through;
            }
            .main .content .discount {
                font-size: 12px;
            }
            .main .content .discount span {
                font-size: 13px;
                color: #ff0000;
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
            <div class="content">
                <div class="title">
                    <span>JD 京东</span> {{ $title }}
                </div>
                <div class="realprice">
                    原价 <span>￥{{ $real_price }}</span>
                </div>
                <div class="discount">
                    券后价：<span>￥{{ $new_price }}</span>
                </div>
            </div>
            <div class="qrcode">
                {!! QrCode::size(120)->margin(1)->generate($url); !!}
                <div> - 扫码领券 - </div>
            </div>
        </div>
    </body>
</html>
