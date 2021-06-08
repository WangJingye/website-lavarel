@extends('layouts.main')
@section('content')
    <style>
        .main-panel-content {
            padding: 1rem 2rem;
            background-color: #e9ecef;
        }

        .main-panel {
            width: 100%;
        }

        .main-panel-one {
            border: 1px solid #ccc;
            background-color: #fff;
            padding: 1rem;
            display: inline-flex;
            justify-content: space-between;
            width: 10rem;
            margin-bottom: 0.5rem;
            margin-top: 0.5rem;
        }

        .main-panel .iconfont {
            font-size: 2rem;
        }

        .main-panel .main-panel-icon {
            color: #28a745;
            padding-right: 0.5rem;
        }

        .main-panel .main-panel-title {
            color: #666
        }

        .main-panel .main-panel-text {
            font-size: 1rem;
        }

        .detail-title {
            text-align: center;
            background-color: #e9ecef;
            padding: 0.5rem;
        }

        .detail-info {
            border-left: 1px solid #ddd;
            border-right: 1px solid #ddd;
            display: inline-flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 0.5rem;
            height: 5rem;
        }

        .detail-info:last-child {
            border-bottom: 1px solid #ddd;
            display: inline-flex;
            width: 100%;
            padding: 0.5rem;
        }

        .detail-info-number {
            color: #dc3545
        }

        .detail-info-title {
            font-weight: bold;
            margin: 0.5rem;
        }

        .detail-info-one {
            width: 25%;
            text-align: center;
        }

        .undo-info {
            width: 100%;
        }

        .content-info {
            width: 100%;
            margin-bottom: 2rem;
        }

        .product-info, .user-info {
            margin-top: 2rem;
        }

        .product-info, .user-info {
            width: 100%;
        }

        a {
            color: #000;
        }

        a:hover {
            text-decoration: none;
        }

        @media (min-width: 1050px) {

            .main-panel {
                display: inline-flex;
                justify-content: space-between;
            }

            .main-panel-one {
                border: 1px solid #ccc;
                background-color: #fff;
                padding: 1.5rem;
                display: inline-flex;
                justify-content: space-between;
                width: 12rem;
            }

            .content-info {
                display: inline-flex;
                justify-content: space-between;
            }

            .detail-info-content-one {
                height: 2rem;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .detail-info-content-two {
                height: 4rem;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .product-info {
                width: 33%;
            }

            .user-info {
                width: 66%;
            }
        }

    </style>
@endsection