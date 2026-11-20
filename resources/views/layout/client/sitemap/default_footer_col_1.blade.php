<a class="nav-link dropdown-toggle" href="#">{{ \Helper::getNameOftext('quick-links', $languageId) }}</a>
<ul>
    <li><a class="dropdown-item"
            href="{{ $abs_site_url . $subdomain . '/staffs' }}">{{ \Helper::getNameOftext('staffs', $languageId) }}</a>
    </li>
    <li><a class="dropdown-item"
            href="{{ $abs_site_url . $subdomain }}/news">{{ \Helper::getNameOftext('news', $languageId) }}</a>
    </li>
    <li><a class="dropdown-item"
            href="{{ $abs_site_url . $subdomain }}/notices">{{ \Helper::getNameOftext('notices', $languageId) }}</a>
    </li>
    <li><a class="dropdown-item"
            href="{{ $abs_site_url . $subdomain }}/publications">{{ \Helper::getNameOftext('publications', $languageId) }}</a>
    </li>
    <li><a class="dropdown-item"
            href="{{ $abs_site_url . $subdomain }}/circulars">{{ \Helper::getNameOftext('circulars', $languageId) }}</a>
    </li>
    <li><a class="dropdown-item"
            href="{{ $abs_site_url . $subdomain }}/downloads">{{ \Helper::getNameOftext('downloads', $languageId) }}</a>
    </li>
    <li><a class="dropdown-item"
            href="{{ $abs_site_url . $subdomain . '/projects' }}">{{ \Helper::getNameOftext('projects', $languageId) }}</a>
    </li>
    <li><a class="dropdown-item"
            href="{{ $abs_site_url . $subdomain . '/vehicles' }}">{{ \Helper::getNameOftext('vehicles', $languageId) }}</a>
    </li>
    <li><a class="dropdown-item"
            href="{{ $abs_site_url . $subdomain }}/gallery">{{ \Helper::getNameOftext('gallery', $languageId) }}</a>
    </li>
</ul>
