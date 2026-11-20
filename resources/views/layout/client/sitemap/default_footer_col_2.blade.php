@php
use App\Models\Content;
@endphp
<a class="nav-link dropdown-toggle" href="#">{{ \Helper::getNameOftext('useful-links', $languageId) }}</a>
<ul>
    <li><a
            href="{{ $abs_site_url . $subdomain . '/tenders' }}">{{ \Helper::getNameOftext('tenders', $languageId) }}</a>
    </li>
    <li><a href="{{ $abs_site_url . $subdomain }}/laws">{{ \Helper::getNameOftext('laws', $languageId) }}</a>
    </li>
    <li><a href="{{ $abs_site_url . $subdomain }}/faqs">{{ \Helper::getNameOftext('faqs', $languageId) }}</a>
    </li>
    <li><a href="{{ $abs_site_url . $subdomain . '/page/links' }}" class="animated fadeIn animation-delay-9 custom">
            {{ \Helper::getNameOftext('links', $languageId) }}</a></li>
    <li><a href="{{ $abs_site_url . 'home' }}/login-credentials-request">Login
            Credentials</a>
    </li>

    <?php
    $footerContents = Content::getContentsPostionWise($languageId, $subdomain, 2);
    foreach ($footerContents as $content) {
        if ($content->content_url == 'login-credentials') {
            continue;
        } else {
            echo '<li><a href="' . $abs_site_url . $subdomain . '/page/' . $content->content_url . '">' . $content->content_heading . '</a></li>';
        }
    }
    ?>
</ul>
