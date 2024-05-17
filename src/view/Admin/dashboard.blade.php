@extends(Baseview('app'))
<!-- content.dashboard -->
@php
$widgets['before_content'][] = [
	        'type'        => 'progress',
            'style'         =>'primary',//primary, secondary , success ,danger ,warning ,info ,light ,dark 
            'header'=>'The Show Header',
            'hint'=>'hint',
            'footer_link'=>'footer_link',
            'footer_text'=>'footer_text',
            'content'=>[
                [
                            'min'=>0,
                            'max'=>100,
                            'val'=>10,
                            'description'=>'description',
                        ],
                        [
                            'min'=>0,
                            'max'=>100,
                            'val'=>10,
                            'description'=>'description',
                        ],
                ],
            'value'         =>50,
            'description'       =>'description',
            'progress'  =>50,
            
	];

@endphp
@Once('jumbotronstyle')
	@push('before_styles')
	<style>
		.jumbotron {
	padding: 4rem 2rem;
	margin-bottom: 2rem;
	background-color: #e9ecef !important;
	border-radius: .3rem;
    color:#000;
        }
	</style>
	@endpush
@endOnce

@section('content')