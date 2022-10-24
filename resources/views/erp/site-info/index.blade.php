@extends('layouts.main')
<style>
    .main-panel {
        justify-content: center;
        align-items: center;
        display: flex;
        height: calc(100% - 11rem);
    }

    .main-panel-bottom {
        border-top: dashed 1px #cccccc;
        min-height: 3rem;
        line-height: 3rem;
        color: rgba(0,0,0,.45);
    }

    svg {
        width: 1rem;
    }
</style>
@section('content')
    <div>
        <div class="main-panel">
            <div class="text-center" style="font-size: 1.5rem">欢迎进入<?= app()->siteInfo->web_name?></div>
        </div>
        <div class="text-center main-panel-bottom">
            Copyright
            <svg data-v-d35c119a="" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="copyright"
                 role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                 class="svg-inline--fa fa-copyright fa-w-16">
                <path data-v-d35c119a="" fill="currentColor"
                      d="M256 8C119.033 8 8 119.033 8 256s111.033 248 248 248 248-111.033 248-248S392.967 8 256 8zm117.134 346.753c-1.592 1.867-39.776 45.731-109.851 45.731-84.692 0-144.484-63.26-144.484-145.567 0-81.303 62.004-143.401 143.762-143.401 66.957 0 101.965 37.315 103.422 38.904a12 12 0 0 1 1.238 14.623l-22.38 34.655c-4.049 6.267-12.774 7.351-18.234 2.295-.233-.214-26.529-23.88-61.88-23.88-46.116 0-73.916 33.575-73.916 76.082 0 39.602 25.514 79.692 74.277 79.692 38.697 0 65.28-28.338 65.544-28.625 5.132-5.565 14.059-5.033 18.508 1.053l24.547 33.572a12.001 12.001 0 0 1-.553 14.866z"
                      class=""></path>
            </svg> <?= app()->siteInfo->web_name?> 2022
        </div>
    </div>
@endsection