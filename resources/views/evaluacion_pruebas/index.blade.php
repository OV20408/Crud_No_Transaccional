@extends('adminlte::page')

@section('content')
<style>
  :root {
    --color-amarillo: #FFA726;
    --color-amarillo-hover: #FB8C00;
    --color-card: #ffffff;
    --color-texto-principal: #333333;
    --color-blanco: #f8f9fa;
    --color-azul: #007bff;
    --color-gris :#6c757d;
  }


  /* ======= Contenedor del selector de cuerpo ======= */
  .seleccion-cuerpo-box{
    padding:1.5rem;border-radius:12px;min-height:600px;
    transition:transform .3s, box-shadow .3s;margin-bottom:2rem;
    background:#fff;box-shadow:0 2px 8px rgba(0,0,0,.1);
  }
  .seleccion-cuerpo-box label{
    font-size:1.3rem;font-weight:600;color:var(--color-texto-principal);
    line-height:1.8;border-bottom:2px solid var(--color-texto-principal);
    display:block;margin-bottom:12px;
  }
 
  /* ======= Lienzo del cuerpo ======= */
  .human-body{width:207px;position:relative;height:550px;display:block;margin:40px auto;}
  .human-body svg{position:absolute;left:50%;fill:#424242;transition:filter .15s ease, transform .15s ease;}
  .human-body svg:hover{cursor:pointer;filter:brightness(1.05);transform:translateY(-1px);}
 
  /* Posiciones exactas */
  .human-body svg#head{margin-left:-28.297px;top:0;}
  .human-body svg#left-shoulder{margin-left:-54.766px;top:75px;}
  .human-body svg#right-shoulder{margin-left:13.297px;top:75px;}
  .human-body svg#left-arm{margin-left:-78.172px;top:118px;}
  .human-body svg#right-arm{margin-left:40px;top:118px;z-index:10001;}
  .human-body svg#chest{margin-left:-43.297px;top:94px;}
  .human-body svg#stomach{margin-left:-37.625px;top:136px;}
  .human-body svg#left-leg{margin-left:-46.813px;top:211px;z-index:9999;}
  .human-body svg#right-leg{margin-left:0;top:211px;z-index:9999;}
  .human-body svg#left-hand{margin-left:-105px;top:230px;}
  .human-body svg#right-hand{margin-left:70px;top:230px;z-index:10000;}
  .human-body svg#left-foot{margin-left:-35px;top:461px;}
  .human-body svg#right-foot{margin-left:0;top:461px;}
 
  /* Estados de color */
  .estado-muybien{fill:#4afb4a !important;}
  .estado-bien{fill:#a9ffa9 !important;}
  .estado-normal{fill:#f3f39d !important;}
  .estado-mal{fill:#fd8697 !important;}
  .estado-muymal{fill:#ff3131 !important;}


  /* Contenedor general tipo React */
  .form-container {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 40px 20px;
    width: 100%;
    min-height: 100vh;
  }

  .form-content {
    padding: 2rem 3rem;
    width: 100%;
    max-width: 1200px;
  }

  .form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
  }

  .form-titulo {
    font-size: 2.5rem;
    padding-bottom: 0.5rem;
    position: relative;
    margin: 0;
  }

  .form-titulo::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 30%;
    height: 3px;
    background: var(--color-azul);
  }

  .btns {
    display: flex;
    gap: 12px;
  }

  .btn-agregar-pregunta {
    background-color: var(--color-azul);
    color: white;
    font-weight: 600;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform .2s, box-shadow .2s;
  }

  .btn-agregar-pregunta:hover {
    background-color: var(--color-amarillo-hover);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.12);
  }

  .step-hidden { display:none; }

  /* Tarjetas suaves como en React */
  .card-soft {
    border: 0;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
    border-radius: 12px;
    overflow: hidden;
    background: var(--color-card);
  }

  .card-soft .card-header {
    border-bottom: 0;
    padding: 1rem 1.5rem;
  }

  .card-soft .card-body {
    padding: 1.5rem 1.8rem 1.8rem;
  }

  /* Etiquetas de pregunta */
  .q-row {
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    background-color: var(--color-card);
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    border: 1px solid #e9ecef;
    transition: transform 0.3s ease, box-shadow 0.3s ease, background-color .3s;
  }

  .q-row:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 14px rgba(0,0,0,0.12);
    background-color: var(--color-blanco);
  }

  .q-label {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--color-texto-principal);
    line-height: 1.6;
    margin-bottom: .5rem;
    border-bottom: 2px solid rgba(0,0,0,0.08);
    padding-bottom: .35rem;
  }

  /* Radios en fila tipo “chips” */
  .icheck-inline {
    display: flex;
    flex-wrap: wrap;
    gap: .75rem;
    margin-top: .4rem;
  }

  .icheck-inline .icheck-primary,
  .icheck-inline .icheck-success,
  .icheck-inline .icheck-info,
  .icheck-inline .icheck-danger,
  .icheck-inline .icheck-warning {
    margin-right: 0;
  }

  .icheck-inline label {
    font-weight: 500;
  }

  .icheck-inline input[type="radio"] {
    transform: scale(1.1);
    cursor: pointer;
  }

  /* Botones de acciones en cada pregunta */
  .acciones {
    margin-top: 10px;
    display: flex;
    gap: 12px;
  }

  .acciones .btn-editar,
  .acciones .btn-eliminar {
    padding: 6px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .acciones .btn-editar {
    background-color: var(--color-gris);
    color: #fff;
  }

  .acciones .btn-editar:hover {
    background-color: var(--color-gris);
  }

  .acciones .btn-eliminar {
    background-color:var(--color-azul);
    color: #fff;
  }

  .acciones .btn-eliminar:hover {
    background-color: var(--color-azul);
  }

  /* Botones navegación */
  .btn-formulario-nav,
  .btn-enviar {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .btn-formulario-nav {
    background-color: var(--color-azul);
    color: white;
  }

  .btn-enviar {
    background-color: var(--color-azul);
    color: white;
  }

  .btn-formulario-nav:hover,
  .btn-enviar:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
  }

  @media (max-width: 768px) {
    .form-content {
      padding: 1rem;
    }

    .form-titulo {
      font-size: 2rem;
    }
  }

  /* ================= NO TOCAR: SECCIÓN CUERPO HUMANO ================= */

  .seleccion-cuerpo-box {
      padding: 1.5rem;
      border-radius: 12px;
      height: auto; /* Cambiado de 650px a auto */
      min-height: 600px; /* Altura mínima */
      transition: transform 0.3s, box-shadow 0.3s;
      margin-bottom: 2rem;
      background-color: white;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }

  .human-body,
  .human-body-viewer {
      width: 207px;
      position: relative;
      height: 550px; /* Altura fija para contener todo el cuerpo */
      display: block;
      margin: 40px auto;
      padding: 0; /* Eliminar padding-top */
  }

  .human-body svg:hover,
  .human-body-viewer svg:hover {
      cursor: pointer;
  }

  .human-body svg,
  .human-body-viewer svg {
      position: absolute;
      left: 50%;
      fill: #424242; /* Mismo color que en React */
  }

  .human-body svg#head,
  .human-body-viewer svg#head {
      margin-left: -28.297px; /* width: 56.594 / 2 */
      top: 0;
  }

  .human-body svg#left-shoulder,
  .human-body-viewer svg#left-shoulder {
      margin-left: -54.766px;
      top: 75px;
  }

  .human-body svg#right-shoulder,
  .human-body-viewer svg#right-shoulder {
      margin-left: 13.297px;
      top: 75px;
  }

  .human-body svg#left-arm,
  .human-body-viewer svg#left-arm {
      margin-left: -78.172px;
      top: 118px;
  }

  .human-body svg#right-arm,
  .human-body-viewer svg#right-arm {
      margin-left: 40px;
      top: 118px;
      z-index: 10001;
  }

  .human-body svg#chest,
  .human-body-viewer svg#chest {
      margin-left: -43.297px;
      top: 94px;
  }

  .human-body svg#stomach,
  .human-body-viewer svg#stomach {
      margin-left: -37.625px;
      top: 136px;
  }

  .human-body svg#left-leg,
  .human-body-viewer svg#left-leg {
      margin-left: -46.813px;
      top: 211px;
      z-index: 9999;
  }

  .human-body svg#right-leg,
  .human-body-viewer svg#right-leg {
      margin-left: 0;
      top: 211px;
      z-index: 9999;
  }

  .human-body svg#left-hand,
  .human-body-viewer svg#left-hand {
      margin-left: -105px;
      top: 230px;
  }

  .human-body svg#right-hand,
  .human-body-viewer svg#right-hand {
      margin-left: 70px;
      top: 230px;
      z-index: 10000;
  }

  .human-body svg#left-foot,
  .human-body-viewer svg#left-foot {
      margin-left: -35px;
      top: 461px;
  }

  .human-body svg#right-foot,
  .human-body-viewer svg#right-foot {
      margin-left: 0;
      top: 461px;
  }

  
  /* Estados de color */
  .estado-muybien{fill:#4afb4a !important;}
  .estado-bien{fill:#a9ffa9 !important;}
  .estado-normal{fill:#f3f39d !important;}
  .estado-mal{fill:#fd8697 !important;}
  .estado-muymal{fill:#ff3131 !important;}

  /* Popup de selección */
  .popup-selector{
    position:fixed; transform:translate(-50%,-100%); display:flex; gap:12px;
    background:#fff;padding:12px;border-radius:10px;box-shadow:0 3px 12px rgba(0,0,0,.3);
    z-index:99999; opacity:0; transform:translate(-50%,-80%) scale(.8);
    animation:popupShow .25s forwards;
  }
  @keyframes popupShow{to{opacity:1;transform:translate(-50%,-100%) scale(1);}}
  .popup-selector .option{
    width:30px;height:30px;border-radius:50%;cursor:pointer;
    display:flex;align-items:center;justify-content:center;
  }
  .option.muybien{background:#4afb4a;}
  .option.bien{background:#a9ffa9;}
  .option.normal{background:#f3f39d;}
  .option.mal{background:#fd8697;}
  .option.muymal{background:#ff3131;}


</style>

<section class="content">
  <div class="container-fluid">

    <div class="form-container">
      <div class="form-content">

        {{-- HEADER COMO EN REACT --}}
        <div class="form-header">
          <h1 class="form-titulo">Evaluación Post-Incendio</h1>
        </div>

        {{-- ===================== PASO 1: FÍSICO ===================== --}}
        <div id="step-fisico">
          <div class="card card-soft card-primary">
            <div class="card-header bg-primary text-white">
              <h3 class="card-title m-0">Evaluación Física</h3>
            </div>
            <div class="card-body">

              @php
                $opciones = ['Nunca','Raramente','A veces','Frecuentemente','Siempre'];
                $pregF = [
                  ['id'=>'f1','t'=>'¿Te sientes más cansado o agotado de lo habitual después de las intervenciones?'],
                  ['id'=>'f2','t'=>'¿Has notado quemaduras, irritación o enrojecimiento en la piel después de las intervenciones?'],
                  ['id'=>'f3','t'=>'¿Has tenido dificultades para respirar o tos después de las intervenciones?'],
                  ['id'=>'f4','t'=>'¿Tienes dolor o molestias en el pecho desde el incendio?'],
                  ['id'=>'f5','t'=>'¿Has experimentado palpitaciones o un ritmo cardíaco irregular después de la intervención?'],
                  ['id'=>'f6','t'=>'¿Tus ojos han estado irritados, con ardor o picazón desde la intervención?'],
                  ['id'=>'f7','t'=>'¿Tienes dificultad para respirar profundamente desde la intervención?'],
                  ['id'=>'f8','t'=>'¿Has notado que tu nariz está congestionada o bloqueada más de lo normal?'],
                ];
              @endphp

              <div id="preguntas-fisico">
                @foreach($pregF as $pf)
                  <div class="q-row" data-tipo="fisico">
                    <div class="q-label">{{ $pf['t'] }}</div>
                    <div class="icheck-inline">
                      @foreach($opciones as $idx => $opt)
                        @php $rid = $pf['id'].'_'.$idx; @endphp
                        <div class="icheck-success d-inline">
                          <input type="radio" id="{{ $rid }}" name="{{ $pf['id'] }}">
                          <label for="{{ $rid }}">{{ $opt }}</label>
                        </div>
                      @endforeach
                    </div>
                  </div>
                @endforeach
              </div>

              {{-- ============ NO TOCAR: SELECTOR DE CONDICIÓN DEL CUERPO ============ --}}
              {{-- ============ NO TOCAR: SELECTOR DE CONDICIÓN DEL CUERPO ============ --}}
<div class="seleccion-cuerpo-box">
<label>Selección de condición del cuerpo</label>
<div class="human-body" id="humanBody">

                  {{-- SVGs (exactos) --}}
<svg data-part="Cabeza" id="head" class="head" xmlns="http://www.w3.org/2000/svg" width="56.594" height="95.031" viewBox="0 0 56.594 95.031"><path d="M15.92 68.5l8.8 12.546 3.97 13.984-9.254-7.38-4.622-15.848zm27.1 0l-8.8 12.546-3.976 13.988 9.254-7.38 4.622-15.848zm6.11-27.775l.108-11.775-21.16-14.742L8.123 26.133 8.09 40.19l-3.24.215 1.462 9.732 5.208 1.81 2.36 11.63 9.72 11.018 10.856-.324 9.56-10.37 1.918-11.952 5.207-1.81 1.342-9.517zm-43.085-1.84l-.257-13.82L28.226 11.9l23.618 15.755-.216 10.37 4.976-17.085L42.556 2.376 25.49 0 10.803 3.673.002 24.415z"></path></svg>
<svg data-part="Hombro Izquierdo" id="left-shoulder" class="left-shoulder" xmlns="http://www.w3.org/2000/svg" width="109.532" height="46.594" viewBox="0 0 109.532 46.594"><path d="m 38.244,-0.004 1.98,9.232 -11.653,2.857 -7.474,-2.637 z M 17.005,10.536 12.962,8.35 0.306,22.35 0.244,27.675 c 0,0 16.52,-17.015 16.764,-17.14 z m 1.285,0.58 C 18.3,11.396 0.528,30.038 0.528,30.038 L -0.01,46.595 6.147,36.045 18.017,30.989 26.374,15.6 Z"></path></svg>
<svg data-part="Hombro Derecho" id="right-shoulder" class="right-shoulder" xmlns="http://www.w3.org/2000/svg" width="109.532" height="46.594" viewBox="0 0 109.532 46.594"><path d="m 3.2759972,-0.004 -1.98,9.232 11.6529998,2.857 7.473999,-2.637 z m 21.2379988,10.54 4.044,-2.187 12.656,14 0.07,5.33 c 0,0 -16.524,-17.019 -16.769,-17.144 z m -1.285,0.58 c -0.008,0.28 17.762,18.922 17.762,18.922 l 0.537,16.557 -6.157,-10.55 -11.871,-5.057 L 15.147997,15.6 Z"></path></svg>
<svg data-part="Brazo Izquierdo" id="left-arm" class="left-arm" xmlns="http://www.w3.org/2000/svg" width="156.344" height="119.25" viewBox="0 0 156.344 119.25"><path d="m21.12,56.5a1.678,1.678 0 0 1 -0.427,0.33l0.935,8.224l12.977,-13.89l1.2,-8.958a168.2,168.2 0 0 0 -14.685,14.294zm1.387,12.522l-18.07,48.91l5.757,1.333l19.125,-39.44l3.518,-22.047l-10.33,11.244zm-5.278,-18.96l2.638,18.74l-17.2,46.023l-2.657,-1.775l6.644,-35.518l10.575,-27.47zm18.805,-12.323a1.78,1.78 0 0 1 0.407,-0.24l3.666,-27.345l-7.037,-10.139l-7.258,10.58l-6.16,37.04l0.566,4.973a151.447,151.447 0 0 1 15.808,-14.87l0.008,0.001zm-13.742,-28.906l-3.3,35.276l-2.2,-26.238l5.5,-9.038z"></path></svg>
<svg data-part="Brazo Derecho" id="right-arm" class="right-arm" xmlns="http://www.w3.org/2000/svg" width="156.344" height="119.25" viewBox="0 0 156.344 119.25"><path d="m 18.997,56.5 a 1.678,1.678 0 0 0 0.427,0.33 L 18.489,65.054 5.512,51.164 4.312,42.206 A 168.2,168.2 0 0 1 18.997,56.5 Z m -1.387,12.522 18.07,48.91 -5.757,1.333 L 10.798,79.825 7.28,57.778 17.61,69.022 Z m 5.278,-18.96 -2.638,18.74 17.2,46.023 2.657,-1.775 L 33.463,77.532 22.888,50.062 Z M 4.083,37.739 A 1.78,1.78 0 0 0 3.676,37.499 L 0.01,10.154 7.047,0.015 l 7.258,10.58 6.16,37.04 -0.566,4.973 A 151.447,151.447 0 0 0 4.091,37.738 l -0.008,10e-4 z m 13.742,-28.906 3.3,35.276 2.2,-26.238 -5.5,-9.038 z"></path></svg>
<svg data-part="Torso" id="chest" class="chest" xmlns="http://www.w3.org/2000/svg" width="86.594" height="45.063" viewBox="0 0 86.594 45.063"><path d="M19.32 0l-9.225 16.488-10.1 5.056 6.15 4.836 4.832 14.07 11.2 4.616 17.85-8.828-4.452-34.7zm47.934 0l9.225 16.488 10.1 5.056-6.15 4.836-4.833 14.07-11.2 4.616-17.844-8.828 4.45-34.7z"></path></svg>
<svg data-part="Torso" id="stomach" class="stomach" xmlns="http://www.w3.org/2000/svg" width="75.25" height="107.594" viewBox="0 0 75.25 107.594"><path d="M19.25 7.49l16.6-7.5-.5 12.16-14.943 7.662zm-10.322 8.9l6.9 3.848-.8-9.116zm5.617-8.732L1.32 2.15 6.3 15.6zm-8.17 9.267l9.015 5.514 1.54 11.028-8.795-5.735zm15.53 5.89l.332 8.662 12.286-2.665.664-11.826zm14.61 84.783L33.28 76.062l-.08-20.53-11.654-5.736-1.32 37.5zM22.735 35.64L22.57 46.3l11.787 3.166.166-16.657zm-14.16-5.255L16.49 35.9l1.1 11.25-8.8-7.06zm8.79 22.74l-9.673-7.28-.84 9.78L-.006 68.29l10.564 14.594 5.5.883 1.98-20.735zM56 7.488l-16.6-7.5.5 12.16 14.942 7.66zm10.32 8.9l-6.9 3.847.8-9.116zm-5.617-8.733L73.93 2.148l-4.98 13.447zm8.17 9.267l-9.015 5.514-1.54 11.03 8.8-5.736zm-15.53 5.89l-.332 8.662-12.285-2.665-.664-11.827zm-14.61 84.783l3.234-31.536.082-20.532 11.65-5.735 1.32 37.5zm13.78-71.957l.166 10.66-11.786 3.168-.166-16.657zm14.16-5.256l-7.915 5.514-1.1 11.25 8.794-7.06zm-8.79 22.743l9.673-7.28.84 9.78 6.862 12.66-10.564 14.597-5.5.883-1.975-20.74z"></path></svg>
<svg data-part="Pierna Izquierda" id="left-leg" class="left-leg" xmlns="http://www.w3.org/2000/svg" width="93.626" height="250.625" viewBox="0 0 93.626 250.625"><path d="m 18.00179,139.99461 -0.664,5.99 4.647,5.77 1.55,9.1 3.1,1.33 2.655,-13.755 1.77,-4.88 -1.55,-3.107 z m 20.582,0.444 -3.32,9.318 -7.082,13.755 1.77,12.647 5.09,-14.2 4.205,-7.982 z m -26.557,-12.645 5.09,27.29 -3.32,-1.777 -2.656,8.875 z m 22.795,42.374 -1.55,4.88 -3.32,20.634 -0.442,27.51 4.65,26.847 -0.223,-34.39 4.87,-13.754 0.663,-15.087 z m -10.623,12.424 1.106,41.267 c 14.157565,64.57987 -5.846437,10.46082 -16.8199998,-29.07 l 5.5329998,-36.384 z m -9.71,-178.164003 0,22.476 15.71,31.073 9.923,30.850003 -1.033,-21.375 z m 25.49,30.248 0.118,-0.148 -0.793,-2.024 -16.545,-18.16 -1.242,-0.44 10.984,28.378 z m -6.255,10.766 6.812,17.6 2.274,-21.596 -1.344,-3.43 z m -26.4699998,17.82 0.827,25.340003 12.8159998,35.257 -3.928,10.136 -12.6099998,-44.51 z M 31.81879,76.04161 l 0.345,0.826 6.47,15.48 -4.177,38.342 -6.594,-3.526 5.715,-35.7 z m -21.465,-74.697003 0.827,21.373 L 4.1527902,65.02561 0.84679017,30.870607 Z m 2.068,27.323 14.677,32.391 3.307,26.000003 -6.2,36.58 -13.437,-37.241 -0.8269998,-38.342003 z"></path></svg>
<svg data-part="Pierna Derecha" id="right-leg" class="right-leg" xmlns="http://www.w3.org/2000/svg" width="80" height="250.625" viewBox="0 0 80 250.625"><path d="m 26.664979,139.7913 0.663,5.99 -4.647,5.77 -1.55,9.1 -3.1,1.33 -2.655,-13.755 -1.77,-4.88 1.55,-3.107 z m -20.5820002,0.444 3.3200005,9.318 7.0799997,13.755 -1.77,12.647 -5.0899997,-14.2 -4.2000005,-7.987 z m 3.7620005,29.73 1.5499997,4.88 3.32,20.633 0.442,27.51 -4.648,26.847 0.22,-34.39 -4.8670002,-13.754 -0.67,-15.087 z m 10.6229997,12.424 -1.107,41.267 -8.852,33.28 9.627,-4.55 16.046,-57.8 -5.533,-36.384 z m -13.9460002,74.991 c -5.157661,19.45233 -2.5788305,9.72616 0,0 z M 30.177979,4.225305 l 0,22.476 -15.713,31.072 -9.9230002,30.850005 1.033,-21.375005 z m -25.4930002,30.249 -0.118,-0.15 0.793,-2.023 16.5450002,-18.16 1.24,-0.44 -10.98,28.377 z m 6.2550002,10.764 -6.8120002,17.6 -2.274,-21.595 1.344,-3.43 z m 26.47,17.82 -0.827,25.342005 -12.816,35.25599 3.927,10.136 12.61,-44.50999 z m -24.565,12.783005 -0.346,0.825 -6.4700002,15.48 4.1780002,38.34199 6.594,-3.527 -5.715,-35.69999 z m 19.792,51.74999 -5.09,27.29 3.32,-1.776 2.655,8.875 z m 1.671,-126.452995 -0.826,21.375 7.03,42.308 3.306,-34.155 z m -2.066,27.325 -14.677,32.392 -3.308,26.000005 6.2,36.57999 13.436,-37.23999 0.827,-38.340005 z"></path></svg>
<svg data-part="Mano Izquierda" id="left-hand" class="left-hand" xmlns="http://www.w3.org/2000/svg" width="90" height="38.938" viewBox="0 0 90 38.938"><path d="m 21.255,-0.00198191 2.88,6.90000201 8.412,1.335 0.664,12.4579799 -4.427,17.8 -2.878,-0.22 2.8,-11.847 -2.99,-0.084 -4.676,12.6 -3.544,-0.446 4.4,-12.736 -3.072,-0.584 -5.978,13.543 -4.428,-0.445 6.088,-14.1 -2.1,-1.25 L 4.878,34.934 1.114,34.489 12.4,12.9 11.293,11.12 0.665,15.57 0,13.124 8.635,5.3380201 Z"></path></svg>
 
                <svg data-part="Mano Derecha" id="right-hand" class="right-hand" xmlns="http://www.w3.org/2000/svg" width="90" height="38.938" viewBox="0 0 90 38.938"><path d="m 13.793386,-0.00198533 -2.88,6.90000163 -8.4120002,1.335 -0.664,12.4579837 4.427,17.8 2.878,-0.22 -2.8,-11.847 2.99,-0.084 4.6760002,12.6 3.544,-0.446 -4.4,-12.736 3.072,-0.584 5.978,13.543 4.428,-0.445 -6.088,-14.1 2.1,-1.25 7.528,12.012 3.764,-0.445 -11.286,-21.589 1.107,-1.78 10.628,4.45 0.665,-2.447 -8.635,-7.7859837 z"></path></svg>
 
                <svg data-part="Pie Izquierdo" id="left-foot" class="left-foot" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30"><path d="m 19.558357,1.92821 c -22.1993328,20.55867 -11.0996668,10.27933 0,0 z m 5.975,5.989 -0.664,18.415 -1.55,6.435 -4.647,0 -1.327,-4.437 -1.55,-0.222 0.332,4.437 -5.864,-1.778 -1.5499998,-0.887 -6.64,-1.442 -0.22,-5.214 6.418,-10.87 4.4259998,-5.548 c 9.991542,-3.26362 9.41586,-8.41457 12.836,1.111 z"></path></svg>
 
                <svg data-part="Pie Derecho" id="right-foot" class="right-foot" xmlns="http://www.w3.org/2000/svg" width="90" height="38.938" viewBox="0 0 90 38.938"><path d="m 11.723492,2.35897 c -40.202667,20.558 -20.1013335,10.279 0,0 z m -5.9740005,5.989 0.663,18.415 1.546,6.435 4.6480005,0 1.328,-4.437 1.55,-0.222 -0.333,4.437 5.863,-1.778 1.55,-0.887 6.638,-1.442 0.222,-5.214 -6.418,-10.868 -4.426,-5.547 -10.8440005,-4.437 z"></path></svg>
</div>
</div>
 

              <div class="d-flex justify-content-end mt-3">
                <button id="btn-next" type="button" class="btn-formulario-nav">
                  Siguiente
                </button>
              </div>

            </div>
          </div>
        </div>

        {{-- ===================== PASO 2: PSICOLÓGICO ===================== --}}
        <div id="step-psico" class="step-hidden">
          <div class="card card-soft card-info">
            <div class="card-header bg-info text-white">
              <h3 class="card-title m-0">Evaluación Psicológica</h3>
            </div>
            <div class="card-body">

              @php
                $pregP = [
                  ['id'=>'p1','t'=>'¿Con qué frecuencia has tenido pensamientos no deseados relacionados al incendio?'],
                  ['id'=>'p2','t'=>'¿Sientes que últimamente piensas en qué pudiste hacer diferente durante la intervención?'],
                  ['id'=>'p3','t'=>'¿Has notado disminución de apetito desde la intervención?'],
                  ['id'=>'p4','t'=>'¿Te resulta difícil relajarte o desconectar mentalmente después de las intervenciones?'],
                  ['id'=>'p5','t'=>'¿Has tenido dificultades para concentrarte en tus tareas diarias debido al estrés?'],
                  ['id'=>'p6','t'=>'¿Has sufrido de insomnio recientemente?'],
                  ['id'=>'p7','t'=>'¿Te has sentido emocionalmente más inestable o irritable desde el incendio?'],
                  ['id'=>'p8','t'=>'¿Te sientes preocupado o ansioso constantemente desde el incendio?'],
                ];
              @endphp

              <div id="preguntas-psico">
                @foreach($pregP as $pp)
                  <div class="q-row" data-tipo="psicologico">
                    <div class="q-label">{{ $pp['t'] }}</div>
                    <div class="icheck-inline">
                      @foreach($opciones as $idx => $opt)
                        @php $rid = $pp['id'].'_'.$idx; @endphp
                        <div class="icheck-info d-inline">
                          <input type="radio" id="{{ $rid }}" name="{{ $pp['id'] }}">
                          <label for="{{ $rid }}">{{ $opt }}</label>
                        </div>
                      @endforeach
                    </div>
                  </div>
                @endforeach
              </div>

              <div class="d-flex justify-content-between mt-3">
                <button id="btn-prev" type="button" class="btn btn-default">
                  Anterior
                </button>
                <button id="btn-send" type="button" class="btn-enviar">
                  Enviar
                </button>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>

    {{-- ================= MODALES BOOTSTRAP / ADMINLTE ================= --}}

    {{-- Modal Agregar / Editar Pregunta --}}
    <div class="modal fade" id="modalPregunta" tabindex="-1" role="dialog" aria-labelledby="modalPreguntaLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalPreguntaLabel">Agregar Pregunta</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="modalTipo">Tipo</label>
              <select id="modalTipo" class="form-control">
                <option value="fisico">Físico</option>
                <option value="psicologico">Psicológico</option>
              </select>
            </div>

            <div class="form-group mt-3">
              <label for="modalTexto">
                Pregunta
                <span class="text-muted ml-2" style="font-size: .85em;">
                  (<span id="modalContador">0</span>/250)
                </span>
              </label>
              <textarea id="modalTexto" class="form-control" rows="3" maxlength="250" placeholder="Escriba la pregunta..."></textarea>
              <small id="modalAvisoLimite" class="form-text text-warning d-none">
                Has alcanzado el límite máximo de caracteres.
              </small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="modalGuardarBtn" disabled>Guardar</button>
          </div>
        </div>
      </div>
    </div>

    {{-- Modal Confirmar Eliminación --}}
    <div class="modal fade" id="modalEliminarPregunta" tabindex="-1" role="dialog" aria-labelledby="modalEliminarPreguntaLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalEliminarPreguntaLabel">Confirmar Eliminación</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            ¿Estás seguro que quieres eliminar esta pregunta?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-danger" id="modalEliminarConfirmBtn">Eliminar</button>
          </div>
        </div>
      </div>
    </div>

    {{-- Modal Error --}}
    <div class="modal fade" id="modalError" tabindex="-1" role="dialog" aria-labelledby="modalErrorLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="modalErrorLabel">Error</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            Debes responder todas las preguntas antes de enviar.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-dismiss="modal">Aceptar</button>
          </div>
        </div>
      </div>
    </div>

    {{-- Modal Éxito --}}
    <div class="modal fade" id="modalSuccess" tabindex="-1" role="dialog" aria-labelledby="modalSuccessLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-center">
          <div class="modal-header border-0">
            <h5 class="modal-title" id="modalSuccessLabel">¡Enviado con éxito!</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <h1 style="font-size: 70px; color: #00b4d8;">✔️</h1>
            <p>Su respuesta ha sido enviada exitosamente.</p>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', () => {
  /* ====== Navegación de pasos ====== */
  const stepFisico = document.getElementById('step-fisico');
  const stepPsico  = document.getElementById('step-psico');
  const stepRes    = document.getElementById('resultado-final');
  const nextBtn    = document.getElementById('btn-next');
  const prevBtn    = document.getElementById('btn-prev');
  const sendBtn    = document.getElementById('btn-send');
 
  nextBtn?.addEventListener('click', e => {
    e.preventDefault();
    stepFisico.classList.add('step-hidden');
    stepPsico.classList.remove('step-hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
  prevBtn?.addEventListener('click', e => {
    e.preventDefault();
    stepPsico.classList.add('step-hidden');
    stepFisico.classList.remove('step-hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
 
  /* ====== Popup selector para el cuerpo ====== */
  const valueByState = { muybien:5, bien:4, normal:3, mal:2, muymal:1 };
  const colorClassByState = {
    muybien:'estado-muybien', bien:'estado-bien', normal:'estado-normal', mal:'estado-mal', muymal:'estado-muymal'
  };
  // estado seleccionado por parte
  const bodyState = {
  'Cabeza': null,
  'Torso': null,
  'Brazo Izquierdo': null,
  'Brazo Derecho': null,
  'Pierna Izquierda': null,
  'Pierna Derecha': null,
  'Mano Izquierda': null,
  'Mano Derecha': null,
  'Pie Izquierdo': null,
  'Pie Derecho': null
};
 
  function closePopup(){ document.querySelectorAll('.popup-selector').forEach(n => n.remove()); }
  function applyStateToSVG(svg, state){
    // limpiar clases previas
    svg.classList.remove(...Object.values(colorClassByState));
    if(state && colorClassByState[state]) svg.classList.add(colorClassByState[state]);
  }
  function showPopup(x, y, svgEl, partName){
    closePopup();
    const popup = document.createElement('div');
    popup.className = 'popup-selector';
    popup.style.left = x+'px';
    popup.style.top  = y+'px';
 
    ['muybien','bien','normal','mal','muymal'].forEach(key=>{
      const opt = document.createElement('div');
      opt.className = 'option '+key;
      opt.title = key;
      opt.addEventListener('click', ()=>{
        bodyState[partName] = key;
        applyStateToSVG(svgEl, key);
        closePopup();
      });
      popup.appendChild(opt);
    });
 
    document.body.appendChild(popup);
    // cerrar al click fuera
    setTimeout(()=>{
      document.addEventListener('click', onOutside, { once:true });
    },0);
    function onOutside(e){
      if(!popup.contains(e.target)) closePopup();
    }
  }
 
  // Clicks en las partes válidas
  const humanBody = document.getElementById('humanBody');
  const torsoIds = new Set(['chest','stomach']);
  humanBody.querySelectorAll('svg').forEach(svg=>{
    svg.addEventListener('click', (e)=>{
      e.stopPropagation();
      const raw = svg.getAttribute('data-part');
      if(!raw) return;
      const partName = torsoIds.has(svg.id) ? 'Torso' : raw; // colapsar torso
      const rect = svg.getBoundingClientRect();
      showPopup(rect.left + rect.width/2, rect.top - 10, svg, partName);
    });
  });
 
  /* ====== Recolección y “análisis” simple de resultados ====== */
  const optionValue = { 'Nunca':1, 'Raramente':2, 'A veces':3, 'Frecuentemente':4, 'Siempre':5 };
 
  function collectSection(prefix){ // prefix: 'f' | 'p'
    const values = [];
    // asume f1..f8 / p1..p8
    for(let i=1;i<=8;i++){
      const name = `${prefix}${i}`;
      const checked = document.querySelector(`input[name="${name}"]:checked`);
      if(checked){ values.push(optionValue[checked.value] || 0); }
    }
    return values;
  }
 
  function summarizeBodyIssues(){
    const criticas = [];
    for(const [parte, estado] of Object.entries(bodyState)){
      if(estado === 'muymal' || estado === 'mal') criticas.push(parte.toLowerCase());
    }
    if(!criticas.length) return 'Sin molestias relevantes por zonas. Mantén hidratación y descanso.';
    return `Molestias en: ${criticas.join(', ')}. Recomendamos reposo, hidratación y vigilancia de síntomas.`;
  }
 
  function textForAverage(avg, area){
    if(avg >= 4.2) return area==='fisico'
      ? 'No se observan signos físicos relevantes. Continúa con descanso, hidratación y control básico.'
      : 'Estado emocional estable. Mantén tus rutinas de descanso y desconexión.';
    if(avg >= 3) return area==='fisico'
      ? 'Molestias leves-moderadas. Considera una evaluación breve y reduce cargas en próximas guardias.'
      : 'Estrés moderado. Practica respiración, pausas activas y contacto con tu red de apoyo.';
    return area==='fisico'
      ? 'Síntomas físicos elevados. Sugerimos evaluación médica y pausa operacional.'
      : 'Indicadores emocionales altos. Recomendamos psicoeducación y apoyo especializado.';
  }
 
  function nl(p){ return `<p>${p}</p>`; }
 
  sendBtn?.addEventListener('click', ()=>{
    // Recolectar respuestas
    const fis = collectSection('f');
    const psi = collectSection('p');
 
    // Promedios (evitamos validar aquí; es pura maquetación con lógica base)
    const avgFis = fis.length ? (fis.reduce((a,b)=>a+b,0)/fis.length) : 0;
    const avgPsi = psi.length ? (psi.reduce((a,b)=>a+b,0)/psi.length) : 0;
 
    const bodyVals = Object.values(bodyState).filter(Boolean).map(s=>valueByState[s]);
    const avgBody = bodyVals.length ? (bodyVals.reduce((a,b)=>a+b,0)/bodyVals.length) : 0;
 
    // Texto final
    const fisicoTexto = [
      textForAverage((avgFis*0.7 + avgBody*0.3), 'fisico'),
      summarizeBodyIssues()
    ].map(nl).join('');
 
    const psicoTexto  = nl(textForAverage(avgPsi, 'emocional'));
 
    document.getElementById('texto-fisico').innerHTML = fisicoTexto;
    document.getElementById('texto-psico').innerHTML  = psicoTexto;
 
    // Mostrar vista de resultados
    stepFisico.classList.add('step-hidden');
    stepPsico.classList.add('step-hidden');
    stepRes.classList.remove('step-hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
});
</script>
@endsection