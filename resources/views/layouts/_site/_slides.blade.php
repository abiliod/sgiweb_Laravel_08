<!--
<div class="slider s12">
	<ul class="slides">
		<li>
			<img src="{{ asset('img/foto_slide_1.jpg') }}" alt="Imagem">
			<div class="caption center-align">
				<h3>Titulo da Imagem</h3>
				<h5>Descrição do Slide</h5>
			</div>
		</li>
		<li>
			<img src="{{ asset('img/foto_slide_2.jpg') }}" alt="Imagem">
			<div class="caption left-align">
				<h3>Titulo da Imagem</h3>
				<h5>Descrição do Slide</h5>
			</div>
		</li>
		<li>
			<img src="{{ asset('img/foto_slide_3.jpg') }}" alt="Imagem">
			<div class="caption rigth-align">
				<h3>Titulo da Imagem</h3>
				<h5>Descrição do Slide</h5>
			</div>
		</li>	

		<li>
			<img src="{{ asset('img/foto_slide_4.jpg') }}" alt="Imagem">
			<div class="caption left-align">
				<h3>Titulo da Imagem</h3>
				<h5>Descrição do Slide</h5>
			</div>
		</li>	

		<li>
			<img src="{{ asset('img/foto_slide_5.jpg') }}" alt="Imagem">
			<div class="caption center-align">
				<h3>Titulo da Imagem</h3>
				<h5>Descrição do Slide</h5>
			</div>
		</li>								
	</ul>
</div>
------>

{{--27/02/2020 Blade Service Injection:

No recurso de Service Injection do Blade.
A diretiva @inject recebe dois argumentos,
o primeiro é um nome da variável, e o segundo
é o service container, que é uma classe que será 
instânciada e atribuída na variável do primeiro argumento.
$resources é um objeto da classe ResourcesService.
Crie uma classe SlidesService com os respectivos metodos
Monte o foreach na sua wiew.
--}}

@inject('resources', 'App\Services\SlidesService')   
<div class="slider">
	<ul class="slides">
	
	@foreach($resources->slides() as $slide)	
		<li onclick="window.location='{{ $slide->link }}'">
			<img src="{{ asset($slide->imagem) }}" alt="Imagem">
			<div class="caption {{ $resources->direcaoImagem() }}">
				<h3>{{ $slide->titulo }}</h3>
				<h5>{{ $slide->descricao }}</h5>
				@if($slide->link != null)
					<a href="{{$slide->link }}" class="btn btn-large blue">mais...</a>
				@endif
			</div>
		</li>
	@endforeach	
	</ul>
</div>



