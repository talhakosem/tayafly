@extends('layouts.frontend')

@section('title', 'About Us')

@section('meta_description', 'Agrolidya tarafından yıllık 1 milyon fidan üretimi kapasitesi ile sertifikalı ceviz fidanı, badem fidanı, zeytin fidanı ve meyve fidanı yetiştiriliyor.')

@section('content')
<!--Breadcrumb start-->
<div class="container py-4">
    <div class="row">
        <div class="col-12 py-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">About Us</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<!--Breadcrumb end-->

<!--About start-->
<section class="py-lg-8 py-6">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="mb-6">
                    <h1 class="mb-4">About Us</h1>
                </div>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-6">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-4">
                                    <p class="lead">
                                        Agrolidya tarafından yıllık 1 milyon fidan üretimi kapasitesi ile sertifikalı ceviz fidanı, badem fidanı, zeytin fidanı, antepfıstığı fidanı ve meyve fidanı anaçları başta olmak üzere birçok meyve fidanı ve tıbbi aromatik meyve fidanı yetiştiriliyor. Agrolidya'nın satışını yaptığı sertifikalı meyve fidanları yüksek verim garantisiyle çiftçilerin yüzünü güldürüyor.
                                    </p>
                                </div>
                                
                                <div class="mb-4">
                                    <p>
                                        Agrolidya sektördeki yarım asırlık tecrübesini çağın gereksinimlerine de uyarak dijital platformlardan sizlere ulaştırıyor. Firma www.fidanlik.com.tr isimli e-ticaret mağazasıyla satışını yaptığı sertifikalı fidan çeşitleri; bereketli mahsulleriyle ülkemizin hatta dünyanın dört bir yanında toprakla buluşuyor.
                                    </p>
                                </div>
                                
                                <div class="mb-4">
                                    <p>
                                        Fidan alışverişlerinizde gerekli sertifikalara sahip, ilgili kurumlarca denetlenen, güvenilir firmaları tercih edilmesi konusunda çiftçileri uyarıyor. Aksi takdirde onlarca yıllık emekleriniz boşa gidebilir. Yıllarınızı vererek; özenle yetiştireceğiniz meyve fidanları için sizler de www.fidanlik.com.tr 'den güvenle alışveriş yapabilirsiniz. Sertifikalı ceviz fidanı, sertifikalı badem fidanı, sertifikalı zeytin fidanı, meyve fidanı anaçları ve birçok fidan çeşidi www.fidanlik.com.tr 'de en uygun fiyat seçenekleriyle sizleri bekliyor.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--About end-->
@endsection

