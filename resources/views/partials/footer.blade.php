<footer class="site-footer">

    <div class="container">

        <div class="row g-4">

            {{-- Brand --}}
            <div class="col-lg-5">

                <a href="{{route('home')}}" class="footer-brand">

                    <span class="footer-brand-icon">
                        🍱
                    </span>

                    <span>
                        KEDAI BANGJO
                    </span>
                </a>

                <p class="footer-description">
                    Layanan catering untuk kebutuhan rumah,
                    kantor, acara, dan berbagai kebutuhan lainnya.
                </p>

            </div>

            {{-- NAVIGASI --}}
            <div class="col-6 col-lg-3">

                <h6 class="footer-title">
                    Navigasi
                </h6>

                <ul class="footer-links">

                    <li>
                        <a href="{{ route('home') }}">
                            Home
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('customer.packages.index') }}">
                            Menu
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('cart.index') }}">
                            Keranjang
                        </a>
                    </li>

                    <li>
                        <a href="{{route('customer.orders.index')}}">
                            Pesanan Saya    
                        </a>                        
                    </li>

                    <li>
                        <a href="{{route('about')}}">
                            Tentang
                        </a>
                    </li>

                    <li>
                        <a href="{{route('contact')}}">
                            Kontak
                        </a>
                    </li>

                </ul>

            </div>


            {{-- INFORMASI --}}
            <div class="col-6 col-lg-4">

                <h6 class="footer-title">
                    Hubungi Kami
                </h6>

                <ul class="footer-contact">

                    <li>
                        <i class="bi bi-telephone"></i>
                        <span>08xxxxxxxxxx</span>
                    </li>

                    <li>
                        <i class="bi bi-envelope"></i>
                        <span>email@tavicatering.com</span>
                    </li>

                    <li>
                        <i class="bi bi-geo-alt"></i>
                        <span>Malang, Jawa Timur</span>
                    </li>

                </ul>

            </div>

        </div>


        <hr class="footer-divider">


        <div class="footer-bottom">

            <p>
                © {{ date('Y') }} Tavi Catering.
                All rights reserved.
            </p>

            <div class="footer-social">

                <a href="#">
                    <i class="bi bi-instagram"></i>
                </a>

                <a href="#">
                    <i class="bi bi-whatsapp"></i>
                </a>

            </div>

        </div>
        
    </div>

</footer>