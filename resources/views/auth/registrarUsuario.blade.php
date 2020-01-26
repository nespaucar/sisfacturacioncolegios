@include('auth.header')
<div class="wrapper-page" style="margin-top: 50px; margin-bottom: 0px">
    <div class="card-box">
        <div class="text-center">
            <a class="logo-lg"><i class="md md-equalizer"></i> <span>Registrar Usuario</span> </a>
        </div>
        <div class="panel-body">
            @if (isset($error))
                <div class="alert alert-success">
                    @if($error == 'OK') 
                        {{ 'Usuario registrado Correctamente.' }}
                    @else
                        {{ 'No se pudo registrar Usuario.' }}
                    @endif
                </div>
            @endif

            <form class="form-horizontal" role="form" method="POST" action="{{ url('/registro') }}">
                {{ csrf_field() }}
                
                <div class="form-group">
                    <div class="col-xs-12">
                        <select name="tipo" id="tipo" class="form-control">
                            <option value="3">Alumno</option>
                        </select>
                        <i class="md md-account-circle form-control-feedback l-h-34"></i>
                    </div>
                </div>

                <div class="form-group{{ $errors->has('nombres') ? ' has-error' : '' }}">
                    <div class="col-xs-12">
                        <input name="nombres" class="form-control" type="text" value="{{ $nombres or old('nombres') }}" placeholder="Nombres">
                        <i class="md md-account-box form-control-feedback l-h-34"></i>
                        @if ($errors->has('nombres'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombres') }}</strong>
                            </span>
                        @endif
                    </div>
                </div> 

                <div class="form-group{{ $errors->has('apellidopaterno') ? ' has-error' : '' }}">
                    <div class="col-xs-12">
                        <input name="apellidopaterno" class="form-control" type="text" value="{{ $apellidopaterno or old('apellidopaterno') }}" placeholder="Apellido Paterno">
                        <i class="md md-account-box form-control-feedback l-h-34"></i>
                        @if ($errors->has('apellidopaterno'))
                            <span class="help-block">
                                <strong>{{ $errors->first('apellidopaterno') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>  

                <div class="form-group{{ $errors->has('apellidomaterno') ? ' has-error' : '' }}">
                    <div class="col-xs-12">
                        <input name="apellidomaterno" class="form-control" type="text" value="{{ $apellidomaterno or old('apellidomaterno') }}" placeholder="Apellido Materno">
                        <i class="md md-account-box form-control-feedback l-h-34"></i>
                        @if ($errors->has('apellidomaterno'))
                            <span class="help-block">
                                <strong>{{ $errors->first('apellidomaterno') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('dni') ? ' has-error' : '' }}">
                    <div class="col-xs-12">
                        <input name="dni" class="form-control" type="text" value="{{ $dni or old('dni') }}" placeholder="DNI">
                        <i class="md md-account-box form-control-feedback l-h-34"></i>
                        @if ($errors->has('dni'))
                            <span class="help-block">
                                <strong>{{ $errors->first('dni') }}</strong>
                            </span>
                        @endif
                    </div>
                </div> 

                <div class="form-group{{ $errors->has('direccion') ? ' has-error' : '' }}">
                    <div class="col-xs-12">
                        <input name="direccion" class="form-control" type="text" value="{{ $direccion or old('direccion') }}" placeholder="Dirección">
                        <i class="md md-account-balance form-control-feedback l-h-34"></i>
                        @if ($errors->has('direccion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('direccion') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">

                    <div class="col-md-12">
                        <input id="email" type="email" class="form-control" name="email" value="{{ $email or old('email') }}" placeholder="Correo Electrónico">
                        <i class="md md-mail form-control-feedback l-h-34"></i>

                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">

                    <div class="col-md-12">
                        <input id="password" type="password" class="form-control" name="password" placeholder="Contraseña">
                        <i class="md md-vpn-key form-control-feedback l-h-34"></i>

                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">

                    <div class="col-md-12">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirme Contraseña">
                        <i class="md md-vpn-key form-control-feedback l-h-34"></i>

                        @if ($errors->has('password_confirmation'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group" style="text-align: center">
                    <div class="col-md-6">
                        <button class="btn btn-primary btn-custom w-md waves-effect waves-light" type="submit">
                            Registrar
                        </button>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ url('/login') }}" class="btn btn-success btn-custom w-md waves-effect waves-light">
                            Retornar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('auth.footer')