<div class="row mt-1">
    <div class="col-12">

        <div class="row" id="dashboard-analytics">
            <div class="col-sm-6 col-12 mt-1">
                <div class="card h-80 p-2 art-2">
                    <div class="card-header d-flex align-items-center text-right pb-0 pt-0 white">
                        <h2 class="mt-1 mb-0 text-white font-weight-light"><b>Saldo disponible</b></h2>
                    </div>
                    <div class="card-sub d-flex align-items-center">
                        <h1 class="text-white mb-0"><b style="color: #66FFCC;">$ {{Auth::user()->saldoDisponible()}}</b></h1>
                    </div>

                    <div class="card-header d-flex align-items-center mt-3">
                        <a class="btn btn-dark rounded" href="{{route('settlement.withdraw')}}" style="border: 1px solid #66FFCC;"><b>RETIRAR</b></a>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-12 mt-1">
                <div class="card h-80 p-2" style="background: #173138;">
                    <div class="card-header d-flex align-items-center text-right pb-0 pt-0 white">
                        <h2 class="mt-1 mb-0 text-white font-weight-light"><b>Ganacia Actual</b></h2>
                    </div>

                    <div class="card-sub d-flex align-items-center ">
                        <h1 class="gold text-bold-700 mb-0"><b>$ {{Auth::user()->gananciaActual()}}</b></h1>
                    </div>

                    <div class="d-flex align-items-center">

                        <div class="progress ml-2 mt-2 mb-2">
                            <div id="bar" class="progress-bar active" role="progressbar" aria-valuenow="0"
                                aria-valuemin="0" aria-valuemax="100" style="width: {{Auth::user()->progreso()}}%;">
                            </div>
                        </div>

                        <div class="card-sub d-flex align-items-center ">
                            <p class="text-bold-700 mb-0 text-white">{{round(Auth::user()->progreso() * 2, 2)}}% </p>
                        </div>

                    </div>

                    <div class="card-sub align-items-center mt-0 ">
                        <h6 class="text-bold-700 mb-0 text-white"><b>Activo {{Auth::user()->fechaActivo()}}</b></h6>
                    </div>

                </div>
            </div>


                <div class="col-sm-6 col-md-3 col-12 mt-1">
                    <div class="card pt-2" style="background: #173138; height: 280px;">
                        <div class="card-header d-flex align-items-center text-right pb-0 pt-0 white">
                            <h5 class="mt-1 mb-0 text-white text-center"><b>Paquete de inversión</b></h5>
                        </div>

                        <div class="card-header d-flex align-items-center mb-0 justify-content-center">
                            <img class="text-center" src="{{Auth::user()->inversionMasAlta() != null ?Auth::user()->inversionMasAlta()->getPackageOrden->img() : asset('assets/img/royal_green/logos/logo.svg')}}" alt=""
                                style="width: @if(Auth::user()->inversionMasAlta() == null)70% @else 62% @endif; margin-top: -15px;">
                        </div>
                        <div class="card-sub d-flex align-items-center">
                            <div class="progresscircle blue" data-value='{{Auth::user()->progreso()}}'>
                                <span class="progress-left">
                                    <span class="progress-circle"></span>
                                </span>
                                <span class="progress-right">
                                    <span class="progress-circle"></span>
                                </span>
                                <div class="progress-value">{{round(Auth::user()->progreso(), 2)}}%</div>
                            </div>
                        </div>
                        

                    </div>
                </div>

                <div class="col-sm-6 col-md-5 col-12 mt-1">
                    <div class="card pt-2" style="background: #173138; height: 280px;">
                        <div class="card-header d-flex align-items-center text-right pb-0 pt-0 white">
                            <h5 class="mt-1 mb-0 text-white"><b>Link de referido</b></h5>
                        </div>

                        <div class="card-sub d-flex align-items-center ">
                            <h2 class="gold text-bold-700 mb-0">INVITA A<br>PERSONAS<br></h2>
                            <svg style="position: absolute; right: 10px;" width="143" height="103" viewBox="0 0 143 103" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M48.5179 0C43.7829 0.018985 39.2473 1.92414 35.8991 5.30038C32.551 8.67661 30.6617 13.2503 30.6429 18.025C30.6429 27.9233 38.7019 36.05 48.5179 36.05C58.3338 36.05 66.3929 27.9233 66.3929 18.025C66.3929 8.1267 58.3338 0 48.5179 0ZM94.4821 0C89.7472 0.018985 85.2116 1.92414 81.8634 5.30038C78.5153 8.67661 76.626 13.2503 76.6071 18.025C76.6071 27.9233 84.6662 36.05 94.4821 36.05C104.298 36.05 112.357 27.9233 112.357 18.025C112.357 8.1267 104.298 0 94.4821 0ZM48.5179 10.3C52.8079 10.3 56.1786 13.699 56.1786 18.025C56.1786 22.351 52.8079 25.75 48.5179 25.75C44.2279 25.75 40.8571 22.351 40.8571 18.025C40.8571 13.699 44.2279 10.3 48.5179 10.3ZM94.4821 10.3C98.7721 10.3 102.143 13.699 102.143 18.025C102.143 22.351 98.7721 25.75 94.4821 25.75C90.1921 25.75 86.8214 22.351 86.8214 18.025C86.8214 13.699 90.1921 10.3 94.4821 10.3ZM25.5357 30.9C14.3 30.9 5.10714 40.17 5.10714 51.5C5.10714 57.232 7.54325 62.4025 11.3327 66.1466C7.85644 68.5121 5.00651 71.699 3.03125 75.4295C1.056 79.1601 0.0153609 83.3211 0 87.55H10.2143C10.2143 78.9598 17.017 72.1 25.5357 72.1C34.0544 72.1 40.8571 78.9598 40.8571 87.55H51.0714C51.0561 83.3211 50.0154 79.1601 48.0402 75.4295C46.0649 71.699 43.215 68.5121 39.7387 66.1466C43.5282 62.4025 45.9643 57.2371 45.9643 51.5C45.9643 40.17 36.7714 30.9 25.5357 30.9ZM51.0714 87.55C47.8795 91.8554 45.9643 97.2681 45.9643 103H56.1786C56.1786 94.4098 62.9813 87.55 71.5 87.55C80.0187 87.55 86.8214 94.4098 86.8214 103H97.0357C97.0269 97.4299 95.2358 92.0114 91.9286 87.55C90.1921 85.217 88.0982 83.224 85.703 81.5966C89.4925 77.8526 91.9286 72.6871 91.9286 66.95C91.9286 55.62 82.7357 46.35 71.5 46.35C60.2643 46.35 51.0714 55.62 51.0714 66.95C51.0714 72.682 53.5075 77.8526 57.297 81.5966C54.9063 83.216 52.8019 85.2284 51.0714 87.55ZM91.9286 87.55H102.143C102.143 78.9598 108.946 72.1 117.464 72.1C125.983 72.1 132.786 78.9598 132.786 87.55H143C142.985 83.3211 141.944 79.1601 139.969 75.4295C137.993 71.699 135.144 68.5121 131.667 66.1466C135.457 62.4025 137.893 57.2371 137.893 51.5C137.893 40.17 128.7 30.9 117.464 30.9C106.229 30.9 97.0357 40.17 97.0357 51.5C97.0357 57.232 99.4718 62.4025 103.261 66.1466C99.785 68.5121 96.9351 71.699 94.9598 75.4295C92.9846 79.1601 91.9439 83.3211 91.9286 87.55ZM25.5357 41.2C31.2404 41.2 35.75 45.7475 35.75 51.5C35.75 57.2526 31.2404 61.8 25.5357 61.8C19.831 61.8 15.3214 57.2526 15.3214 51.5C15.3214 45.7475 19.831 41.2 25.5357 41.2ZM117.464 41.2C123.169 41.2 127.679 45.7475 127.679 51.5C127.679 57.2526 123.169 61.8 117.464 61.8C111.76 61.8 107.25 57.2526 107.25 51.5C107.25 45.7475 111.76 41.2 117.464 41.2ZM71.5 56.65C77.2047 56.65 81.7143 61.1975 81.7143 66.95C81.7143 72.7026 77.2047 77.25 71.5 77.25C65.7953 77.25 61.2857 72.7026 61.2857 66.95C61.2857 61.1975 65.7953 56.65 71.5 56.65Z" fill="#204446"/>
                                </svg>
                                
                        </div>
                        <div class="card-header d-flex align-items-center white mt-2">
                            <button class="btn-darks btn-block mt-4 rounded" style="boder-color=#66FFCC; position: //" onclick="getlink('{{Auth::user()->binary_side_register}}')">
                                LINK DE REFERIDO
                                <svg style="position: relative; top: -4px;left: 10px;" width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11.5 15V16.75C11.5 17.2141 11.3156 17.6592 10.9874 17.9874C10.6592 18.3156 10.2141 18.5 9.75 18.5H2.75C2.28587 18.5 1.84075 18.3156 1.51256 17.9874C1.18437 17.6592 1 17.2141 1 16.75V7.125C1 6.66087 1.18437 6.21575 1.51256 5.88756C1.84075 5.55937 2.28587 5.375 2.75 5.375H4.5L11.5 15ZM10.3494 1H6.25C5.78587 1 5.34075 1.18437 5.01256 1.51256C4.68437 1.84075 4.5 2.28587 4.5 2.75V13.25C4.5 13.7141 4.68437 14.1592 5.01256 14.4874C5.34075 14.8156 5.78587 15 6.25 15H13.25C13.7141 15 14.1592 14.8156 14.4874 14.4874C14.8156 14.1592 15 13.7141 15 13.25V5.58675C15 5.35361 14.9534 5.12284 14.8629 4.90796C14.7724 4.69309 14.64 4.49846 14.4733 4.3355L11.5726 1.49875C11.2457 1.17908 10.8066 1.00006 10.3494 1V1Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                        
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-4 col-12 mt-1">
                    <div class="card h-80 p-2" style="background: #173138; height: 280px;">
                        <div class="card-header d-flex align-items-center text-right pb-0 pt-0 white">
                            <h5 class="mt-1 mb-0 text-white"><b>Lado Binario</b></h5>
                        </div>

                        <div class="card-sub d-flex align-items-center ">
                            <h1 class="gold text-bold-700 mb-0">
                                @if (Auth::user()->binary_side_register == 'I')
                                IZQUIERDA
                                @else
                                DERECHA
                                @endif
                            </h1>
                            <svg style="position: absolute; right: 20px; top: 20px;" width="117" height="117" viewBox="0 0 107 107" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M49.8214 57.1746L49.8214 76.0696C46.3538 76.964 43.3317 79.091 41.3217 82.052C39.3117 85.0129 38.4517 88.6045 38.903 92.1535C39.3543 95.7025 41.0858 98.9653 43.773 101.33C46.4602 103.695 49.9186 105 53.5 105C57.0814 105 60.5398 103.695 63.227 101.33C65.9142 98.9653 67.6457 95.7026 68.097 92.1535C68.5483 88.6045 67.6883 85.0129 65.6783 82.052C63.6683 79.091 60.6462 76.964 57.1786 76.0696L57.1786 57.1746L86.6071 57.1746L86.6071 76.0696C83.1395 76.964 80.1174 79.091 78.1074 82.052C76.0974 85.0129 75.2374 88.6045 75.6887 92.1535C76.14 95.7026 77.8715 98.9653 80.5587 101.33C83.2459 103.695 86.7043 105 90.2857 105C93.867 105 97.3255 103.695 100.013 101.33C102.7 98.9653 104.431 95.7026 104.883 92.1535C105.334 88.6045 104.474 85.0129 102.464 82.052C100.454 79.091 97.4319 76.964 93.9643 76.0696L93.9643 57.1746C93.9623 55.2261 93.1866 53.3579 91.8072 51.9801C90.4279 50.6022 88.5578 49.8273 86.6071 49.8254L57.1786 49.8254L57.1786 30.9304C60.6462 30.036 63.6683 27.909 65.6783 24.948C67.6883 21.9871 68.5483 18.3955 68.097 14.8465C67.6457 11.2975 65.9142 8.03467 63.227 5.66972C60.5398 3.30476 57.0814 2 53.5 2C49.9186 2 46.4602 3.30476 43.773 5.66972C41.0858 8.03467 39.3543 11.2975 38.903 14.8465C38.4517 18.3955 39.3117 21.9871 41.3217 24.948C43.3317 27.909 46.3538 30.036 49.8214 30.9304L49.8214 49.8254L20.3929 49.8254C18.4423 49.8273 16.5721 50.6022 15.1928 51.9801C13.8134 53.3579 13.0377 55.2261 13.0357 57.1746L13.0357 76.0696C9.56811 76.964 6.54606 79.091 4.53604 82.052C2.52602 85.0129 1.66605 88.6045 2.11732 92.1535C2.56858 95.7025 4.30011 98.9653 6.98732 101.33C9.67454 103.695 13.133 105 16.7143 105C20.2957 105 23.7541 103.695 26.4413 101.33C29.1285 98.9653 30.86 95.7025 31.3113 92.1535C31.7626 88.6045 30.9026 85.0129 28.8926 82.052C26.8826 79.091 23.8605 76.964 20.3929 76.0696L20.3929 57.1746L49.8214 57.1746ZM60.8571 90.2464C60.8571 91.6999 60.4256 93.1208 59.6172 94.3294C58.8088 95.538 57.6598 96.48 56.3155 97.0362C54.9711 97.5925 53.4918 97.738 52.0647 97.4545C50.6376 97.1709 49.3266 96.4709 48.2977 95.4431C47.2688 94.4153 46.5681 93.1058 46.2842 91.6802C46.0004 90.2545 46.146 88.7768 46.7029 87.4339C47.2597 86.091 48.2027 84.9432 49.4126 84.1357C50.6225 83.3281 52.0449 82.8971 53.5 82.8971C55.4506 82.8991 57.3208 83.674 58.7001 85.0518C60.0794 86.4296 60.8552 88.2978 60.8571 90.2464ZM97.6428 90.2464C97.6428 91.6999 97.2113 93.1208 96.4029 94.3294C95.5945 95.538 94.4455 96.48 93.1011 97.0362C91.7568 97.5925 90.2775 97.738 88.8504 97.4545C87.4232 97.1709 86.1123 96.4709 85.0834 95.4431C84.0545 94.4153 83.3538 93.1058 83.0699 91.6802C82.786 90.2545 82.9317 88.7768 83.4886 87.4339C84.0454 86.091 84.9884 84.9432 86.1983 84.1357C87.4082 83.3281 88.8306 82.8971 90.2857 82.8971C92.2363 82.8991 94.1065 83.674 95.4858 85.0518C96.8651 86.4296 97.6409 88.2978 97.6428 90.2464ZM46.1429 16.7536C46.1429 15.3001 46.5744 13.8792 47.3828 12.6706C48.1912 11.462 49.3402 10.52 50.6845 9.96376C52.0289 9.40751 53.5082 9.26197 54.9353 9.54555C56.3625 9.82912 57.6734 10.5291 58.7023 11.5569C59.7312 12.5847 60.4319 13.8942 60.7158 15.3198C60.9997 16.7455 60.854 18.2232 60.2971 19.5661C59.7403 20.909 58.7973 22.0568 57.5874 22.8643C56.3775 23.6719 54.9551 24.1029 53.5 24.1029C51.5494 24.1009 49.6792 23.326 48.2999 21.9482C46.9206 20.5704 46.1448 18.7022 46.1429 16.7536ZM24.0715 90.2464C24.0715 91.6999 23.64 93.1208 22.8315 94.3294C22.0231 95.538 20.8741 96.48 19.5298 97.0362C18.1854 97.5925 16.7061 97.738 15.279 97.4544C13.8519 97.1709 12.541 96.4709 11.512 95.4431C10.4831 94.4153 9.78242 93.1058 9.49854 91.6802C9.21466 90.2545 9.36036 88.7768 9.9172 87.4339C10.474 86.091 11.417 84.9432 12.6269 84.1357C13.8368 83.3281 15.2592 82.8971 16.7143 82.8971C18.665 82.8991 20.5351 83.674 21.9144 85.0518C23.2938 86.4296 24.0695 88.2978 24.0715 90.2464Z" fill="#204446" stroke="#204446" stroke-width="2.5"/>
                                </svg>
                                
                        </div>
                        <div class="row no-gutters card-header align-items-center h-100 mt-5">

                            @if (Auth::user()->binary_side_register == 'I')
                                <div class="col">     
                                    {{-- <a href="#"
                                        class="btn btn-primary btn-block padding-button-short mt-1 waves-effect waves-light text-white"
                                        v-on:click="updateBinarySide('I')">
                                        IZQUIERDA
                                    </a> --}}
                                    <label class="content-input">
                                        <input type="radio">IZQUIERDA
                                        <div class="radioclass">
                                            <i class="radiochecked"></i>
                                        </div>
                                    </label>
                                </div>
                                <div class="col">
                                    {{-- <a href="#"
                                        class="btn btn-block btn-outline-warning padding-button-short mt-1 waves-effect waves-light text-white"
                                        v-on:click="updateBinarySide('D')" style="height: 44.78px">
                                        DERECHA
                                    </a> --}}
                                    <label class="content-input">
                                        <input type="radio" v-on:click="updateBinarySide('D')">DERECHA
                                        <div class="radioclass">
                                        </div>
                                    </label>
                                </div>
                            @else
                                    <div class="col">
                                        {{-- <a href="#"
                                            class="btn btn-block btn-outline-warning padding-button-short mt-1 waves-effect waves-light text-white"
                                            v-on:click="updateBinarySide('I')">
                                            IZQUIERDA
                                        </a> --}}
                                        <label class="content-input">
                                            <input type="radio" v-on:click="updateBinarySide('I')">IZQUIERDA
                                            <div class="radioclass">
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col">
                                        {{-- <a href="#"
                                            class="btn btn-block btn-primary padding-button-short mt-1 waves-effect waves-light text-white"
                                            v-on:click="updateBinarySide('D')" style="height: 44.78px">
                                            DERECHA
                                        </a> --}}
                                        <label class="content-input">
                                            <input type="radio">DERECHA
                                            <div class="radioclass">
                                                <i class="radiochecked"></i>
                                            </div>
                                        </label>
                                    </div>
                                
                            @endif


                        </div>
                    </div>
                </div>
            
            
        </div>
    </div>

            <div class="col-sm-6 col-12 mt-1">
                <div class="card h-100" style="background: #173138;">
                    

                    <input type="hidden" id="idrango"
                    value="{{(Auth::user()->rank_id == null) ? 0 : Auth::user()->rank_id}}">
                    <div class="card-header d-flex align-items-center mb-2 carrusel_rango">
                        <div class="item text-center">
                            <img src="{{ asset('assets/img/royal_green/rangos/sin_rango.svg') }}" alt="" height="110" class="m-auto">
                            <h5 class="text-white mb-0" style="width: 150px; font-size: 12pt;">
                                <strong>Sin Rango</strong>
                            </h5>
                        </div>
                        @foreach ($data['rangos']['ranks'] as $rango)
                        <div class="item text-center">
                            <img src="{{$rango->img}}" alt="" height="110" class="m-auto">
                            <h5 class="text-white mb-0" style="width: 150px; font-size: 12pt;">
                                <strong>{{$rango->name}}</strong>
                            </h5>
                        </div>
                        @endforeach
                    </div>

                    <div class="card-header d-flex align-items-center mb-2 ">
                        <img src="{{asset('assets/img/Line28.svg')}}" alt="" style="width: 100%;" height="1">
                    </div>
                    <div class="hr"></div>
                    <div class="card-header text-right pb-0 pt-0 white">
                        <p class="mt-1 mb-0">Rango Actual:  {{$data['rangos']['name_rank_actual']}}</p>
                    </div>
                    <div class="card-header d-flex align-items-center text-right pb-0 pt-0 white mt-2">
                        <p class="mt-1 mb-0">Total Puntos:</p>
                    </div>

                    <div class="card-sub d-flex align-items-center ">
                        <h2 class="gold text-bold-700 mb-1">{{$data['rangos']['puntos']}}</h2>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="progress ml-2">
                            <div id="bar" class="progress-bar active" role="progressbar" aria-valuenow="0"
                                aria-valuemin="0" aria-valuemax="100" style="width: {{$data['rangos']['porcentage']}}%">
                                <span class="sr-only">{{$data['rangos']['porcentage']}}% Complete</span>
                            </div>
                        </div>
                        <div class="card-sub d-flex align-items-center ">
                            <p class="white text-bold-700" style="margin-top: -30px;">{{round($data['rangos']['porcentage'],2)}}% </p>
                        </div>
                    </div>
                    <div class="card-sub">
                        <p class="white text-bold-700" style="margin-top: -50px;">Próximo rango = {{$data['rangos']['puntos_sig']}} </p>
                    </div>
                    @isset($requisito)
                    <div class="alert alert-danger">
                        <p class="text-bold-700 text-center">{{$requisito}} para optar por el siguiente rango</p>
                    </div>
                    @endisset

                    
                </div>
            </div>

            <div class="col-sm-6 col-12 mt-1">
                <div class="card h-100 p-0" style="background: #173138;">
                    <div class="card-header d-flex align-items-center text-right pb-0 pt-0 white">
                        <h5 class="mt-1 mb-0 text-white"><b>Referidos - 2021</b></h5>
                    </div>
                        @include('dashboard.componente.partials.grafig-1')
                </div>
            </div>


            <div class="col-12 mt-3">
                <div class="card bg-lp">
                    <div class="card-content">
                        <div class="card-body card-dashboard p-0">
                            <div class="table-responsive">
                            <h3 class="text-white p-1">Últimos Pedidos</h3>
                                <table class="table nowrap scroll-horizontal-vertical myTable2">
                                    <thead>

                                        <tr class="text-center text-dark text-uppercase pl-2">                                
                                            <th>ID</th>
                                            <th>Email</th>
                                            <th>Monto</th>
                                            <th>Estado</th>
                                            <th>Fecha</th>
                                        </tr>

                                    </thead>
                                    <tbody>
                                        @foreach($ordenes as $orden)
                                        <tr class="text-center text-white pl-2">
                                            <td>{{$orden->id}}</td>
                                            <td>{{$orden->getOrdenUser->email}}</td>
                                            <td>{{$orden->total}}</td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <p style="margin: 0; padding: 0; position: relative;">
                                                        @if ($orden->status == '0')
                                                        <span class="dot enespera"></span> En Espera
                                                        @elseif($orden->status == '1')
                                                        <span class="dot completado"></span> Completado
                                                        @elseif($orden->status >= '2')
                                                        <span class="dot cancelado"></span> Cancelado
                                                        @endif
                                                    </p>     
                                                </div>
                                            </td>
                                            <td>{{$orden->created_at}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- permite llamar a las opciones de las tablas --}}
            @include('layouts.componenteDashboard.optionDatatable')

        </div>
    </div>
</div>


